<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginOtpMail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // BRUTE FORCE PROTECTION: Define unique throttle keys based on IP and Username
        $throttleKeyShort = Str::transliterate(Str::lower($request->username).'|'.$request->ip().'|short');
        $throttleKeyLong = Str::transliterate(Str::lower($request->username).'|'.$request->ip().'|long');

        // Check 24-Hour Lock (8 attempts)
        if (RateLimiter::tooManyAttempts($throttleKeyLong, 8)) {
            return back()->withErrors(['username' => 'SECURITY LOCKOUT: Maximum attempts reached. Account locked for 24 hours.']);
        }

        // Check 1-Minute Lock (3 attempts)
        if (RateLimiter::tooManyAttempts($throttleKeyShort, 3)) {
            $seconds = RateLimiter::availableIn($throttleKeyShort);
            return back()->withErrors(['username' => "Too many failed attempts. Please wait {$seconds} seconds."]);
        }

        // Find the user by username or email
        $user = \App\Models\User::where('username', $request->username)
            ->orWhere('email', $request->username)->first();

        // Failed Login Trigger: Add to the rate limiter penalties
        if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKeyShort, 60); // 1 minute penalty
            RateLimiter::hit($throttleKeyLong, 86400); // 24 hour penalty
            return back()->withErrors(['username' => 'Account not found or incorrect password.']);
        }

        // Successful Login: Clear the penalties!
        RateLimiter::clear($throttleKeyShort);
        RateLimiter::clear($throttleKeyLong);

        // ONLY apply Email OTP to Customers
        if ($user->role === 'customer') {
            $otpCode = sprintf("%06d", mt_rand(1, 999999));
            
            // BYPASS MASS ASSIGNMENT SECURITY
            $user->email_otp = $otpCode;
            $user->email_otp_expiry = now()->addMinutes(10);
            $user->save();

            try {
                Mail::to($user->email)->send(new LoginOtpMail($otpCode));
            } catch (\Exception $e) {
                return back()->withErrors(['username' => 'Server Error: Could not send OTP email.']);
            }

            // Force save session data
            $request->session()->put('mfa_user_id', $user->id);
            $request->session()->save();

            return Inertia::render('Auth/Login', [
                'requires_mfa' => true,
                'masked_email' => $this->maskEmail($user->email)
            ]);
        }

        // STAFF BYPASS
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        
        $redirectUrl = route('dashboard');
        if ($user->role === 'hq') $redirectUrl = route('hq.dashboard');
        if ($user->role === 'manager') $redirectUrl = route('branch.dashboard');

        return redirect($redirectUrl);
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    private function maskEmail($email) {
        $parts = explode('@', $email);
        if(count($parts) !== 2) return $email;
        $name = $parts[0];
        $domain = $parts[1];
        $maskedName = substr($name, 0, 2) . str_repeat('*', max(strlen($name) - 2, 2));
        return $maskedName . '@' . $domain;
    }
}
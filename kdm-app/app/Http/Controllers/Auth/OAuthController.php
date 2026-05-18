<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    // 1. Sends the user to Google's website to log in
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // 2. Google sends them back here with their info
public function handleGoogleCallback()
    {
        try {
            $googleUser = \Laravel\Socialite\Facades\Socialite::driver('google')->stateless()->user();
            
            // Look for the user by their Google Email
            $user = \App\Models\User::where('email', $googleUser->email)->first();

            // 1. Block if they haven't registered manually
            if (!$user) {
                // CHANGED TO oauth_error
                return redirect()->route('login')->withErrors([
                    'oauth_error' => 'Access Denied: No KDM account found for this Google email. Please register manually first.'
                ]);
            }

            // 2. Block if they haven't verified their email yet
            if (!$user->hasVerifiedEmail()) {
                // CHANGED TO oauth_error
                return redirect()->route('login')->withErrors([
                    'oauth_error' => 'Access Denied: Please verify your email address before using Google Sign-In.'
                ]);
            }

            // 3. Link their Google ID if it isn't linked yet
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->id]);
            }

            // 4. Fully Log them in (Bypasses the Email OTP)
            \Illuminate\Support\Facades\Auth::login($user);
            
            $role = \Illuminate\Support\Facades\Auth::user()->role;
            if ($role === 'hq') return redirect()->route('hq.dashboard');
            if ($role === 'manager') return redirect()->route('branch.dashboard');
            
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            // CHANGED TO oauth_error
            return redirect()->route('login')->withErrors([
                'oauth_error' => 'Google Authentication Failed or was Cancelled.'
            ]);
        }
    }
}
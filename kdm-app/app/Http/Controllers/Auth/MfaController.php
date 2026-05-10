<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MfaController extends Controller
{
public function verifyLogin(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $userId = $request->session()->get('mfa_user_id');
        
        if (!$userId) {
            return back()->withErrors(['code' => 'Session expired. Please log in again.']);
        }

        $user = \App\Models\User::find($userId);

        // Strict string check and manual date check
        if ($user && (string)$user->email_otp === (string)$request->code && now()->isBefore($user->email_otp_expiry)) {
            
            // Clear the DB to prevent reuse
            $user->email_otp = null;
            $user->email_otp_expiry = null;
            $user->save();
            
            \Illuminate\Support\Facades\Auth::login($user);
            $request->session()->forget('mfa_user_id');
            $request->session()->regenerate();

            $role = $user->role;
            $redirectUrl = route('dashboard');
            if ($role === 'hq') $redirectUrl = route('hq.dashboard');
            if ($role === 'manager') $redirectUrl = route('branch.dashboard');

            return redirect($redirectUrl);
        }

        return back()->withErrors(['code' => 'Invalid or expired verification code.']);
    }
}
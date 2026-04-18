<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- This fixes the red line!
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Check Admins Table (Plain-text bypass)
        $admin = \App\Models\Admin::where('username', $request->username)
                                  ->where('password', $request->password)
                                  ->first();

        if ($admin) {
            // Log them in using the custom admin guard
            Auth::guard('admin')->login($admin);
            $request->session()->regenerate();

            // Route based on specific role
            if ($admin->role === 'hq') {
                return redirect()->intended(route('hq.dashboard', absolute: false));
            }
            return redirect()->intended(route('branch.dashboard', absolute: false));
        }

        // 2. Check Users Table (Standard Hashed Customer Login)
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Log out both guards just to be safe
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
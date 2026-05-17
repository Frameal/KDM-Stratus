<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:'.User::class,
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'contact_number' => 'required|string|max:15',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'agreed' => 'accepted', // Validates the privacy checkbox
        ]);

        // Generate a 10-character alphanumeric recovery code (e.g., KDM-A8B9C2)
        $plainRecoveryCode = 'KDM-' . strtoupper(\Illuminate\Support\Str::random(8));

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'recovery_code' => Hash::make($plainRecoveryCode), // Securely hash it in DB
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Save the plain text code to the session just once so we can show it to the user
        $request->session()->put('recovery_code_plain', $plainRecoveryCode);

        // Divert to the recovery code screen
        return redirect()->route('register.recovery');
    }
}

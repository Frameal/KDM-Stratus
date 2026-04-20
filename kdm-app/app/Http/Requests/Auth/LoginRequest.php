<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = $this->input('username');
        $password = $this->input('password');

        // Detect if they typed an email format or a standard username
        $loginField = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // STEP 1: Does the username/email exist in the database?
        $user = \App\Models\User::where($loginField, $login)->first();

        if (! $user) {
            \Illuminate\Support\Facades\RateLimiter::hit($this->throttleKey());
            throw \Illuminate\Validation\ValidationException::withMessages([
                'username' => 'We could not find an account with that ' . $loginField . '.',
            ]);
        }

        // STEP 2: Does the password match the database hash?
        if (! \Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            \Illuminate\Support\Facades\RateLimiter::hit($this->throttleKey());
            throw \Illuminate\Validation\ValidationException::withMessages([
                'password' => 'The password you entered is incorrect.',
            ]);
        }

        // STEP 3: If both pass, log the user in!
        \Illuminate\Support\Facades\Auth::login($user, $this->boolean('remember'));
        \Illuminate\Support\Facades\RateLimiter::clear($this->throttleKey());
    }
    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}

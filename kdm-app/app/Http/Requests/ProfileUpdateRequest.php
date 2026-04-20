<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique(\App\Models\User::class)->ignore($this->user()->id)],
            'email' => ['required', 'string', 'lowercase', 'email:rfc,dns', 'max:255', \Illuminate\Validation\Rule::unique(\App\Models\User::class)->ignore($this->user()->id)],
            'contact_number' => ['required', 'string', 'regex:/^\+639\d{9}$/'],
            'dob' => ['required', 'date'],
        ];
    }
}

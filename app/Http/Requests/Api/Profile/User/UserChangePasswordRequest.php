<?php

namespace App\Http\Requests\Api\Profile\User;

use App\Http\Requests\Api\ApiFormRequest;

class UserChangePasswordRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true; // already guarded by auth:sanctum
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            
            'password'         => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required'  => 'Please provide your current password.',
            'current_password.current_password' => 'The current password is incorrect.',
            'password.required'          => 'Please provide a new password.',
            'password.min'               => 'The new password must be at least :min characters.',
            'password.confirmed'         => 'The password confirmation does not match.',
            'password.different'         => 'The new password must be different from the current password.',
        ];
    }
}

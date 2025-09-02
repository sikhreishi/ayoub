<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;

class UserLoginRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'phone' => 'required|numeric',
            'password' => 'required|string',
            'token' => 'required|string',
            'platform' => 'required|string', // Ensure platform is provided
        ];
    }

    public function messages()
    {
        return [
            'phone.required' => 'Phone number is required.',
            'password.required' => 'Password is required.',
            'token.required' => 'Token is required.',
            'platform.required' => 'Platform is required.',
        ];
    }
}

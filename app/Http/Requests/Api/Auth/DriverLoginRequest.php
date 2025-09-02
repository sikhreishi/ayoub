<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;

class DriverLoginRequest extends ApiFormRequest
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
            'phone' => 'required|string',
            'password' => 'required|string',
            'token' => 'required|string',
            'platform' => 'required|string',
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

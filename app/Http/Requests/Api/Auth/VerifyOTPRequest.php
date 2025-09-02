<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;

class VerifyOTPRequest extends ApiFormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'phone' => 'required|numeric',
            'otp' => 'required|string|size:6',
            'token' => 'required|string',
            'platform' => 'required|string',
        ];
    }


    public function messages()
    {
        return [
            'phone.required' => 'Phone number is required.',
            'otp.required' => 'OTP is required.',
            'otp.size' => 'OTP must be exactly 6 characters.',
            'token.required' => 'Device token is required.',
            'platform.required' => 'Platform is required.',
        ];
    }
}

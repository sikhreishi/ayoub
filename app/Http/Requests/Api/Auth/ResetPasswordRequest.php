<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;

class ResetPasswordRequest extends ApiFormRequest
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
        'password' => 'required|confirmed|min:8',
        'otp' => 'required|string|size:6',
        'token' => 'nullable|string',
        'platform' => 'nullable|string|in:ios,android,web',
    ];
}

public function messages()
{
    return [
        'password.required' => 'Password is required.',
        'password.confirmed' => 'Password confirmation does not match.',
        'password.min' => 'Password must be at least 8 characters.',
        
    ];
}
}
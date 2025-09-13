<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;

class UserRegisterRequest extends ApiFormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email',
            'phone' => 'required|numeric|unique:users,phone',
            'password' => 'required|confirmed|min:8',
            'street' => 'nullable|string|max:255',
            'city_id' => 'nullable|exists:cities,id',
            'district_id' => 'nullable|exists:districts,id',
            'country_id' => 'nullable|exists:countries,id',
            'token' => 'required|string',
            'platform' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'Phone number must be unique.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'token.required' => 'Token is required.',
            'platform.required' => 'Platform is required.',
        ];
    }
}

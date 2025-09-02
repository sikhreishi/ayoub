<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;

class DriverRegisterRequest extends ApiFormRequest
{
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
            // 'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            // 'driver_profile' => 'array',
            // 'driver_profile.id_card_front' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            // 'driver_profile.id_card_back' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            // 'driver_profile.license_front' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            // 'driver_profile.license_back' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            // 'driver_profile.vehicle_license_front' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            // 'driver_profile.vehicle_license_back' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            // 'driver_profile.interior_front_seats' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            // 'driver_profile.interior_back_seats' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            // 'driver_profile.exterior_back_side' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            // 'driver_profile.exterior_front_side' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            // 'vehicle' => 'array',
            // 'vehicle.vehicle_type_id' => 'required|exists:vehicle_types,id',
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
            'token.required' => 'Device token is required.',
            'platform.required' => 'Platform is required.',

        ];
    }


}

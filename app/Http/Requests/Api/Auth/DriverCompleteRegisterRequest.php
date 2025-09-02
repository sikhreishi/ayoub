<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class DriverCompleteRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'driver_profile' => 'array',
            'driver_profile.id_card_front' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'driver_profile.id_card_back' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'driver_profile.license_front' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'driver_profile.license_back' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'driver_profile.vehicle_license_front' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'driver_profile.vehicle_license_back' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'driver_profile.interior_front_seats' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'driver_profile.interior_back_seats' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'driver_profile.exterior_back_side' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'driver_profile.exterior_front_side' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'vehicle' => 'array',
            'vehicle.vehicle_type_id' => 'required|exists:vehicle_types,id',
        ];
    }
}

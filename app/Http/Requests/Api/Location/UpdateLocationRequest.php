<?php

namespace App\Http\Requests\Api\Location;

use App\Http\Requests\Api\ApiFormRequest;

class UpdateLocationRequest extends ApiFormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.required'  => 'Latitude is required.',
            'latitude.numeric'   => 'Latitude must be a number.',
            'latitude.between'   => 'Latitude must be between -90 and 90 degrees.',

            'longitude.required' => 'Longitude is required.',
            'longitude.numeric'  => 'Longitude must be a number.',
            'longitude.between'  => 'Longitude must be between -180 and 180 degrees.',
        ];
    }

}

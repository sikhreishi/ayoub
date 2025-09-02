<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'id' => $this->id,
            'name' => $this->user ? $this->user->name : 'N/A',  // Check if user exists
            'avatar' => $this->user ? $this->user->avatar : null,  // Check if user exists
            'phone' => $this->user ? $this->user->phone : 'N/A',  // Check if user exists
            'rating' => $this->rating,
            'vehicle' => $this->vehicle ? [  // Check if vehicle exists
                'make' => $this->vehicle->make,
                'model' => $this->vehicle->model,
                'year' => $this->vehicle->year,
                'color' => $this->vehicle->color,
                'license_plate' => $this->vehicle->license_plate,
            ] : null,  // Return null if vehicle does not exist
        ];
    }
}

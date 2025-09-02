<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\DriverProfileResource;


class TripResource extends JsonResource
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
        'user_id' => $this->user_id,
        'driver_id' => $this->driver_id,
        'pickup' => [
            'latitude' => $this->pickup_lat,
            'longitude' => $this->pickup_lng,
            'address' => $this->pickup_name,
        ],
        'dropoff' => [
            'latitude' => $this->dropoff_lat,
            'longitude' => $this->dropoff_lng,
            'address' => $this->dropoff_name,
        ],
        'status' => $this->status,
        'estimated_fare' => $this->estimated_fare,
        'final_fare' => $this->final_fare,
        'payment_status' => $this->payment_status,
        'driver' => [
            'id' => $this->driver ? $this->driver->id : null,
            'name' => $this->driver ? $this->driver->name : null,
            'phone' => $this->driver ? $this->driver->phone : null,
            'avatar' => $this->driver && $this->driver->avatar ? asset("storage/" . $this->driver->avatar) : null,
            'driver_profile' => [
                'rating' => $this->driver && $this->driver->driverProfile ? $this->driver->driverProfile->rating : null,
                'vehicle' => $this->driver && $this->driver->driverProfile && $this->driver->driverProfile->vehicle ? [
                    'id' => $this->driver->driverProfile->vehicle->id,
                    'make' => $this->driver->driverProfile->vehicle->make,
                    'model' => $this->driver->driverProfile->vehicle->model,
                    'color' => $this->driver->driverProfile->vehicle->color,
                    'license_plate' => $this->driver->driverProfile->vehicle->license_plate,
                ] : null,
            ],
        ],
    ];
}

}

<?php

namespace App\Models\Vehicle;

use Illuminate\Database\Eloquent\Model;
use App\Models\DriverProfile;
use App\Models\Vehicle\VehicleType;

class Vehicle extends Model
{
  protected $fillable = [
        'driver_profile_id',
        'vehicle_type_id',
        'make',
        'model',
        'year',
        'color',
        'license_plate',
        'seats',
        'image_url',
    ];

    public function driverProfile()
    {
        return $this->belongsTo(DriverProfile::class);
    }


    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }
}

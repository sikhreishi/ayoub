<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vehicle\Vehicle;

class DriverProfile extends Model
{
    protected $fillable = [
        'user_id',
        'vehicle_id',  // Foreign key to Vehicle model
        'id_card_front',  // Front side of ID image
        'id_card_back',   // Back side of ID image
        'license_front',  // Front side of Driver's License image
        'license_back',   // Back side of Driver's License image
        'vehicle_license_front',  // Front side of Vehicle License image
        'vehicle_license_back',   // Back side of Vehicle License image
        'interior_front_seats',
        'interior_back_seats',
        'exterior_front_side',
        'exterior_back_side',
        'is_driver_verified',
        'registration_complete',
        'verification_note',
        'vehicle_info',
        'rating',  // New rating field
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function driver()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

}

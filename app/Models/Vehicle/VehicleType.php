<?php

namespace App\Models\Vehicle;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'start_fare',
        'day_per_km_rate',
        'night_per_km_rate',
        'day_per_minute_rate',
        'night_per_minute_rate',
        'commission_percentage',
        'is_active',
        'icon_url'
    ];


    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}

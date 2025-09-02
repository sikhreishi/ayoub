<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripDetail extends Model
{
      protected $fillable = [
        'trip_id',
        'distance_meters',
        'distance_km',
        'distance_text',
        'duration_seconds',
        'duration_min',
        'duration_text',
        'user_note',
        'driver_note',
        'user_rating',
        'driver_rating',
    ];

      protected function casts(): array
    {
        return [
        'distance_km' => 'decimal:2',
        'duration_min' => 'decimal:2',
        'user_rating' => 'float',
        'driver_rating' => 'float',
        ];
    }
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}

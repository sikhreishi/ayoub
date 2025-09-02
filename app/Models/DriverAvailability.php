<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverAvailability extends Model
{
    protected $table = 'driver_availability';

    protected $primaryKey = 'driver_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $keyType = 'int';

    protected $fillable = [
        'driver_id',
        'latitude',
        'longitude',
        'geohash',
        'last_ping',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'last_ping' => 'datetime',
        'is_available' => 'boolean',
        ];
    }
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
       protected $fillable = [
        'trip_id',
        'type',
        'user_id',
        'lat',
        'lng',
        'speed',
        'heading',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
         'type' => 'integer',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'speed' => 'decimal:2',
        'heading' => 'float',
        'recorded_at' => 'datetime',
        ];
    }
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripHistory extends Model
{
    protected $fillable = [
        'trip_id',
        'user_id',
    ];
    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

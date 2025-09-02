<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripReview extends Model
{
    protected $fillable = [
        'trip_id',
        'user_id',
        'is_driver',
        'comment',
        'rating',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }
}

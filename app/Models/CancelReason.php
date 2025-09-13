<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CancelReason extends Model
{
    protected $fillable = ['reason_en', 'reason_ar', 'is_active'];

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}

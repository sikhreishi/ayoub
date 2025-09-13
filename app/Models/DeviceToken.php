<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    protected $fillable = ['user_id', 'token', 'platform', 'is_active','last_used_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


     public function scopeActive($query)
    {
        return $query->where('is_active', true); // Only return tokens where 'is_active' is true
    }

      public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc'); // Or 'updated_at' if you prefer that field
    }


}

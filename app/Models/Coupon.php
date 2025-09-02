<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'max_uses',
        'max_uses_per_user',
        'min_trip_amount',
        'starts_at',
        'expires_at',
        'is_active'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('uses')->withTimestamps();
    }

    public function isValidFor(User $user, float $tripAmount): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && now()->lt($this->starts_at)) return false;
        if ($this->expires_at && now()->gt($this->expires_at)) return false;
        if ($this->min_trip_amount && $tripAmount < $this->min_trip_amount) return false;
        $totalUsed = $this->users()->sum('uses');
        if ($this->max_uses && $totalUsed >= $this->max_uses) return false;
        $userUsed = $this->users()->where('user_id', $user->id)->first()?->pivot->uses ?? 0;
        if ($this->max_uses_per_user && $userUsed >= $this->max_uses_per_user) return false;
        return true;
    }

    public function applyDiscount(float $amount): float
    {
        if ($this->type === 'fixed') {
            return max(0, $amount - $this->value);
        } elseif ($this->type === 'percent') {
            return max(0, $amount - ($amount * $this->value / 100));
        }

        return $amount;
    }
}

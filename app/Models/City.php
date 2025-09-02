<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = [
        'country_id',
        'name_en',
        'name_ar',
        'lat',
        'lng',
    ];

    // A city belongs to a country
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    // A city has many addresses
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
    public function districts()
    {
        return $this->hasMany(District::class);
    }

    public function user()
    {
        return $this->hasMany(User::class);
    }
}

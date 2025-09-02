<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'code',
    ];

    // A country has many cities
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
    public function countrycurrencies()
    {
        return $this->hasMany(CountryCurrency::class, 'country_id');
    }
}

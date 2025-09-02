<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryCurrency extends Model
{
    protected $table = 'countrycurrencies';

    protected $fillable = [
        'country_id',
        'currency_id',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}

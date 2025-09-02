<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];

    public function countrycurrencies()
    {
        return $this->hasMany(CountryCurrency::class, 'currency_id');
    }
}

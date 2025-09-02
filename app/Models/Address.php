<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Address extends Model
{
    protected $fillable = [
        'user_id',
        'city_id',
        'street',
        'district_id',
        'label',
        'lat',
        'lng',
        'type',
    ];

    // An address belongs to a user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // An address belongs to a city
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
    // An address belongs to a district
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}

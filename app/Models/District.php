<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $table = 'districts';

    protected $fillable = [
        'city_id',
        'name_en', 
        'name_ar',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}

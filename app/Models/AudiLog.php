<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AudiLog extends Model
{
    protected $fillable = [
        'admin_id',
        'action',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}

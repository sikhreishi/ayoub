<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class WalletCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'balance',
        'status',
        'generated_by',
        'used_by',
        'used_at'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'used_at' => 'datetime'
    ];

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    public static function generateUniqueCode()
    {
        do {
            $code = 'WC' . strtoupper(Str::random(8));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function markAsUsed($userId)
    {
        $this->update([
            'status' => 'used',
            'used_by' => $userId,
            'used_at' => now()
        ]);
    }

    public function isUnused()
    {
        return $this->status === 'unused';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'amount',
        'transaction_type',
        'reference_type',
        'reference_id',
        'description'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public $timestamps = false;

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}

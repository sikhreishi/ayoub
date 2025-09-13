<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Trip;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'amount',
        'transaction_type',
        'reference_type',
        'reference_id',
        'trip_id',
        'payment_transaction_id',
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

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

}

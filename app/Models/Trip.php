<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\TripDetail;
use App\Models\Location;
use App\Models\TripHistory;
use App\Models\User;
use App\Models\Vehicle\VehicleType;


class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_id',
        'pickup_lat',
        'pickup_lng',
        'dropoff_lat',
        'dropoff_lng',
        'pickup_name',
        'dropoff_name',
        'requested_at',
        'accepted_at',
        'started_at',
        'completed_at',
        'driver_accept_lat',
        'driver_accept_lng',
        'status',
        'estimated_fare',
        'final_fare',
        'payment_status',
        'payment_method',
        'cancelled_at',
        'cancelled_by',
        'vehicle_type_id',
        'cancel_reason_id',
        'cancel_reason_note',

    ];

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'accepted_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'estimated_fare' => 'decimal:2',
            'final_fare' => 'decimal:2',
        ];
    }

    public $timestamps = false;
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function tripDetails()
    {
        return $this->hasOne(TripDetail::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function tripHistory()
    {
        return $this->hasMany(TripHistory::class, 'trip_id');
    }
    public function tripReviews()
    {
        return $this->hasMany(TripHistory::class, 'trip_id');
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class); // Define the relationship
    }

    public function cancelReason()
    {
        return $this->belongsTo(CancelReason::class, 'cancel_reason_id');
    }
}

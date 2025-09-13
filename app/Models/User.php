<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{
    use HasFactory, Notifiable,  HasRoles, HasApiTokens, SoftDeletes;

  protected $fillable = [
        'name',
        'email',
        'phone',
        'language',
        'avatar',
        'gender',
        'password',
        'is_online',
        'current_lat',
        'current_lng',
        'geohash',
        'location_updated_at',
        'invite_code',
        'country_id', 'city_id', 'current_address_id'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected function casts(): array
    {
        return [
            'is_online' => 'boolean',
            'location_updated_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function driverProfile()
    {
        return $this->hasOne(DriverProfile::class);
    }

    public function driverAvailability()
    {
        return $this->hasOne(DriverAvailability::class, 'driver_id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function currentAddress()
    {
        return $this->belongsTo(Address::class, 'current_address_id');
    }
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function driverTrips()
    {
        return $this->hasMany(Trip::class, 'driver_id');
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

     public function sentNotifications()
    {
        return $this->hasMany(Notification::class, 'sender_id');
    }

    public function emailUserNotfictions()
    {
        return $this->hasMany(EmailUserNotfiction::class, 'user_id');
    }
    public function wallets(){
        return $this->hasMany(Wallet::class, 'user_id');
    }
    public function tripHistory()
    {
        return $this->hasMany(TripHistory::class, 'user_id');
    }
    public function tripReviews()
    {
        return $this->hasMany(TripReview::class, 'user_id');
    }
    public function coupons()
    {
        return $this->belongsToMany(Coupon::class)->withPivot('uses')->withTimestamps();
    }

    //     public function setCurrentLatAttribute($value)
    // {
    //     $this->attributes['current_lat'] = $value;

    //     if (isset($this->attributes['current_lng'])) {
    //         $this->attributes['geohash'] = Geohash::encode($value, $this->attributes['current_lng']);
    //     }
    // }

    public function tickets()
    {
        return $this->morphMany(Ticket::class, 'sender');
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function ticketReplies()
    {
        return $this->morphMany(TicketReply::class, 'replier');
    }
}


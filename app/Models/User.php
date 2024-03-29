<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public function routeNotificationForWhatsApp()
    {
        return $this->phone;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'lat',
        'lng',
        'address',
        'role', //USER, DRIVER, MERCHANT
        'avatar',
        'motor_type',
        'vehicle_number',
        'is_working',
        'driver_type',
        'is_premium',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    protected $appends = ['avatar_url', 'rating', 'latlng', 'total_balance'];

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return '/images/media/'.$this->avatar;
        } else {
            return 'https://cdn.vuetifyjs.com/images/john.jpg';
        }
    }
    
    public function getTotalBalanceAttribute()
    {
        return $this->deposits->where('status', 'SUCCESS')->sum('total') + $this->bonuses->sum('amount') + $this->sharings->sum('amount') - $this->transactions->sum('amount') - $this->withdrawals->where('status', 'SUCCESS')->sum('amount');
    }

    public function getRatingAttribute()
    {
        return 5;
    }

    public function getLatLngAttribute()
    {
        return [floatval($this->lat), floatval($this->lng)];
    }

    public function scopePremium($query) {
        return $query->where('is_premium', 1);
    }

    public function scopeBikeDriver($query) {
        return $query->where('driver_type', 'BIKE');
    }

    public function scopeCarDriver($query) {
        return $query->where('driver_type', 'CAR');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function media()
    {
        return $this->hasMany(Order::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function bonuses()
    {
        return $this->hasMany(Bonus::class, 'merchant_id');
    }

    public function sharings()
    {
        return $this->hasMany(ProfitSharing::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'driver_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'user_id');
    }
}

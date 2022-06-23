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
        'role',
        'avatar',
        'motor_type',
        'vehicle_number',
        'is_working',
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

    protected $appends = ['avatar_url', 'rating', 'latlng'];

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return '/images/avatar/'.$this->avatar;
        } else {
            return 'https://cdn.vuetifyjs.com/images/john.jpg';
        }
    }

    public function getRatingAttribute()
    {
        return 5;
    }

    public function getLatLngAttribute()
    {
        return [floatval($this->lat), floatval($this->lng)];
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}

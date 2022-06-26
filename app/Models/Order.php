<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice',
        'user_id',
        'driver_id',
        'restaurant_id',
        'lat',
        'lng',
        'delivery_address',
        'delivery_fee',
        'service_fee',
        'status', //RECEIVED, TAKEN, PAID
    ];

    protected $appends = ['latlng', 'sub_total', 'total'];

    public function getLatLngAttribute()
    {
        return [floatval($this->lat), floatval($this->lng)];
    }

    public function getSubTotalAttribute()
    {
        return $this->items->sum('sub_total');
    }

    public function getTotalAttribute()
    {
        return $this->sub_total + $this->delivery_fee + $this->service_fee;
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
         return $this->belongsTo(User::class, 'user_id');
    }

    public function driver()
    {
         return $this->belongsTo(User::class, 'driver_id');
    }

    public function restaurant()
    {
         return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
}

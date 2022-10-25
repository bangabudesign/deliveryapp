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
        'type', //FOOD,BIKE
        'restaurant_id',
        'origin_lat',
        'origin_lng',
        'origin_address',
        'destination_lat',
        'destination_lng',
        'destination_address',
        'delivery_fee',
        'service_fee',
        'status', //RECEIVED, TAKEN, PAID, CANCELED
    ];

    protected $appends = ['origin', 'destination', 'sub_total', 'total'];

    public function getOriginAttribute()
    {
        return [floatval($this->origin_lat), floatval($this->origin_lng)];
    }

    public function getDestinationAttribute()
    {
        return [floatval($this->destination_lat), floatval($this->destination_lng)];
    }

    public function getSubTotalAttribute()
    {
        if ($this->items) {
            return $this->items->sum('sub_total');
        } else {
            return 0;
        }
    }

    public function getTotalAttribute()
    {
        return $this->sub_total + $this->delivery_fee + $this->service_fee;
    }

    public function scopePaid($query) {
        return $query->where('status', 'PAID');
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

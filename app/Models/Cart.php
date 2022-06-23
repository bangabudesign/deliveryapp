<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'product_id',
        'name',
        'price',
        'qty',
        'note',
    ];

    protected $appends = ['total'];

    public function getTotalAttribute()
    {
        return $this->qty * $this->price;
    }

    public function user()
    {
         return $this->belongsTo(User::class, 'user_id');
    }

    public function restaurant()
    {
         return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
    
    public function product()
    {
         return $this->belongsTo(Product::class, 'product_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'merchant_id',
        'amount',
        'notes',
    ];

    public function order()
    {
         return $this->belongsTo(Order::class, 'order_id');
    }

    public function merchant()
    {
         return $this->belongsTo(User::class, 'merchant_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_id',
        'amount',
        'admin_fee',
        'status', //PENDING, SUCCESS, CANCEL, REJECTED
        'confirmed_at',
        'receipt'
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    protected $appends = ['total', 'receipt_url'];

    public function getTotalAttribute()
    {
        return $this->amount - $this->admin_fee;
    }

    public function getReceiptUrlAttribute()
    {
        return '/images/receipt/' . $this->receipt;
    }

    public function user()
    {
         return $this->belongsTo(User::class, 'user_id');
    }

    public function bank()
    {
         return $this->belongsTo(Bank::class, 'bank_id');
    }
}

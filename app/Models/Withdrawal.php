<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'admin_fee',
        'bank_name',
        'account_number',
        'account_name',
        'status', //PENDING, SUCCESS, CANCEL, REJECTED
        'confirmed_at',
        'receipt'
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    protected $appends = ['total'];

    public function getTotalAttribute()
    {
        return $this->amount - $this->admin_fee;
    }

    public function user()
    {
         return $this->belongsTo(User::class, 'user_id');
    }
}

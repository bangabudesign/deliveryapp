<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'name',
        'image',
        'lat',
        'lng',
        'address',
        'opening_hours',
        'closing_hours',
    ];

    protected $casts = [
        'opening_hours' => 'datetime: H:i',
        'closing_hours' => 'datetime: H:i',
    ];

    protected $appends = ['image_url', 'latlng'];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return '/images/restaurants/'.$this->image;
        } else {
            return 'https://cdn.vuetifyjs.com/images/cards/cooking.png';
        }
    }

    public function getLatLngAttribute()
    {
        return [floatval($this->lat), floatval($this->lng)];
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function merchant()
    {
         return $this->belongsTo(User::class, 'merchant_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'name',
        'image',
        'price',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return '/images/media/'.$this->image;
        } else {
            return 'https://cdn.vuetifyjs.com/images/cards/cooking.png';
        }
    }

   public function restaurant()
   {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
   }
}

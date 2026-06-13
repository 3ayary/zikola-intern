<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price','category_id','price_after_discount','sku','description','stock'];
    protected $casts = ['price' => 'decimal:2'];

    function orders()
    {
        return $this->belongsToMany(Order::class, 'order_product');
    }

    function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    function category()
    {
        return $this->belongsTo(Category::class);
    }

    function images()
    {
        return $this->hasMany(ProductImages::class);
    }

    function primaryImage()
    {
        return $this->hasOne(ProductImages::class)->where('is_primary', true);
    }
}

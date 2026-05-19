<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price'];
    protected $casts = ['price' => 'decimal:2'];

    function orders()
    {
        return $this->belongsToMany(Order::class, 'order_product');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'total_price'];
    function user()
    {
        return $this->belongsTo(User::class);
    }
    function products()
    {
        return $this->belongsToMany(Product::class, 'order_product');
    }
}

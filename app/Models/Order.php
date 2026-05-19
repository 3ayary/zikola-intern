<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'total_price'];
    protected $casts = ['total_price' => 'decimal:2'];

    function user()
    {
        return $this->belongsTo(User::class);
    }
    function products()
    {
        return $this->belongsToMany(Product::class, 'order_product');
    }

    public function scopeExpensive($query)
    {
        return $query->where('total_price', '>', 500);
    }

    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn() => '$' . number_format($this->total_price, 2)
        );
    }
}

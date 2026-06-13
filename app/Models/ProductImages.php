<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImages extends Model
{
    protected $fillable = ['product_id', 'path', 'is_primary'];
    
    function product()
    {
        $this->belongsTo(Product::class);
    }
}

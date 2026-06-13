<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'parent_id'];

    function parent(): BelongsTo
    {
        return  $this->belongsTo(Category::class, 'parent_id');
    }

    function subCategories(): HasMany
    {
        return  $this->hasMany(Category::class, 'parent_id');
    }

    function products()
    {
        return $this->hasMany(Product::class);
    }
}

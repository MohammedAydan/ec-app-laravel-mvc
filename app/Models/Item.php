<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'slug',
        'image_url',
        'image_preview_url',
        'name',
        'description',
        'price',
        'sale_price',
        'currency',
        'stock',
        'tags',
        'sales_count',
    ];

    protected $casts = [
        'tags' => 'array',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];
}

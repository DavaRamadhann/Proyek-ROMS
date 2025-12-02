<?php

namespace App\Domains\Product\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'description',
        'recommendation_text',
        'price',
        'stock',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];
}

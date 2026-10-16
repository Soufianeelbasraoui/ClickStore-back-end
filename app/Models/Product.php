<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'new_price',
        'prev_price',
        'promo',
        'img',
        'rating',
        'reviews',
        'in_stock',
        'description',
    ];

    protected $casts = [
        'new_price' => 'float',
        'rating' => 'float',
        'in_stock' => 'boolean',
    ];

    protected $appends = [
        'newPrice',
        'prevPrice',
        'inStock',
    ];

    public function getNewPriceAttribute()
    {
        return isset($this->attributes['new_price']) ? (float)$this->attributes['new_price'] : 0.0;
    }

    public function getPrevPriceAttribute()
    {
        return $this->attributes['prev_price'] ?? null;
    }

    public function getInStockAttribute()
    {
        return isset($this->attributes['in_stock']) ? (bool)$this->attributes['in_stock'] : true;
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}

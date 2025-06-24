<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartDetailDatabase extends Model
{
    use HasFactory;


    protected $fillable = [
        'cart_id',
        'product_variant_id',
        'quantity',
        'price',
        'reserved_at',
    ];

    public function cart()
    {
        return $this->belongsTo(CartDatabase::class);
    }

    public function product_variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}

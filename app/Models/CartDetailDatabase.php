<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarDetailDatabase extends Model
{
    use HasFactory;

    protected $table = 'cart_detail_databases';

    protected $fillable = [
        'cart_id',
        'product_variant_id',
        'quantity',
        'price',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDiscount extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'discount_id',
    ];

    public function discount()
    {
        return $this->belongsToMany(Discount::class, 'product_discounts');
    }

    public function product()
    {
        return $this->belongsToMany(Product::class, 'product_discounts');
    }
}

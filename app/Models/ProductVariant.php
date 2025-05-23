<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'color',
        'size',
        'price',
        'stock',
        'product_id'
    ];

    public $timestamps = false;

    //1 MoTaSP cho 1 SP
    public function Product() {
        return $this->belongsTo(Product::class);
    }

    public function ImageVariants() {
        return $this->hasMany(ImageVariant::class);
    }
}

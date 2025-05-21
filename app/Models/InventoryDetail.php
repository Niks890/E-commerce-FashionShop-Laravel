<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Thiagoprz\CompositeKey\HasCompositeKey;
use Illuminate\Database\Eloquent\Model;

class InventoryDetail extends Model
{
    use HasFactory, HasCompositeKey;
    protected $primaryKey = ['product_id', 'inventory_id', 'product_variant_id'];
    protected $fillable = [
        'product_id',
        'inventory_id',
        'product_variant_id',
        'quantity',
        'price',
        'size'
    ];

    /** Tao QH Thuc the yeu **/
    //1 CT PhieuNhap thuoc 1 SP va 1 CT PhieuNhap thuoc 1 PhieuNhap => co 2 ham de thuc hien quan he nay
    public function Product() {
        return $this->belongsTo(Product::class);
    }
    public function Inventory() {
        return $this->belongsTo(Inventory::class);
    }

    public function ProductVariant() {
        return $this->belongsTo(ProductVariant::class);
    }


}

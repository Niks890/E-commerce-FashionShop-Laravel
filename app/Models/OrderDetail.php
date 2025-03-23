<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Thiagoprz\CompositeKey\HasCompositeKey;

class OrderDetail extends Model
{
    use HasFactory;
    use HasCompositeKey;

    protected $primaryKey = [
        'product_id',
        'order_id'
    ];

    protected $fillable = [
        'product_id',
        'order_id',
        'product_variant_id',
        'quantity',
        'price',
        'size_and_color',
        'code',
    ];

    /** Tao QH Thuc the yeu **/
    //1 CT DonHang thuoc 1 SP va 1 CT DonHang thuoc 1 DonHang => co 2 ham de thuc hien quan he nay
    public function Product()
    {
        return $this->belongsTo(Product::class);
    }
    public function Order()
    {
        return $this->belongsTo(Order::class);
    }

    public function ProductVariant(){
        return $this->belongsTo(ProductVariant::class);
    }
}

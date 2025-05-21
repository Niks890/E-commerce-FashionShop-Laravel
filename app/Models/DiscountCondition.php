<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCondition extends Model
{
    use HasFactory;
    protected $fillable = [
        'discount_conditions_min_quantity',
        'discount_conditions_percent',
        'discount_gift_product_quantity',
        'discount_id'
    ];

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }


}

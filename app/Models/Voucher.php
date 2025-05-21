<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'vouchers_code',
        'vouchers_description',
        'vouchers_percent_discount',
        'vouchers_max_discount',
        'vouchers_min_order_amount',
        'vouchers_start_date',
        'vouchers_end_date',
        'vouchers_usage_limit'
    ];

    public function voucher_usages()
    {
        return $this->hasMany(VoucherUsage::class);
    }
}

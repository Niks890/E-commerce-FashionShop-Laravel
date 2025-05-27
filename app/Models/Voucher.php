<?php

namespace App\Models;

use Carbon\Carbon;
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




    public function getStatusAttribute()
    {
        $now = Carbon::now();

        if ($now >= $this->vouchers_start_date && $now <= $this->vouchers_end_date) {
            return 'active';
        } elseif ($now > $this->vouchers_end_date) {
            return 'expired';
        } else {
            return 'upcoming';
        }
    }

    /**
     * Get the status text in Vietnamese
     */
    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case 'active':
                return 'Đang hiệu lực';
            case 'expired':
                return 'Đã hết hạn';
            case 'upcoming':
                return 'Sắp diễn ra';
            default:
                return 'Không xác định';
        }
    }

    /**
     * Get the status CSS class
     */
    public function getStatusClassAttribute()
    {
        switch ($this->status) {
            case 'active':
                return 'bg-success';
            case 'expired':
                return 'bg-danger';
            case 'upcoming':
                return 'bg-info';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Check if voucher is currently active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if voucher is expired
     */
    public function isExpired()
    {
        return $this->status === 'expired';
    }

    /**
     * Check if voucher is upcoming
     */
    public function isUpcoming()
    {
        return $this->status === 'upcoming';
    }

    /**
     * Scope to get active vouchers
     */
    public function scopeActive($query)
    {
        $now = Carbon::now();
        return $query->where('vouchers_start_date', '<=', $now)
            ->where('vouchers_end_date', '>=', $now);
    }

    /**
     * Scope to get expired vouchers
     */
    public function scopeExpired($query)
    {
        return $query->where('vouchers_end_date', '<', Carbon::now());
    }

    /**
     * Scope to get upcoming vouchers
     */
    public function scopeUpcoming($query)
    {
        return $query->where('vouchers_start_date', '>', Carbon::now());
    }
}

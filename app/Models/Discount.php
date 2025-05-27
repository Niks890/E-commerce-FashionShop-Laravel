<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'percent_discount',
        'code',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    //1 KM cho n SP
    public function Products()
    {
        return $this->hasMany(Product::class);
    }


     protected static function booted()
    {
        // Sự kiện này được kích hoạt mỗi khi một model được lấy ra từ database.
        static::retrieved(function (Discount $discount) {
            $now = Carbon::now();

            // Kiểm tra nếu khuyến mãi đã hết hạn và đang ở trạng thái 'active'
            // (Không tự động kích hoạt lại nếu đã là 'inactive' thủ công)
            if ($discount->end_date < $now && $discount->status === 'active') {
                $discount->status = 'inactive';
                $discount->saveQuietly(); // saveQuietly() để tránh lặp vô hạn sự kiện retrieved
            }
        });
    }


  public function getCalculatedStatusAttribute()
    {
        $now = Carbon::now();

        // If the manual status is 'inactive', it overrides date-based activity.
        if ($this->status === 'inactive') {
            return 'inactive';
        }

        // Otherwise, determine status based on dates
        if ($this->start_date <= $now && $this->end_date >= $now) {
            return 'active'; // Currently within the active date range
        }

        // If not 'inactive' by manual setting, and not active by date, it's inactive.
        // This covers expired and upcoming deals as 'inactive' from a broad perspective.
        return 'inactive';
    }
}

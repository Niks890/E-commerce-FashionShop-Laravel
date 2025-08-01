<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'brand',
        'sku',
        'description',
        'tags',
        'price',
        'image',
        'length',
        'width',
        'status',
        'category_id',
        'short_description',
        'slug',
        'material',
        'discount_id'
    ];

    //1 SP thuoc 1 DM
    public function Category()
    {
        return $this->belongsTo(Category::class);
    }

    //1 SP co n MoTaSP
    public function ProductVariants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }


    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('active', 1);
    }

    //1 SP co 1 KM
    public function Discount()
    {
        return $this->belongsTo(Discount::class);
    }

    //1 SP chua nhieu CT DonHang
    public function OrderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    //1 SP chua nhieu CT PhieuNhap
    public function InventoryDetails()
    {
        return $this->hasMany(InventoryDetail::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function color(){
        return $this->hasMany(Color::class);
    }


    public function getDiscountedPriceAttribute()
    {
        // Kiểm tra xem có khuyến mãi và khuyến mãi đó có đang hoạt động không
        if ($this->discount && $this->has_active_discount) {
            $discountPercentage = $this->discount->percent_discount;
            return $this->price - ($this->price * $discountPercentage);
        }
        return $this->price; // Trả về giá gốc nếu không có khuyến mãi hoặc khuyến mãi không hợp lệ
    }

    public function getHasActiveDiscountAttribute()
    {
        if ($this->discount_id === null || !$this->discount) {
            return false;
        }

        $now = Carbon::now();
        $startDate = Carbon::parse($this->discount->start_date);
        $endDate = Carbon::parse($this->discount->end_date);

        // Kiểm tra trạng thái 'active' và ngày hiệu lực
        return $this->discount->status === 'active' && $now->between($startDate, $endDate);
    }
}

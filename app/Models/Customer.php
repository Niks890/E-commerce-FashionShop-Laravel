<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'email',
        'username',
        'password',
        'sex',
        'image',
        'platform_id', //Đăng nhập bằng nền tảng gì đó (google, facebook,...)
        'platform_name'
    ];

    protected $hidden = [
        'password'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function voucherUsages()
    {
        return $this->hasMany(VoucherUsage::class);
    }

    public function blogcommentsofcustomer()
    {
        return $this->hasMany(BlogComment::class);
    }

    public function likecommentsofcustomer()
    {
        return $this->hasMany(LikeComment::class);
    }


    // In your Customer model
public function hasVoucher($voucherId)
{
    return $this->voucherUsages->pluck('voucher_id')->contains($voucherId);
}
}

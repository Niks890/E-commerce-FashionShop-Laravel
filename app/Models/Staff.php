<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Staff extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'email',
        'sex',
        'username',
        'password',
        'position',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    //1 NhanVien tao n PhieuNhap
    public function Inventories() {
        return $this->hasMany(Inventory::class);
    }

    public function blogs() {
        return $this->hasMany(Blog::class);
    }
}

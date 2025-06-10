<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
        'provider_id',
        'staff_id',
        'vat',
        'status',
        'note',
        'approved_by'
    ];

    //1 PhieuNhap cho 1 NCC
    public function Provider()
    {
        return $this->belongsTo(Provider::class);
    }

    //1 PhieuNhap chua n CT PhieuNhap
    public function InventoryDetails()
    {
        return $this->hasMany(InventoryDetail::class);
    }

    //1 PhieuNhap duoc tao boi 1 NhanVien
    public function Staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(Staff::class, 'approved_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone'
    ];

    //1 NCC cho n PhieuNhap
    public function Inventories() {
        return $this->hasMany(Inventory::class);
    }
}

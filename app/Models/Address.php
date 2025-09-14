<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'province_code',
        'province_name',
        'ward_code',
        'ward_name',
        'street_address',
        'full_address',
        'is_default'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

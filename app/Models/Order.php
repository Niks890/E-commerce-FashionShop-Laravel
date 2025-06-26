<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'shipping_fee',
        'total',
        'note',
        'receiver_name',
        'email',
        'phone',
        'status',
        'VAT',
        'image',
        'payment',
        'customer_id',
        'transaction_id',
        'staff_delivery_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function staffDelivery()
    {
        return $this->belongsTo(Staff::class, 'staff_delivery_id');
    }

    public function voucherUsages()
    {
        return $this->hasMany(VoucherUsage::class);
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }





}

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
        'payment',
        'customer_id',
        'transaction_id'
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
}

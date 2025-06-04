<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartDatabase extends Model
{
    use HasFactory;

    protected $table = 'cart_databases';

    protected $fillable = [
        'cart_session_id',
        'customer_id',
        'total',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function cartdetails()
    {
        return $this->hasMany(CarDetailDatabase::class);
    }
}

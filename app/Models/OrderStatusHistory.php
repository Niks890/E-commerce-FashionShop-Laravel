<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'status',
        'note',
        'updated_by',
        'order_id',
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function staff() {
        return $this->belongsTo(Staff::class);
    }
}

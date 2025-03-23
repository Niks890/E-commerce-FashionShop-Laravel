<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image',
        'slug',
        'tags',
        'staff_id'
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}

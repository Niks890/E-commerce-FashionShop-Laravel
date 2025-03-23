<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['category_name', 'status'];

    // protected $hidden = ['password', 'updated_at'];

    public function Products() {
        return $this->hasMany(Product::class);
    }
}

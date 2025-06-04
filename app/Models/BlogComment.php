<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogComment extends Model
{
    use HasFactory;

    protected $fillable =[
        'comment_id',
        'content',
        'status',
        'parent_id',
        'blog_id',
        'customer_id',
    ];


    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function blogcomments()
    {
        return $this->hasMany(BlogComment::class, 'parent_id');
    }

    public function likecomments()
    {
        return $this->hasMany(LikeComment::class);
    }
}

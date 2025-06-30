<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikeComment extends Model
{
    use HasFactory;


    protected $table = 'like_comments';
    // protected $primaryKey = ['comment_id', 'customer_id'];
    protected $fillable =[
        'comment_id',
        'customer_id',
        'like_count',
    ];




    public function comment()
    {
        return $this->belongsTo(BlogComment::class, 'blog_comment_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}

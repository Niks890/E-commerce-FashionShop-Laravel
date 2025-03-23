<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'star' => 'required',
            'content' => 'required',
        ],[
            'star.required' => 'Rating is required',
            'content.required' => 'content is required',
        ]);

        $comment = new Comment();
        $comment->star = $data['star'];
        $comment->content = $data['content'];
        $comment->product_id = $request->product_id;
        $comment->order_id = $request->order_id;
        $comment->customer_id =  Auth::guard('customer')->user()->id;
        $comment->save();

        $customer = Customer::find($comment->customer_id);

        return response()->json([
            'success' => true,
            'review' => [
                'user_name' => $customer ? $customer->name : 'Người dùng ẩn danh',
                'star' => $comment->star,
                'content' => $comment->content,
                'created_at' => $comment->created_at
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        //
    }
}

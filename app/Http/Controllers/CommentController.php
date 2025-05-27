<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Comment::with(['customer', 'product']);

        // Tìm kiếm theo từ khóa
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('content', 'LIKE', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('product', function ($productQuery) use ($search) {
                        $productQuery->where('product_name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Lọc theo đánh giá
        if ($request->filled('rating')) {
            $query->where('star', $request->rating);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo ngày
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $data = $query->orderBy('id', 'DESC')->paginate(5);

        return view('admin.comment.index', compact('data'));
    }

    public function destroy($id)
    {
        try {
            $comment = Comment::findOrFail($id);
            $comment->delete();

            return redirect()->route('comment.index')->with('success', 'Xóa bình luận thành công!');
        } catch (\Exception $e) {
            return redirect()->route('comment.index')->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }



    public function show(Comment $comment)
    {
        $comment = Comment::with(['customer', 'product'])->findOrFail($comment->id);
        return view('admin.comment.show', compact('comment'));
    }


    public function update(Request $request, Comment $comment)
    {
        try {
            $comment = Comment::findOrFail($comment->id);

            // Toggle trạng thái
            $comment->status = $comment->status == 1 ? 0 : 1;
            $comment->save();

            $message = $comment->status == 1 ? 'Hiển thị bình luận thành công!' : 'Ẩn bình luận thành công!';

            return redirect()->route('comment.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('comment.index')->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}

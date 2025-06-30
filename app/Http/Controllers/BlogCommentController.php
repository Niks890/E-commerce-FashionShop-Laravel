<?php

namespace App\Http\Controllers;

use App\Models\BlogComment;
use App\Models\LikeComment;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogCommentController extends Controller
{
    // Middleware to ensure user is authenticated for comment actions
    public function __construct()
    {
        $this->middleware('auth:customer')->except(['index', 'show', 'getComments']);
    }

    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'blog_id' => 'required|exists:blogs,id',
            'parent_id' => 'nullable|exists:blog_comments,id'
        ]);

        $comment = BlogComment::create([
            'content' => $validated['content'],
            'blog_id' => $validated['blog_id'],
            'parent_id' => $validated['parent_id'] ?? null,
            'customer_id' => auth('customer')->id(),
            'status' => 'Visible'
        ]);

        // Load relationships để trả về đầy đủ thông tin
        $comment->load(['customer', 'likecomments']);

        return response()->json([
            'success' => true,
            'message' => 'Bình luận đã được thêm thành công',
            'comment' => $comment
        ]);
    }

    /**
     * Update the specified comment in storage.
     */
    public function update(Request $request, $id)
    {
        $comment = BlogComment::findOrFail($id);

        // Check if the authenticated user owns the comment
        if ($comment->customer_id !== auth('customer')->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $comment->update([
            'content' => $validated['content'],
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment updated successfully',
            'comment' => $comment
        ]);
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy($id)
    {
        $comment = BlogComment::findOrFail($id);

        // Check if the authenticated user owns the comment
        if ($comment->customer_id !== auth('customer')->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }

        // Delete the comment and its replies
        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }

    /**
     * Like or unlike a comment
     */
    public function toggleLike($id)
    {
        $comment = BlogComment::findOrFail($id);
        $customerId = auth('customer')->id();

        $like = LikeComment::where([
            'blog_comment_id' => $comment->id,
            'customer_id' => $customerId
        ])->first();

        if ($like) {
            // Unlike the comment
            $like->delete();
            $isLiked = false;
        } else {
            // Like the comment
            LikeComment::create([
                'blog_comment_id' => $comment->id,
                'customer_id' => $customerId
            ]);
            $isLiked = true;
        }

        // Get updated like count
        $likeCount = $comment->likecomments()->count();

        return response()->json([
            'success' => true,
            'isLiked' => $isLiked,
            'likeCount' => $likeCount
        ]);
    }

    /**
     * Get comments for a blog post with pagination
     */
    public function getComments(Request $request, $blogId)
    {
        $filter = $request->get('filter', 'recent');

        $query = BlogComment::with(['customer', 'likecomments', 'blogcomments.customer', 'blogcomments.likecomments'])
            ->where('blog_id', $blogId)
            ->whereNull('parent_id')
            ->where('status', 'Visible');

        // Apply filtering
        if ($filter === 'popular') {
            $query->withCount('likecomments')
                  ->orderBy('like_count', 'desc')
                  ->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        if ($request->has('page')) {
            // Pagination for load more
            $comments = $query->paginate(10);

            return response()->json([
                'success' => true,
                'comments' => $comments
            ]);
        } else {
            // All comments for filtering
            $comments = $query->get();

            return response()->json([
                'success' => true,
                'comments' => $comments
            ]);
        }
    }
}

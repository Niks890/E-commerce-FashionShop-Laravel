<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\CloudinaryService;


class BlogController extends Controller
{

    public function index()
    {
        $data = Blog::paginate(5);
        return view('admin.blog.index', compact('data'));
    }


    public function store(Request $request, CloudinaryService $cloudinaryService)
    {
        $data = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'image' => 'required|file|image|max:2048',
            'blog_tag' => 'required',
            'status' => 'required',
            'staff_id' => 'required',
        ], [
            'title.required' => 'Title is required',
            'content.required' => 'Content is required',
            'image.required' => 'Image is required',
            'blog_tag.required' => 'Blog Tag is required',
            'status.required' => 'Status is required',
        ]);

        // Upload ảnh lên Cloudinary
        $uploadResult = $cloudinaryService->uploadImage($request->file('image')->getPathname(), 'blog_images');

        if (isset($uploadResult['error'])) {
            return redirect()->back()->with('error', 'Upload ảnh thất bại: ' . $uploadResult['error']);
        }

        $blog = new Blog();
        $blog->title = $data['title'];
        $blog->content = $data['content'];
        $blog->image = $uploadResult['url']; // Lưu URL từ Cloudinary
        $blog->slug = Str::slug($data['title']);
        $blog->tags = $data['blog_tag'];
        $blog->status = $data['status'];
        $blog->staff_id = $data['staff_id'];
        $blog->save();

        return redirect()->route('blog.index')->with('success', 'Thêm bài viết thành công');
    }


    public function update(Request $request, Blog $blog, CloudinaryService $cloudinaryService)
    {
        $data = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'blog_tag' => 'required',
            'status' => 'required',
            'staff_id' => 'required',
            'image_update' => 'nullable|file|image|max:2048', // ảnh có thể có hoặc không
        ], [
            'title.required' => 'Title is required',
            'content.required' => 'Content is required',
            'blog_tag.required' => 'Blog Tag is required',
            'status.required' => 'Status is required',
        ]);

        $blog->title = $data['title'];
        $blog->slug = Str::slug($data['title']);
        $blog->content = $data['content'];
        $blog->tags = $data['blog_tag'];
        $blog->status = $data['status'];
        $blog->staff_id = $data['staff_id'];

        // Nếu có ảnh mới thì upload lên Cloudinary
        if ($request->hasFile('image_update')) {
            $uploadResult = $cloudinaryService->uploadImage(
                $request->file('image_update')->getPathname(),
                'blog_images'
            );

            if (isset($uploadResult['error'])) {
                return redirect()->back()->with('error', 'Upload ảnh thất bại: ' . $uploadResult['error']);
            }

            $blog->image = $uploadResult['url']; // Cập nhật ảnh mới
        }

        $blog->save();

        return redirect()->route('blog.index')->with('success', 'Cập nhật bài viết thành công');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        if ($blog->staff()->count() == 0) {
            $blog->delete();
            return redirect()->route('blog.index')->with('success', 'Xoá bài viết thành công');
        }
        return redirect()->route('blog.index')->with('error', 'Xoá thất bại');
    }

    public function search(Request $request)
    {
        $search = $request->input('query');
        $data = Blog::with('staff')->where('title', 'like', "%$search%")->paginate(3);
        return view('admin.blog.index', compact('data'));
    }
}

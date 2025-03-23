<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Blog::paginate(5);
        return view('admin.blog.index', compact('data'));
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
            'title' => 'required',
            'content' => 'required',
            'image' => 'required',
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

        $blog = new Blog();
        $blog->title = $data['title'];
        $blog->content = $data['content'];
        //Xu ly anh
        $file_name = $request->image->hashName();
        $request->image->move(public_path('uploads'), $file_name);
        $blog->image = $file_name;
        $blog->slug = Str::slug($data['title']);
        $blog->tags = $data['blog_tag'];
        $blog->status = $data['status'];
        $blog->staff_id = $data['staff_id'];
        $blog->save();

        return redirect()->route('blog.index')->with('success', 'Thêm bài viết thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blog $blog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        // dd($request->all());
        $data = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'image_update' => 'required',
            'blog_tag' => 'required',
            'status' => 'required',
            'staff_id' => 'required',
        ],[
            'title.required' => 'Title is required',
            'content.required' => 'Content is required',
            'image_update.required' => 'Image is required',
            'blog_tag.required' => 'Blog Tag is required',
            'status.required' => 'Status is required',
        ]);

        $blog->title = $data['title'];
        $blog->slug = Str::slug($data['title']);
        $blog->content = $data['content'];
        //Xu ly anh
        $file_name = $request->image_update->hashName();
        $request->image_update->move(public_path('uploads'), $file_name);
        $blog->image = $file_name;

        $blog->tags = $data['blog_tag'];
        $blog->status = $data['status'];
        $blog->staff_id = $data['staff_id'];
        $blog->save();
        return redirect()->route('blog.index')->with('success', 'Cập nhật nội dung bài viết thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
      if($blog->staff()->count() == 0) {
        $blog->delete();
        return redirect()->route('blog.index')->with('success', 'Xoá bài viết thành công');
      }
      return redirect()->route('blog.index')->with('error', 'Xoá thất bại');
    }

    public function search(Request $request){
        $search = $request->input('query');
        $data = Blog::with('staff')->where('title', 'like', "%$search%")->paginate(3);
        return view('admin.blog.index', compact('data'));
    }
}

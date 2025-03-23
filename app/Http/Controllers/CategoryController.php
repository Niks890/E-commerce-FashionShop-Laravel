<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Category::orderBy('id', 'ASC')->paginate(); //SELECT * FROM CATEGORY
        return view('admin.category.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) //[]
    {
        $rule = [
            'name' => 'required',
            'status' => 'required'
        ];
        $message = [
            'name.required' => 'Trường này bắt buộc nhâp'
        ];
        $data = $request->validate($rule, $message);
        $categories = new Category();
        $categories->category_name = $data['name']; //$_POST['']
        $categories->status = $data['status'];
        $categories->save();
        return redirect()->route('category.index')->with('success', 'Thêm danh mục mới thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Category::find($id); //$data = Category::all()->get($id - 1);
        // dd($data); die dump
        return view('admin.category.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required',
            'status' => 'required'
        ], [
            'name.required' => 'Trường này bắt buộc nhâp'
        ]);
        $category->category_name = $data['name'];
        $category->status = $data['status'];
        $category->save();

        return redirect()->route('category.index')->with('success', 'Sửa thông tin danh mục thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->Products->count() == 0) {
            $category->delete();
            return redirect()->back()->with('success', 'Xoá danh mục thành công!');
        }
        return redirect()->back()->with('error', 'Xoá thất bại!');
    }

     /**
     * Search Engine the specified resource from storage.
     */
    public function search(Request $request)
    {
        $keyword = $request->input('query');
        $data = Category::where('category_name', 'like', "%$keyword%")->paginate();
        return view('admin.category.index', compact('data', 'keyword'));
    }
}

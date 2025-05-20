<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Discount::orderBy('id', 'ASC')->paginate();
        return view('admin.discount.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:0|max:255',
            'percent_discount' => 'required|numeric|min:0',
            'start_date' => 'required',
            'end_date' => 'required'
        ], [
            'name.required' => 'Tên chương trình không được để trống.',
            'percent_discount.required' => 'Vui lòng nhập phần trăm khuyến mãi.',
            'percent_discount.numeric' => 'Phần trăm khuyến mãi phải là kiểu số.',
            'percent_discount.min' => 'Vui lòng nhập số dương.',
            'start_date.required' => 'Vui lòng nhập ngày bắt đầu cho chương trình.',
            'end_date.required' => 'Vui lòng nhập ngày kết thúc cho chương trình.'
        ]);
        $discount = new Discount();
        $discount->name = $data['name'];
        $discount->percent_discount = $data['percent_discount'] / 100;
        $discount->start_date = $data['start_date'];
        $discount->end_date = $data['end_date'];
        $discount->save();
        return redirect()->route('discount.index')->with('success', 'Thêm chương trình khuyến mãi mới thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Discount $discount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Discount $discount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Discount $discount)
    {
        $data = $request->validate([
            'name' => 'required|string|min:0|max:255',
            'percent_discount' => 'required|numeric|min:0',
            'start_date' => 'required',
            'end_date' => 'required'
        ], [
            'name.required' => 'Tên chương trình không được để trống.',
            'percent_discount.required' => 'Vui lòng nhập phần trăm khuyến mãi.',
            'percent_discount.numeric' => 'Phần trăm khuyến mãi phải là kiểu số.',
            'percent_discount.min' => 'Vui lòng nhập số dương.',
            'start_date.required' => 'Vui lòng nhập ngày bắt đầu cho chương trình.',
            'end_date.required' => 'Vui lòng nhập ngày kết thúc cho chương trình.'
        ]);
        $discount->name = $data['name'];
        $discount->percent_discount = $data['percent_discount'] / 100;
        $discount->start_date = $data['start_date'];
        $discount->end_date = $data['end_date'];
        $discount->save();
        return redirect()->route('discount.index')->with('success', 'Sửa chương trình khuyến mãi thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discount $discount)
    {
        if ($discount->Products->count() == 0) {
            $discount->delete();
            return redirect()->back()->with('success', 'Xoá khuyến mãi thành công!');
        }
        return redirect()->back()->with('error', 'Xoá khuyến mãi thất bại!');
    }

    public function search(Request $request)
    {
        $keyword = $request->input('query');
        $data = Discount::where('name', 'like', "%$keyword%")->orWhere('id', "%$keyword%")->paginate(3);
        return view('admin.discount.index', compact('data', 'keyword'));
    }
}

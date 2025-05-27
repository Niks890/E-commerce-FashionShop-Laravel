<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;
use Carbon\Carbon;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::orderBy('created_at', 'desc')->paginate(10);

        // Transform data to match frontend format
        $transformedVouchers = $vouchers->map(function($voucher) {
            return [
                'id' => $voucher->id,
                'name' => $voucher->vouchers_code . ' - ' . $voucher->vouchers_description,
                'code' => $voucher->vouchers_code,
                'description' => $voucher->vouchers_description,
                'percent_discount' => $voucher->vouchers_percent_discount / 100,
                'max_discount' => $voucher->vouchers_max_discount,
                'min_order_amount' => $voucher->vouchers_min_order_amount,
                'available_uses' => $voucher->vouchers_usage_limit,
                'start_date' => $voucher->vouchers_start_date,
                'end_date' => $voucher->vouchers_end_date,
                'created_at' => $voucher->created_at,
                'updated_at' => $voucher->updated_at,
            ];
        });

        return view('admin.voucher.voucher', compact('vouchers', 'transformedVouchers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'vouchers_code' => 'required|string|max:10|unique:vouchers',
            'percent_discount' => 'required|numeric|min:0|max:100',
            'max_discount' => 'required|numeric|min:0',
            'min_order_amount' => 'required|numeric|min:0',
            'available_uses' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        Voucher::create([
            'vouchers_code' => $request->vouchers_code,
            'vouchers_description' => $request->name,
            'vouchers_percent_discount' => $request->percent_discount,
            'vouchers_max_discount' => $request->max_discount,
            'vouchers_min_order_amount' => $request->min_order_amount,
            'vouchers_start_date' => $request->start_date,
            'vouchers_end_date' => $request->end_date,
            'vouchers_usage_limit' => $request->available_uses,
        ]);

        return response()->json(['success' => true, 'message' => 'Voucher đã được tạo thành công!']);
    }

    public function update(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'vouchers_code' => 'required|string|max:10|unique:vouchers,vouchers_code,' . $id,
            'percent_discount' => 'required|numeric|min:0|max:100',
            'max_discount' => 'required|numeric|min:0',
            'min_order_amount' => 'required|numeric|min:0',
            'available_uses' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $voucher->update([
            'vouchers_code' => $request->vouchers_code,
            'vouchers_description' => $request->name,
            'vouchers_percent_discount' => $request->percent_discount,
            'vouchers_max_discount' => $request->max_discount,
            'vouchers_min_order_amount' => $request->min_order_amount,
            'vouchers_start_date' => $request->start_date,
            'vouchers_end_date' => $request->end_date,
            'vouchers_usage_limit' => $request->available_uses,
        ]);

        return response()->json(['success' => true, 'message' => 'Voucher đã được cập nhật thành công!']);
    }

    public function destroy($id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();

        return response()->json(['success' => true, 'message' => 'Voucher đã được xóa thành công!']);
    }

    public function show($id)
    {
        $voucher = Voucher::findOrFail($id);

        $transformedVoucher = [
            'id' => $voucher->id,
            'name' => $voucher->vouchers_code . ' - ' . $voucher->vouchers_description,
            'code' => $voucher->vouchers_code,
            'description' => $voucher->vouchers_description,
            'percent_discount' => $voucher->vouchers_percent_discount / 100,
            'max_discount' => $voucher->vouchers_max_discount,
            'min_order_amount' => $voucher->vouchers_min_order_amount,
            'available_uses' => $voucher->vouchers_usage_limit,
            'start_date' => $voucher->vouchers_start_date,
            'end_date' => $voucher->vouchers_end_date,
        ];

        return response()->json($transformedVoucher);
    }

    public function search(Request $request)
    {
        $query = Voucher::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('vouchers_code', 'like', "%{$search}%")
                  ->orWhere('vouchers_description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && !empty($request->status)) {
            $now = Carbon::now();
            switch ($request->status) {
                case 'active':
                    $query->where('vouchers_start_date', '<=', $now)
                          ->where('vouchers_end_date', '>=', $now);
                    break;
                case 'inactive':
                    $query->where('vouchers_end_date', '<', $now);
                    break;
                case 'upcoming':
                    $query->where('vouchers_start_date', '>', $now);
                    break;
            }
        }

        $vouchers = $query->orderBy('created_at', 'desc')->get();

        $transformedVouchers = $vouchers->map(function($voucher) {
            return [
                'id' => $voucher->id,
                'name' => $voucher->vouchers_code . ' - ' . $voucher->vouchers_description,
                'code' => $voucher->vouchers_code,
                'description' => $voucher->vouchers_description,
                'percent_discount' => $voucher->vouchers_percent_discount / 100,
                'max_discount' => $voucher->vouchers_max_discount,
                'min_order_amount' => $voucher->vouchers_min_order_amount,
                'available_uses' => $voucher->vouchers_usage_limit,
                'start_date' => $voucher->vouchers_start_date,
                'end_date' => $voucher->vouchers_end_date,
            ];
        });

        return response()->json($transformedVouchers);
    }
}

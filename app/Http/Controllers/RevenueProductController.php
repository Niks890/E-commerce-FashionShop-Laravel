<?php

namespace App\Http\Controllers;

use App\Exports\ProductBestSellerDayExcel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RevenueProductController extends Controller
{


    public function revenueProductBestSeller(Request $request)
    {

        $from = request('from');
        $to = request('to');
            if (!$from) {
        $from = Carbon::now()->subDays(7)->format('Y-m-d');
    }
    if (!$to) {
        $to = Carbon::now()->format('Y-m-d');
    }

        $query = DB::table('order_details as od')
            ->join('orders as o', 'od.order_id', '=', 'o.id')
            ->join('products as p', 'od.product_id', '=', 'p.id')
            ->select(
                'p.id as product_id',
                'p.product_name',
                DB::raw('SUM(od.quantity) as total_sold'),
                DB::raw('SUM(o.total) as total_revenue')
            )
            ->where('o.status',  'Đã Thanh Toán');



        if ($from) {
            $query->whereDate('o.created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('o.created_at', '<=', $to);
        }

        // Dữ liệu cho bảng (phân trang)
        $bestsellers = (clone $query)
            ->groupBy('p.product_name', 'p.id')
            ->orderByDesc('total_sold')
            ->paginate(10);

        // Dữ liệu cho biểu đồ (không phân trang, top 10 thôi)
        $bestsellersForChart = (clone $query)
            ->groupBy('p.product_name', 'p.id')
            ->orderByDesc('total_sold')
            ->get();

        return view('admin.product.revenue.bestseller.bestseller', compact('bestsellers', 'bestsellersForChart', 'from', 'to'));
    }


// private function getRevenueDataProductBestSeller($from, $to)
// {
//     $query = DB::table('order_details as od')
//         ->join('orders as o', 'od.order_id', '=', 'o.id')
//         ->join('products as p', 'od.product_id', '=', 'p.id')
//         ->select(
//             'p.product_name',
//             DB::raw('SUM(od.quantity) as total_sold'),
//             DB::raw('SUM(o.total) as total_revenue') // giả định total_price đã có VAT
//         )
//         ->where('o.status', 'Đã Thanh Toán');

//     if ($from) {
//         $query->whereDate('o.created_at', '>=', $from);
//     }

//     if ($to) {
//         $query->whereDate('o.created_at', '<=', $to);
//     }

//     return $query
//         ->groupBy('p.product_name')
//         ->orderByDesc('total_sold')
//         ->limit(10)
//         ->get();
// }

private function getRevenueDataProductBestSeller($from, $to)
{
    $query = DB::table('order_details as od')
        ->join('orders as o', 'od.order_id', '=', 'o.id')
        ->join('products as p', 'od.product_id', '=', 'p.id')
        ->join('product_variants as pv', 'od.product_variant_id', '=', 'pv.id') // nếu có bảng variant
        ->select(
            'p.id as product_id',
            'p.product_name',
            'pv.color',
            'pv.size',
            DB::raw('SUM(od.quantity) as variant_quantity'),
            DB::raw('SUM(o.total) as total_revenue')
        )
        ->where('o.status', 'Đã Thanh Toán');

    if ($from) {
        $query->whereDate('o.created_at', '>=', $from);
    }

    if ($to) {
        $query->whereDate('o.created_at', '<=', $to);
    }

    // Lấy chi tiết theo sản phẩm + color + size
    $details = $query
        ->groupBy('p.id', 'p.product_name', 'pv.color', 'pv.size')
        ->get();

    // Gom nhóm để tính tổng theo sản phẩm
    $grouped = $details->groupBy('product_name')->map(function ($items) {
        return [
            'product_name' => $items[0]->product_name,
            'total_sold' => $items->sum('variant_quantity'),
            'total_revenue' => $items->sum('total_revenue'),
            'variants' => $items->map(function ($item) {
                return [
                    'color' => $item->color,
                    'size' => $item->size,
                    'quantity' => $item->variant_quantity,
                ];
            })
        ];
    });

    // Trả về top 10
    return $grouped->sortByDesc('total_sold')->take(10)->values();
}




public function exportPdf(Request $request)
{
    $from = $request->input('from');
    $to = $request->input('to');

    if (!$from || !$to) {
        return redirect()->route('admin.revenueProductBestSeller')->with('error', 'Vui lòng chọn khoảng thời gian trước khi xuất PDF.');
    }

    $data = $this->getRevenueDataProductBestSeller($from, $to);

    $pdf = Pdf::loadView('admin.export.pdf.bestseller.export-bestseller-pdf', [
        'data' => $data,
        'from' => $from,
        'to' => $to
    ]);

    return $pdf->download('san_pham_ban_chay_' . now()->format('Ymd_His') . '.pdf');
}



  public function exportExcel(Request $request)
{
    $from = $request->input('from');
    $to = $request->input('to');

    if (!$from || !$to) {
        return redirect()->route('admin.revenueProductBestSeller')->with('error', 'Vui lòng chọn khoảng thời gian trước khi xuất Excel.');
    }

    $data = collect($this->getRevenueDataProductBestSeller($from, $to));

    return Excel::download(new ProductBestSellerDayExcel($data, $from, $to), 'san_pham_ban_chay_' . now()->format('Ymd_His') . '.xlsx');
}

public function getProductVariantDetail($productId, Request $request)
{
    $from = $request->input('from');
    $to = $request->input('to');

    if (!$from || !$to) {
        return redirect()->route('admin.revenueProductBestSeller')->with('error', 'Vui lòng chọn khoảng thời gian trước khi xuất Excel.');
    }

    $query = DB::table('order_details as od')
        ->join('orders as o', 'od.order_id', '=', 'o.id')
        ->join('product_variants as pv', 'od.product_variant_id', '=', 'pv.id')
        ->where('o.status', 'Đã Thanh Toán')
        ->where('pv.product_id', $productId)
        ->select('pv.color', 'pv.size', DB::raw('SUM(od.quantity) as total_sold'));

    if ($from) {
        $query->whereDate('o.created_at', '>=', $from);
    }

    if ($to) {
        $query->whereDate('o.created_at', '<=', $to);
    }

    $details = $query->groupBy('pv.color', 'pv.size')->get();

    return response()->json($details);
}


}

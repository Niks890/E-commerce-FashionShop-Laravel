<?php

namespace App\Http\Controllers;

use App\Exports\ProductBestSellerDayExcel;
use App\Exports\TopProductsExport;
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
            ->where('o.status',  'Đã thanh toán')
            ->orWhere('o.status', 'Giao hàng thành công');



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
            ->where('o.status', 'Đã thanh toán')
            ->orWhere('o.status', 'Giao hàng thành công');

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
    public function revenueProductBestSellerMonthYear()
    {
        return view('admin.product.revenue.bestseller.bestseller-month-year');
    }

    public function getProductVariantDetail($productId, Request $request)
    {
        // dd($request->all(), $productId);
        $from = $request->input('from');
        $to = $request->input('to');

        if (!$from || !$to) {
            return redirect()->route('admin.revenueProductBestSeller')->with('error', 'Vui lòng chọn khoảng thời gian trước khi xuất Excel.');
        }

        $query = DB::table('order_details as od')
            ->join('orders as o', 'od.order_id', '=', 'o.id')
            ->join('product_variants as pv', 'od.product_variant_id', '=', 'pv.id')
            ->where(function ($q) { // <-- Thêm nhóm điều kiện này
                $q->where('o.status', 'Đã thanh toán')
                    ->orWhere('o.status', 'Giao hàng thành công');
            })
            ->where('pv.product_id', $productId) // <-- Điều kiện này sẽ áp dụng cho cả hai trạng thái trên
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



    public function topProductSalesApi(Request $request)
    {
        $period = $request->input('period', 'year'); // month | quarter | year
        $selectedYear = $request->input('year');

        $groupByRaw = match ($period) {
            'month' => 'MONTH(o.created_at)',
            'quarter' => 'QUARTER(o.created_at)',
            default => 'YEAR(o.created_at)',
        };

        $query = DB::table('order_details as od')
            ->join('orders as o', 'od.order_id', '=', 'o.id')
            ->join('products as p', 'od.product_id', '=', 'p.id')
            ->selectRaw("p.id as product_id, p.product_name, {$groupByRaw} as period, SUM(od.quantity) as total_sold, SUM(od.quantity * od.price) as total_revenue")
            ->where('o.status', 'Đã thanh toán')
            ->orWhere('o.status', 'Giao hàng thành công');

        if ($selectedYear) {
            $query->whereYear('o.created_at', $selectedYear);
        }

        $results = $query
            ->groupBy('p.id', 'p.product_name', 'period')
            ->orderBy('period')
            ->orderByDesc('total_sold')
            ->get();

        // Tạo labels theo period (tháng, quý, năm)
        $labels = [];
        $totalQuantityByPeriod = [];
        $totalRevenueByPeriod = [];

        if ($period === 'month' && $selectedYear) {
            for ($i = 1; $i <= 12; $i++) {
                $labels[$i] = 'Tháng ' . $i;
                $totalQuantityByPeriod[$i] = 0;
                $totalRevenueByPeriod[$i] = 0;
            }
        } elseif ($period === 'quarter' && $selectedYear) {
            for ($i = 1; $i <= 4; $i++) {
                $labels[$i] = 'Quý ' . $i;
                $totalQuantityByPeriod[$i] = 0;
                $totalRevenueByPeriod[$i] = 0;
            }
        } else {
            $uniquePeriods = $results->pluck('period')->unique()->sort()->values();
            foreach ($uniquePeriods as $periodKey) {
                $periodStr = (string)$periodKey;
                $labels[$periodStr] = $periodStr;
                $totalQuantityByPeriod[$periodStr] = 0;
                $totalRevenueByPeriod[$periodStr] = 0;
            }
        }

        // Tổng hợp dữ liệu để vẽ chart
        foreach ($results as $row) {
            $periodKey = (string)$row->period;
            $totalQuantityByPeriod[$periodKey] += $row->total_sold;
            $totalRevenueByPeriod[$periodKey] += $row->total_revenue;
        }

        $quantityValues = [];
        $revenueValues = [];
        $finalLabels = [];

        foreach ($labels as $key => $label) {
            $finalLabels[] = $label;
            $quantityValues[] = (int)($totalQuantityByPeriod[$key] ?? 0);
            $revenueValues[] = (float)($totalRevenueByPeriod[$key] ?? 0);
        }

        $detailData = [];
        foreach ($results as $row) {
            $detailData[$row->period][] = [
                'product_id' => $row->product_id,
                'product_name' => $row->product_name,
                'total_sold' => $row->total_sold,
                'total_revenue' => $row->total_revenue,
            ];
        }

        return response()->json([
            'labels' => $finalLabels,
            'data' => [
                'total_quantity' => $quantityValues,
                'total_revenue' => $revenueValues,
            ],
            'detail' => $detailData,  // Dữ liệu chi tiết để bạn có thể hiển thị bảng bên dưới biểu đồ
        ]);
    }



    public function getProductVariantDetailMonthYear($productId, Request $request)
    {
        $period = $request->input('period'); // month, quarter, year
        $value = $request->input('value'); // ví dụ 5 (tháng 5), hoặc 2025 (năm), hoặc 2 (quý 2)

        if (!$period || !$value) {
            return response()->json(['error' => 'Vui lòng chọn loại thống kê và giá trị tương ứng.'], 400);
        }

        // Tính from và to dựa theo period + value
        switch ($period) {
            case 'month':
                $year = $request->input('year') ?? date('Y'); // THÊM DÒNG NÀY
                $from = \Carbon\Carbon::createFromDate($year, $value, 1)->startOfMonth()->toDateString();
                $to = \Carbon\Carbon::createFromDate($year, $value, 1)->endOfMonth()->toDateString();
                break;
            case 'quarter':
                // Quý 1 = tháng 1-3, quý 2 = 4-6, ...
                $year = $request->input('year') ?? date('Y');
                $startMonth = ($value - 1) * 3 + 1;
                $from = \Carbon\Carbon::createFromDate($year, $startMonth, 1)->startOfMonth()->toDateString();
                $to = \Carbon\Carbon::createFromDate($year, $startMonth, 1)->addMonths(2)->endOfMonth()->toDateString();
                break;
            case 'year':
                $year = $value;
                $from = \Carbon\Carbon::createFromDate($year, 1, 1)->startOfYear()->toDateString();
                $to = \Carbon\Carbon::createFromDate($year, 1, 1)->endOfYear()->toDateString();
                break;
            default:
                return response()->json(['error' => 'Loại thống kê không hợp lệ.'], 400);
        }

        $query = DB::table('order_details as od')
            ->join('orders as o', 'od.order_id', '=', 'o.id')
            ->join('product_variants as pv', 'od.product_variant_id', '=', 'pv.id')
            // Bắt đầu nhóm các điều kiện OR
            ->where(function ($q) {
                $q->where('o.status', 'Đã thanh toán')
                    ->orWhere('o.status', 'Giao hàng thành công');
            })
            // Các điều kiện này sẽ được áp dụng cho cả hai trạng thái trên
            ->where('pv.product_id', $productId)
            ->whereDate('o.created_at', '>=', $from)
            ->whereDate('o.created_at', '<=', $to)
            ->select('pv.color', 'pv.size', DB::raw('SUM(od.quantity) as total_sold'))
            ->groupBy('pv.color', 'pv.size')
            ->get();

        return response()->json($query);
    }
}

<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueInventoryController extends Controller
{
    public function revenueInventory(Request $request)
    {
        $query = DB::table('product_variants as pv')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->select('pv.id as variant_id', 'p.product_name', 'p.id as product_id', 'pv.color', 'pv.size', 'pv.stock')
            ->where('pv.stock', '>=', 0);

        // Lọc theo các trường
        if ($request->filled('product_name')) {
            $query->where('p.product_name', 'like', '%' . $request->product_name . '%');
        }

        if ($request->filled('color')) {
            $query->where('pv.color', 'like', '%' . $request->color . '%');
        }

        if ($request->filled('size')) {
            $query->where('pv.size', $request->size);
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->where('stock', '<', 5)->where('stock', '>', 0);
                    break;
                case 'out':
                    $query->where('stock', '<=', 0);
                    break;
                case 'available':
                    $query->where('stock', '>', 0)->orderBy('stock', 'desc');
                    break;
            }
        }

        $inventoryRevenueCurrent = $query->orderBy('p.product_name')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.inventory.revenue.revenue-inventory', compact('inventoryRevenueCurrent'));
    }



    public function revenueInventoryDatetime(Request $request)
    {
        // Xử lý filter theo thời gian
        $timeRange = $request->input('time_range', 'month');
        $now = Carbon::now();

        $startDate = null;
        $endDate = null;
        $year = $request->input('year', $now->year);
        $month = $request->input('month', $now->month);

        // Xác định điều kiện thời gian cụ thể
        switch ($timeRange) {
            case 'day':
                $startDate = $request->input('start_date', $now->format('Y-m-d'));
                $endDate = $request->input('end_date', $now->format('Y-m-d'));
                break;
            case 'year':
                // Năm đã được lấy ở trên
                break;
            default: // mặc định theo tháng
                // Tháng và năm đã được lấy ở trên
                break;
        }

        // Query tính tổng tiền và số phiếu nhập (từ bảng inventories)
        $totalCostQuery = DB::table('inventories as ii')
            ->select(
                DB::raw('SUM(ii.total) as total_cost'),
                DB::raw('COUNT(ii.id) as import_count')
            );

        if ($timeRange === 'day') {
            $totalCostQuery->whereBetween('ii.created_at', [$startDate, $endDate]);
        } elseif ($timeRange === 'month') {
            $totalCostQuery->whereYear('ii.created_at', $year)
                ->whereMonth('ii.created_at', $month);
        } elseif ($timeRange === 'year') {
            $totalCostQuery->whereYear('ii.created_at', $year);
        }

        $totalSummary = $totalCostQuery->first();

        // Query tính tổng số lượng (từ bảng inventory_details join inventories để filter thời gian)
        $totalQuantityQuery = DB::table('inventory_details as iid')
            ->join('inventories as ii', 'iid.inventory_id', '=', 'ii.id')
            ->select(DB::raw('SUM(iid.quantity) as total_quantity'));

        if ($timeRange === 'day') {
            $totalQuantityQuery->whereBetween('ii.created_at', [$startDate, $endDate]);
        } elseif ($timeRange === 'month') {
            $totalQuantityQuery->whereYear('ii.created_at', $year)
                ->whereMonth('ii.created_at', $month);
        } elseif ($timeRange === 'year') {
            $totalQuantityQuery->whereYear('ii.created_at', $year);
        }

        $totalQuantity = $totalQuantityQuery->value('total_quantity') ?? 0;

        // Kết hợp summary
        $summary = [
            'total_cost' => $totalSummary->total_cost ?? 0,
            'import_count' => $totalSummary->import_count ?? 0,
            'total_quantity' => $totalQuantity
        ];

        // Dữ liệu cho biểu đồ
        $chartDataQuery = DB::table('inventories as ii')
            ->select(DB::raw('SUM(ii.total) as total_cost'));

        switch ($timeRange) {
            case 'day':
                $chartDataQuery->addSelect(DB::raw('DATE(ii.created_at) as label'))
                    ->whereBetween('ii.created_at', [$startDate, $endDate])
                    ->groupBy(DB::raw('DATE(ii.created_at)'))
                    ->orderBy('label');
                break;
            case 'year':
                $chartDataQuery->addSelect(DB::raw('MONTH(ii.created_at) as label'))
                    ->whereYear('ii.created_at', $year)
                    ->groupBy(DB::raw('MONTH(ii.created_at)'))
                    ->orderBy('label');
                break;
            default: // month
                $chartDataQuery->addSelect(DB::raw('DATE(ii.created_at) as label'))
                    ->whereYear('ii.created_at', $year)
                    ->whereMonth('ii.created_at', $month)
                    ->groupBy(DB::raw('DATE(ii.created_at)'))
                    ->orderBy('label');
                break;
        }
        $chartData = $chartDataQuery->get();

        // Danh sách sản phẩm nhập kho (chi tiết)
        $importProducts = DB::table('inventory_details as iid')
            ->join('products as p', 'iid.product_id', '=', 'p.id')
            ->leftJoin('product_variants as pv', 'pv.product_id', '=', 'p.id')
            ->join('inventories as ii', 'iid.inventory_id', '=', 'ii.id')
            ->select(
                'p.product_name',
                'pv.color',
                'pv.size',
                'iid.quantity',
                'iid.price',
                DB::raw('ii.total as total_price'),
                'ii.created_at'
            );

        if ($timeRange === 'day') {
            $importProducts->whereBetween('ii.created_at', [$startDate, $endDate]);
        } elseif ($timeRange === 'month') {
            $importProducts->whereYear('ii.created_at', $year)
                ->whereMonth('ii.created_at', $month);
        } elseif ($timeRange === 'year') {
            $importProducts->whereYear('ii.created_at', $year);
        }

        $importProducts = $importProducts->paginate(10)->appends($request->query());

        return view('admin.inventory.revenue.revenue-inventory-datetime', [
            'summary' => $summary,
            'chartData' => $chartData,
            'importProducts' => $importProducts,
            'timeRange' => $timeRange,
            'currentYear' => $now->year,
            'currentMonth' => $now->month,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedYear' => $year,
            'selectedMonth' => $month
        ]);
    }
}

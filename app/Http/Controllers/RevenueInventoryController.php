<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueInventoryController extends Controller
{
    public function revenueInventory(Request $request)
    {
        $query = DB::table('product_variants as pv')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->select('pv.id as variant_id', 'p.product_name', 'p.id as product_id', 'pv.color', 'pv.size', 'pv.stock', 'pv.available_stock')
            ->where('pv.stock', '>=', 0)
            ->whereIn('p.status', [0, 1]);

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

            if ($request->has('export_pdf')) {
                $data = [
                    'inventory' => $query->get(),
                    'filter' => $request->all(),
                    'export_time' => now()->format('d/m/Y H:i')
                ];

                $pdf = Pdf::loadView('admin.inventory.revenue.export-pdf', $data);
                return $pdf->download('bao-cao-ton-kho-'.now()->format('Ymd').'.pdf');
            }

            $inventoryRevenueCurrent = $query->paginate(10)->appends($request->query());
            return view('admin.inventory.revenue.revenue-inventory', compact('inventoryRevenueCurrent'));

    }



    // public function revenueInventoryDatetime(Request $request)
    // {
    //     // Xử lý filter theo thời gian
    //     $timeRange = $request->input('time_range', 'month');
    //     $now = Carbon::now();

    //     $startDate = null;
    //     $endDate = null;
    //     $year = $request->input('year', $now->year);
    //     $month = $request->input('month', $now->month);

    //     switch ($timeRange) {
    //         case 'day':
    //             $startDate = $request->input('start_date', $now->format('Y-m-d'));
    //             $endDate = $request->input('end_date', $now->format('Y-m-d'));
    //             break;
    //         case 'year':
    //             break;
    //         default:
    //             break;
    //     }

    //     // Query tính tổng tiền và số phiếu nhập
    //     $totalCostQuery = DB::table('inventories as ii')
    //         ->select(
    //             DB::raw('SUM(ii.total) as total_cost'),
    //             DB::raw('COUNT(ii.id) as import_count')
    //         );
    //     if ($timeRange === 'day') {
    //         $totalCostQuery->whereBetween('ii.created_at', [$startDate, $endDate]);
    //     } elseif ($timeRange === 'month') {
    //         $totalCostQuery->whereYear('ii.created_at', $year)
    //             ->whereMonth('ii.created_at', $month);
    //     } elseif ($timeRange === 'year') {
    //         $totalCostQuery->whereYear('ii.created_at', $year);
    //     }
    //     $totalSummary = $totalCostQuery->first();

    //     // Query tính tổng số lượng
    //     $totalQuantityQuery = DB::table('inventory_details as iid')
    //         ->join('inventories as ii', 'iid.inventory_id', '=', 'ii.id')
    //         ->select(DB::raw('SUM(iid.quantity) as total_quantity'));
    //     if ($timeRange === 'day') {
    //         $totalQuantityQuery->whereBetween('ii.created_at', [$startDate, $endDate]);
    //     } elseif ($timeRange === 'month') {
    //         $totalQuantityQuery->whereYear('ii.created_at', $year)
    //             ->whereMonth('ii.created_at', $month);
    //     } elseif ($timeRange === 'year') {
    //         $totalQuantityQuery->whereYear('ii.created_at', $year);
    //     }
    //     $totalQuantity = $totalQuantityQuery->value('total_quantity') ?? 0;

    //     // Kết hợp summary
    //     $summary = [
    //         'total_cost' => $totalSummary->total_cost ?? 0,
    //         'import_count' => $totalSummary->import_count ?? 0,
    //         'total_quantity' => $totalQuantity
    //     ];

    //     // Dữ liệu cho biểu đồ
    //     $chartDataQuery = DB::table('inventories as ii')
    //         ->select(DB::raw('SUM(ii.total) as total_cost'));
    //     switch ($timeRange) {
    //         case 'day':
    //             $chartDataQuery->addSelect(DB::raw('DATE(ii.created_at) as label'))
    //                 ->whereBetween('ii.created_at', [$startDate, $endDate])
    //                 ->groupBy(DB::raw('DATE(ii.created_at)'))
    //                 ->orderBy('label');
    //             break;
    //         case 'year':
    //             $chartDataQuery->addSelect(DB::raw('MONTH(ii.created_at) as label'))
    //                 ->whereYear('ii.created_at', $year)
    //                 ->groupBy(DB::raw('MONTH(ii.created_at)'))
    //                 ->orderBy('label');
    //             break;
    //         default:
    //             $chartDataQuery->addSelect(DB::raw('DATE(ii.created_at) as label'))
    //                 ->whereYear('ii.created_at', $year)
    //                 ->whereMonth('ii.created_at', $month)
    //                 ->groupBy(DB::raw('DATE(ii.created_at)'))
    //                 ->orderBy('label');
    //             break;
    //     }
    //     $chartData = $chartDataQuery->get();

    //     // Query sản phẩm
    //     $products = Product::whereHas('inventoryDetails', function ($query) use ($timeRange, $startDate, $endDate, $year, $month) {
    //         $query->join('inventories as ii', 'inventory_details.inventory_id', '=', 'ii.id')
    //             ->when($timeRange === 'day', function ($q) use ($startDate, $endDate) {
    //                 $q->whereBetween('ii.created_at', [$startDate, $endDate]);
    //             })
    //             ->when($timeRange === 'month', function ($q) use ($year, $month) {
    //                 $q->whereYear('ii.created_at', $year)
    //                     ->whereMonth('ii.created_at', $month);
    //             })
    //             ->when($timeRange === 'year', function ($q) use ($year) {
    //                 $q->whereYear('ii.created_at', $year);
    //             });
    //     })
    //         ->with(['productVariants']) // Load tất cả variants
    //         ->with(['inventoryDetails' => function ($query) use ($timeRange, $startDate, $endDate, $year, $month) {
    //             $query->join('inventories as ii', 'inventory_details.inventory_id', '=', 'ii.id')
    //                 ->select('inventory_details.*', 'ii.created_at')
    //                 ->when($timeRange === 'day', function ($q) use ($startDate, $endDate) {
    //                     $q->whereBetween('ii.created_at', [$startDate, $endDate]);
    //                 })
    //                 ->when($timeRange === 'month', function ($q) use ($year, $month) {
    //                     $q->whereYear('ii.created_at', $year)
    //                         ->whereMonth('ii.created_at', $month);
    //                 })
    //                 ->when($timeRange === 'year', function ($q) use ($year) {
    //                     $q->whereYear('ii.created_at', $year);
    //                 });
    //         }])
    //         ->paginate(10)
    //         ->appends($request->query());

    //     // Tính toán thống kê cho từng sản phẩm sau khi paginate
    //     foreach ($products as $product) {
    //         // Tính tổng từ inventory_details trong khoảng thời gian
    //         $totalImportedQuantity = $product->inventoryDetails->sum('quantity');
    //         $totalImportedCost = $product->inventoryDetails->sum(function ($detail) {
    //             return $detail->quantity * $detail->price;
    //         });

    //         // Gán thống kê cho sản phẩm
    //         $product->total_imported_quantity = $totalImportedQuantity;
    //         $product->total_imported_cost = $totalImportedCost;

    //         // Vì không có product_variant_id trong inventory_details,
    //         // ta sẽ hiển thị thông tin variant hiện có và ghi chú tổng nhập
    //         foreach ($product->productVariants as $variant) {
    //             // Variant sẽ hiển thị số lượng tồn kho hiện tại
    //             // Thông tin tổng nhập sẽ hiển thị ở cấp sản phẩm
    //             $variant->current_stock = $variant->quantity ?? 0;
    //         }
    //     }

    //     return view('admin.inventory.revenue.revenue-inventory-datetime', [
    //         'summary' => $summary,
    //         'chartData' => $chartData,
    //         'products' => $products, // Vẫn dùng biến 'products' như cũ
    //         'timeRange' => $timeRange,
    //         'currentYear' => $now->year,
    //         'currentMonth' => $now->month,
    //         'startDate' => $startDate,
    //         'endDate' => $endDate,
    //         'selectedYear' => $year,
    //         'selectedMonth' => $month
    //     ]);
    // }


    public function revenueInventoryDatetime(Request $request)
    {
        // Xử lý filter theo thời gian
        $timeRange = $request->input('time_range', 'month');
        $now = Carbon::now();

        $startDate = null;
        $endDate = null;
        $year = $request->input('year', $now->year);
        $month = $request->input('month', $now->month);

        switch ($timeRange) {
            case 'day':
                $startDate = $request->input('start_date', $now->format('Y-m-d'));
                $endDate = $request->input('end_date', $now->format('Y-m-d'));
                break;
            case 'year':
                break;
            default:
                break;
        }

        // Query tính tổng tiền và số phiếu nhập - CHỈ LẤY STATUS APPROVED
        $totalCostQuery = DB::table('inventories as ii')
            ->select(
                DB::raw('SUM(ii.total) as total_cost'),
                DB::raw('COUNT(ii.id) as import_count')
            )
            ->where('ii.status', 'approved'); // Thêm điều kiện này

        if ($timeRange === 'day') {
            $totalCostQuery->whereBetween('ii.created_at', [$startDate, $endDate]);
        } elseif ($timeRange === 'month') {
            $totalCostQuery->whereYear('ii.created_at', $year)
                ->whereMonth('ii.created_at', $month);
        } elseif ($timeRange === 'year') {
            $totalCostQuery->whereYear('ii.created_at', $year);
        }
        $totalSummary = $totalCostQuery->first();

        // Query tính tổng số lượng - CHỈ LẤY STATUS APPROVED
        $totalQuantityQuery = DB::table('inventory_details as iid')
            ->join('inventories as ii', 'iid.inventory_id', '=', 'ii.id')
            ->select(DB::raw('SUM(iid.quantity) as total_quantity'))
            ->where('ii.status', 'approved'); // Thêm điều kiện này

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

        // Dữ liệu cho biểu đồ - CHỈ LẤY STATUS APPROVED
        $chartDataQuery = DB::table('inventories as ii')
            ->select(DB::raw('SUM(ii.total) as total_cost'))
            ->where('ii.status', 'approved'); // Thêm điều kiện này

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
            default:
                $chartDataQuery->addSelect(DB::raw('DATE(ii.created_at) as label'))
                    ->whereYear('ii.created_at', $year)
                    ->whereMonth('ii.created_at', $month)
                    ->groupBy(DB::raw('DATE(ii.created_at)'))
                    ->orderBy('label');
                break;
        }
        $chartData = $chartDataQuery->get();

        // Query sản phẩm - CHỈ LẤY STATUS APPROVED
        $products = Product::whereHas('inventoryDetails', function ($query) use ($timeRange, $startDate, $endDate, $year, $month) {
            $query->join('inventories as ii', 'inventory_details.inventory_id', '=', 'ii.id')
                ->where('ii.status', 'approved') // Thêm điều kiện này
                ->when($timeRange === 'day', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('ii.created_at', [$startDate, $endDate]);
                })
                ->when($timeRange === 'month', function ($q) use ($year, $month) {
                    $q->whereYear('ii.created_at', $year)
                        ->whereMonth('ii.created_at', $month);
                })
                ->when($timeRange === 'year', function ($q) use ($year) {
                    $q->whereYear('ii.created_at', $year);
                });
        })
            ->with(['productVariants'])
            ->with(['inventoryDetails' => function ($query) use ($timeRange, $startDate, $endDate, $year, $month) {
                $query->join('inventories as ii', 'inventory_details.inventory_id', '=', 'ii.id')
                    ->where('ii.status', 'approved') // Thêm điều kiện này
                    ->select('inventory_details.*', 'ii.created_at')
                    ->when($timeRange === 'day', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('ii.created_at', [$startDate, $endDate]);
                    })
                    ->when($timeRange === 'month', function ($q) use ($year, $month) {
                        $q->whereYear('ii.created_at', $year)
                            ->whereMonth('ii.created_at', $month);
                    })
                    ->when($timeRange === 'year', function ($q) use ($year) {
                        $q->whereYear('ii.created_at', $year);
                    });
            }])
            ->paginate(10)
            ->appends($request->query());

        // Tính toán thống kê cho từng sản phẩm sau khi paginate
        foreach ($products as $product) {
            $totalImportedQuantity = $product->inventoryDetails->sum('quantity');
            $totalImportedCost = $product->inventoryDetails->sum(function ($detail) {
                return $detail->quantity * $detail->price;
            });

            $product->total_imported_quantity = $totalImportedQuantity;
            $product->total_imported_cost = $totalImportedCost;

            foreach ($product->productVariants as $variant) {
                $variant->current_stock = $variant->quantity ?? 0;
            }
        }

        return view('admin.inventory.revenue.revenue-inventory-datetime', [
            'summary' => $summary,
            'chartData' => $chartData,
            'products' => $products,
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

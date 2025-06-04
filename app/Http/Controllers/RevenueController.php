<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\RevenueDayExportExcel;
use App\Exports\RevenueMonthExportExcel as ExportsRevenueMonthExportExcel;
use Maatwebsite\Excel\Facades\Excel;
use RevenueMonthExportExcel;

class RevenueController extends Controller
{

    public function revenueDay(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $query = "SELECT DATE(created_at) AS ngaytao, SUM(total) AS tongtien
              FROM orders
              WHERE status = 'Đã thanh toán' OR status = 'Giao hàng thành công'";

        $params = [];

        if ($from && $to) {
            $query .= " AND DATE(created_at) BETWEEN ? AND ?";
            $params = [$from, $to];
        }

        $query .= " GROUP BY DATE(created_at) ORDER BY DATE(created_at)";

        $revenueDay = DB::select($query, $params);

        $day = [];
        $total = [];

        foreach ($revenueDay as $row) {
            $row = (array) $row;
            $day[] = "Ngày " . $row['ngaytao'];
            $total[] = $row['tongtien'];
        }

        return view('admin.revenuestatistics.revenue-day.index', compact('day', 'total', 'from', 'to'));
    }

    private function getRevenueData($from, $to)
    {
        $query = "SELECT DATE(created_at) AS ngaytao, SUM(total) AS tongtien
              FROM orders
              WHERE status = 'Đã thanh toán' OR status = 'Giao hàng thành công'";

        $params = [];

        if ($from && $to) {
            $query .= " AND DATE(created_at) BETWEEN ? AND ?";
            $params = [$from, $to];
        }

        $query .= " GROUP BY DATE(created_at) ORDER BY DATE(created_at)";
        return DB::select($query, $params);
    }


    public function exportPdf(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        if (!$from || !$to) {
            return redirect()->route('admin.revenueDay')->with('error', 'Vui lòng chọn khoảng thời gian trước khi xuất PDF.');
        }


        $data = $this->getRevenueData($from, $to);
        $pdf = Pdf::loadView('admin.export.pdf.revenue.day.export-revenue-pdf', ['data' => $data, 'from' => $from, 'to' => $to]);

        return $pdf->download('doanh_thu_' . now()->format('Ymd_His') . '.pdf');
    }


    public function exportExcel(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        if (!$from || !$to) {
            return redirect()->route('admin.revenueDay')->with('error', 'Vui lòng chọn khoảng thời gian trước khi xuất Excel.');
        }

        $data = collect($this->getRevenueData($from, $to));

        return Excel::download(new RevenueDayExportExcel($data), 'doanh_thu_' . now()->format('Ymd_His') . '.xlsx');
    }




    public function revenueMonth()
    {
        return view('admin.revenuestatistics.revenue-month.index');
    }

    public function revenueMonthApi(Request $request)
    {
        $period = $request->input('period', 'year'); // month | quarter | year
        $selectedYear = $request->input('year');

        // Xác định biểu thức group theo thời gian và định dạng nhãn
        $groupByRaw = match ($period) {
            'month' => 'MONTH(created_at)',
            'quarter' => 'QUARTER(created_at)',
            default => 'YEAR(created_at)',
        };

        $query = DB::table('orders')
            ->selectRaw("{$groupByRaw} AS period, SUM(total) AS total")
            ->where('status', 'Đã thanh toán')
            ->orWhere('status', 'Giao hàng thành công');

        // Lọc theo năm nếu cần
        if ($selectedYear) {
            $query->whereYear('created_at', $selectedYear);
        }

        $results = $query->groupBy('period')
            ->orderBy('period')
            ->get();

        // Chuẩn bị dữ liệu trả về
        $labels = [];
        $totals = [];

        // Tạo đầy đủ các khoảng thời gian, kể cả khoảng không có dữ liệu
        if ($period === 'month' && $selectedYear) {
            // Tạo đủ 12 tháng
            for ($i = 1; $i <= 12; $i++) {
                $labels[$i] = 'Tháng ' . $i;
                $totals[$i] = 0;
            }
        } elseif ($period === 'quarter' && $selectedYear) {
            // Tạo đủ 4 quý
            for ($i = 1; $i <= 4; $i++) {
                $labels[$i] = 'Quý ' . $i;
                $totals[$i] = 0;
            }
        }

        // Điền dữ liệu từ kết quả query
        foreach ($results as $row) {
            $periodKey = $row->period;
            $label = match ($period) {
                'month' => 'Tháng ' . $periodKey,
                'quarter' => 'Quý ' . $periodKey,
                'year' => $periodKey,
            };

            if ($period === 'month' || $period === 'quarter') {
                $totals[$periodKey] = (float)$row->total;
            } else {
                $labels[] = $label;
                $totals[] = (float)$row->total;
            }
        }

        // Chuyển đổi mảng key-value sang mảng tuần tự cho tháng và quý
        if ($period === 'month' || $period === 'quarter') {
            $labels = array_values($labels);
            $totals = array_values($totals);
        }

        return response()->json([
            'labels' => $labels,
            'values' => $totals,
        ]);
    }


    private function getRevenueMonthData($period, $year = null)
{
    $groupByRaw = match ($period) {
        'month' => 'MONTH(created_at)',
        'quarter' => 'QUARTER(created_at)',
        default => 'YEAR(created_at)',
    };

    $query = DB::table('orders')
        ->selectRaw("{$groupByRaw} AS period, SUM(total) AS total")
        ->where('status', 'Đã thanh toán')
        ->orWhere('status', 'Giao hàng thành công');

    if ($year) {
        $query->whereYear('created_at', $year);
    }

    $results = $query->groupBy('period')
        ->orderBy('period')
        ->get();

    $labels = [];
    $totals = [];

    if ($period === 'month' && $year) {
        for ($i = 1; $i <= 12; $i++) {
            $labels[$i] = 'Tháng ' . $i;
            $totals[$i] = 0;
        }
    } elseif ($period === 'quarter' && $year) {
        for ($i = 1; $i <= 4; $i++) {
            $labels[$i] = 'Quý ' . $i;
            $totals[$i] = 0;
        }
    }

    foreach ($results as $row) {
        $periodKey = $row->period;
        if ($period === 'month' || $period === 'quarter') {
            $totals[$periodKey] = (float)$row->total;
        } else {
            $labels[] = $row->period;
            $totals[] = (float)$row->total;
        }
    }

    if ($period === 'month' || $period === 'quarter') {
        $labels = array_values($labels);
        $totals = array_values($totals);
    }

    return [
        'labels' => $labels,
        'totals' => $totals,
    ];
}


public function exportPdfMonth(Request $request)
{
    $period = $request->input('period', 'year'); // month | quarter | year
    $year = $request->input('year');

    if (!$year) {
        return redirect()->route('admin.revenueMonth')->with('error', 'Vui lòng chọn năm để xuất PDF.');
    }

    $data = $this->getRevenueMonthData($period, $year);

    $pdf = Pdf::loadView('admin.export.pdf.revenue.month.export-revenue-pdf', [
        'labels' => $data['labels'],
        'totals' => $data['totals'],
        'period' => $period,
        'year' => $year,
    ]);

    return $pdf->download('doanh_thu_' . $period . '_' . $year . '_' . now()->format('Ymd_His') . '.pdf');
}


public function exportExcelMonth(Request $request)
{
    $period = $request->input('period', 'year'); // month | quarter | year
    $year = $request->input('year');

    if (!$year) {
        return redirect()->route('admin.revenueMonth')->with('error', 'Vui lòng chọn năm để xuất Excel.');
    }

    $data = $this->getRevenueMonthData($period, $year);

    // Tạo một class Export Excel tương tự RevenueDayExportExcel nhưng cho dữ liệu tháng
    return Excel::download(new ExportsRevenueMonthExportExcel($data), 'doanh_thu_' . $period . '_' . $year . '_' . now()->format('Ymd_His') . '.xlsx');
}



    public function revenueYear()
    {
        $revenueYear = DB::select("SELECT YEAR(created_at) AS namtao, SUM(total) AS tongtien
                                    FROM orders
                                    WHERE status = 'Đã thanh toán' OR status = 'Giao hàng thành công'
                                    GROUP BY YEAR(created_at)
                                    ORDER BY YEAR(created_at)");
        $year = [];
        $total = [];
        if (!empty($revenueYear)) {
            foreach ($revenueYear as $row) {
                $row = (array) $row; // Ép kiểu đối tượng thành mảng assoc
                $year[] = "Năm " . $row['namtao'];
                $total[] = $row['tongtien'];
            }
        } else {
            echo "Không có dữ liệu doanh thu theo năm.";
        }
        return view('admin.revenuestatistics.revenue-year.index', compact('year', 'total'));
    }

    // public function profitYear()
    // {
    //     // khó điên truy vấn dữ liệu
    //     $profitYear = Order::selectRaw('YEAR(created_at) AS nam, SUM(total) AS doanhthu')
    //         ->where('status', 'Đã thanh toán')
    //         ->groupByRaw('YEAR(created_at)')
    //         ->orderByRaw('YEAR(created_at)')
    //         ->get()
    //         ->map(function ($order) {
    //             $chiphi = Inventory::whereYear('created_at', $order->nam)->sum('total');
    //             return [
    //                 'nam' => $order->nam,
    //                 'doanhthu' => $order->doanhthu,
    //                 'chiphi' => $chiphi ?? 0,
    //                 'loiNhuan' => $order->doanhthu - ($chiphi ?? 0),
    //             ];
    //         });

    //     // Chuẩn bị dữ liệu cho biểu đồ
    //     $nam = $profitYear->pluck('nam')->map(fn($y) => "Năm $y")->toArray();
    //     $loiNhuan = $profitYear->pluck('loiNhuan')->toArray();

    //     return view('admin.revenuestatistics.profit.index', compact('nam', 'loiNhuan'));
    // }




    public function profitYear(Request $request)
    {
        // Lấy các tham số lọc từ request
        $filterType = $request->get('filter_type', 'year'); // year, month, date_range
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $selectedMonth = $request->get('selected_month');
        $selectedYear = $request->get('selected_year', date('Y'));

        // Khởi tạo query cơ bản
        $orderQuery = Order::where('status', 'Đã thanh toán')->orWhere('status', 'Giao hàng thành công');
        $inventoryQuery = Inventory::query();

        $labels = [];
        $profitData = [];

        switch ($filterType) {
            case 'date_range':
                if ($fromDate && $toDate) {
                    // Lọc theo khoảng ngày
                    $profitData = $this->getProfitByDateRange($fromDate, $toDate);
                    $labels = $profitData->pluck('label')->toArray();
                } else {
                    // Mặc định lấy 30 ngày gần nhất
                    $fromDate = now()->subDays(30)->format('Y-m-d');
                    $toDate = now()->format('Y-m-d');
                    $profitData = $this->getProfitByDateRange($fromDate, $toDate);
                    $labels = $profitData->pluck('label')->toArray();
                }
                break;

            case 'month':
                if ($selectedMonth && $selectedYear) {
                    // Lọc theo tháng cụ thể
                    $profitData = $this->getProfitByMonth($selectedYear, $selectedMonth);
                    $labels = $profitData->pluck('label')->toArray();
                } else {
                    // Lấy 12 tháng của năm hiện tại
                    $profitData = $this->getProfitByMonthsInYear($selectedYear);
                    $labels = $profitData->pluck('label')->toArray();
                }
                break;

            case 'year':
            default:
                // Lọc theo năm
                $profitData = $this->getProfitByYear();
                $labels = $profitData->pluck('label')->toArray();
                break;
        }

        $loiNhuan = $profitData->pluck('loiNhuan')->toArray();

        // Lấy danh sách năm để hiển thị trong dropdown
        $availableYears = Order::selectRaw('DISTINCT YEAR(created_at) as year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.revenuestatistics.profit.index', compact(
            'labels',
            'loiNhuan',
            'profitData',
            'filterType',
            'fromDate',
            'toDate',
            'selectedMonth',
            'selectedYear',
            'availableYears'
        ));
    }

    private function getProfitByDateRange($fromDate, $toDate)
    {
        return Order::selectRaw('DATE(created_at) AS ngay, SUM(total) AS doanhthu')
            ->where('status', 'Đã thanh toán')
            ->orWhere('status', 'Giao hàng thành công')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at)')
            ->get()
            ->map(function ($order) {
                $chiphi = Inventory::whereDate('created_at', $order->ngay)->sum('total');
                return [
                    'label' => date('d/m/Y', strtotime($order->ngay)),
                    'ngay' => $order->ngay,
                    'doanhthu' => $order->doanhthu,
                    'chiphi' => $chiphi ?? 0,
                    'loiNhuan' => $order->doanhthu - ($chiphi ?? 0),
                ];
            });
    }

    private function getProfitByMonth($year, $month)
    {
        return Order::selectRaw('DAY(created_at) AS ngay, SUM(total) AS doanhthu')
            ->where('status', 'Đã thanh toán')
            ->orWhere('status', 'Giao hàng thành công')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupByRaw('DAY(created_at)')
            ->orderByRaw('DAY(created_at)')
            ->get()
            ->map(function ($order) use ($year, $month) {
                $chiphi = Inventory::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->whereDay('created_at', $order->ngay)
                    ->sum('total');
                return [
                    'label' => "Ngày {$order->ngay}",
                    'ngay' => $order->ngay,
                    'doanhthu' => $order->doanhthu,
                    'chiphi' => $chiphi ?? 0,
                    'loiNhuan' => $order->doanhthu - ($chiphi ?? 0),
                ];
            });
    }

    private function getProfitByMonthsInYear($year)
    {
        return Order::selectRaw('MONTH(created_at) AS thang, SUM(total) AS doanhthu')
            ->where('status', 'Đã thanh toán')
            ->orWhere('status', 'Giao hàng thành công')
            ->whereYear('created_at', $year)
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get()
            ->map(function ($order) use ($year) {
                $chiphi = Inventory::whereYear('created_at', $year)
                    ->whereMonth('created_at', $order->thang)
                    ->sum('total');
                return [
                    'label' => "Tháng {$order->thang}",
                    'thang' => $order->thang,
                    'doanhthu' => $order->doanhthu,
                    'chiphi' => $chiphi ?? 0,
                    'loiNhuan' => $order->doanhthu - ($chiphi ?? 0),
                ];
            });
    }

    private function getProfitByYear()
    {
        return Order::selectRaw('YEAR(created_at) AS nam, SUM(total) AS doanhthu')
            ->where('status', 'Đã thanh toán')
            ->orWhere('status', 'Giao hàng thành công')
            ->groupByRaw('YEAR(created_at)')
            ->orderByRaw('YEAR(created_at)')
            ->get()
            ->map(function ($order) {
                $chiphi = Inventory::whereYear('created_at', $order->nam)->sum('total');
                return [
                    'label' => "Năm {$order->nam}",
                    'nam' => $order->nam,
                    'doanhthu' => $order->doanhthu,
                    'chiphi' => $chiphi ?? 0,
                    'loiNhuan' => $order->doanhthu - ($chiphi ?? 0),
                ];
            });
    }



}

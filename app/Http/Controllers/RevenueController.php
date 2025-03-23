<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    public function revenueDay()
    {
        $revenueDay = DB::select(" SELECT DATE(created_at) AS ngaytao, SUM(total) AS tongtien 
                                    FROM orders 
                                    WHERE status = 'Đã thanh toán'
                                    GROUP BY DATE(created_at) 
                                    ORDER BY DATE(created_at)");
        //  dd($revenueDay);

        $day = [];
        $total = [];
        if (!empty($revenueDay)) {
            // Duyệt mảng kết quả theo hàm lấy doanh thu theo ngày
            foreach ($revenueDay as $row) {
                $row = (array) $row; // Ép kiểu đối tượng thành mảng assoc
                $day[] = "Ngày " . $row['ngaytao'];
                $total[] = $row['tongtien'];
            }
        } else {
            echo "Không có dữ liệu doanh thu theo ngày.";
        }
        return view('admin.revenuestatistics.revenue-day.index', compact('day', 'total'));
    }


    public function revenueMonth()
    {
        $revenuemonth = DB::select("SELECT YEAR(created_at) AS namtao, MONTH(created_at) AS thangtao, SUM(total) AS tongtien 
                                    FROM orders 
                                    WHERE status = 'Đã thanh toán'
                                    GROUP BY YEAR(created_at), MONTH(created_at) 
                                    ORDER BY YEAR(created_at), MONTH(created_at)");
        $month = [];
        $total = [];
        if (!empty($revenuemonth)) {
            foreach ($revenuemonth as $row) {
                $row = (array) $row; // Ép kiểu đối tượng thành mảng assoc
                $month[] = "Tháng " . $row['thangtao'] . " - " . $row['namtao'];
                $total[] = $row['tongtien'];
            }
        } else {
            echo "Không có dữ liệu doanh thu theo tháng.";
        }
        return view('admin.revenuestatistics.revenue-month.index', compact('month', 'total'));
    }

    public function revenueYear()
    {
        $revenueYear = DB::select("SELECT YEAR(created_at) AS namtao, SUM(total) AS tongtien 
                                    FROM orders 
                                    WHERE status = 'Đã thanh toán'
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

    public function profitYear()
    {
        // khó điên truy vấn dữ liệu
        $profitYear = Order::selectRaw('YEAR(created_at) AS nam, SUM(total) AS doanhthu')
            ->where('status', 'Đã thanh toán')
            ->groupByRaw('YEAR(created_at)')
            ->orderByRaw('YEAR(created_at)')
            ->get()
            ->map(function ($order) {
                $chiphi = Inventory::whereYear('created_at', $order->nam)->sum('total');
                return [
                    'nam' => $order->nam,
                    'doanhthu' => $order->doanhthu,
                    'chiphi' => $chiphi ?? 0,
                    'loiNhuan' => $order->doanhthu - ($chiphi ?? 0),
                ];
            });
    
        // Chuẩn bị dữ liệu cho biểu đồ
        $nam = $profitYear->pluck('nam')->map(fn($y) => "Năm $y")->toArray();
        $loiNhuan = $profitYear->pluck('loiNhuan')->toArray();
    
        return view('admin.revenuestatistics.profit.index', compact('nam', 'loiNhuan'));
    }
}

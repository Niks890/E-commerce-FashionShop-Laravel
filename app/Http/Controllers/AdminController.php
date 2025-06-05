<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public  function dashboard()
    {
        $revenueByMonth = [];
        for ($i = 1; $i <= 12; $i++) {
            // Sử dụng Carbon để lấy tên tháng theo định dạng 'M' (Jan, Feb,...)
            $monthName = Carbon::create(null, $i, 1)->format('M');
            $revenueByMonth[$monthName] = 0.0; // Khởi tạo với giá trị 0.0
        }

        // 2. Lấy dữ liệu doanh thu thực tế từ database cho năm hiện tại
        $currentYear = now()->year;

        $dbResults = DB::table('orders')
            ->select(
                DB::raw('MONTH(created_at) as month_number'),
                DB::raw('SUM(total) as monthly_revenue')
            )
            ->whereYear('created_at', $currentYear)
            ->where(function ($query) {
                $query->where('status', 'Đã thanh toán')
                    ->orWhere('status', 'Giao hàng thành công');
            })
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        // 3. Ghi đè các giá trị doanh thu thực tế vào mảng $revenueByMonth
        foreach ($dbResults as $row) {
            $monthName = Carbon::create(null, $row->month_number, 1)->format('M');
            $revenueByMonth[$monthName] = (float) $row->monthly_revenue;
        }

        // 4. Tính toán tổng doanh thu từ mảng đã chuẩn bị
        $total = array_values($revenueByMonth);
        // dd($total);


        $staffQuantity = DB::table('staff')->count();
        $customerQuantity = DB::table('customers')->count();
        $productQuantity = DB::table('products')->count();
        $orderQuantity = DB::table('orders')->where('status', 'Chờ xử lý')->count();


        $orderAssign = Order::where('status', 'Đã gửi cho đơn vị vận chuyển')
        ->where('staff_delivery_id', auth()->user()->id - 1)
        ->count();

        $orderProcessing = Order::where('status', 'Đang giao hàng')
        ->where('staff_delivery_id', auth()->user()->id - 1)
        ->count();

        $orderSuccess = Order::where('status', 'Giao hàng thành công')
        ->where('staff_delivery_id', auth()->user()->id - 1)
        ->count();

        $orderPending = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->whereIn('o.status', ['Chờ xử lý'])
            ->orderBy('o.id', 'DESC')
            ->select('o.*', 'c.name as customer_name')
            ->paginate(5);

        $productOutOfStock = DB::table('product_variants as pv')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->where('pv.stock', 0)
            ->count();
        return view('admin.dashboard', compact('staffQuantity', 'customerQuantity', 'productQuantity', 'orderQuantity', 'revenueByMonth', 'productOutOfStock', 'orderPending', 'total', 'orderAssign', 'orderProcessing', 'orderSuccess'));
    }

    public function login()
    {
        return view('admin.login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function username()
    {
        $login = request()->input('login');
        return filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    }

    public function post_login(Request $request)
    {
        $request->validate([
            'login' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = DB::table('users')
                        ->where('username', $value)
                        ->orWhere('email', $value)
                        ->exists();

                    if (!$exists) {
                        $fail("Username hoặc Email không tồn tại.");
                    }
                }
            ],
            'password' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    // Lấy user theo login
                    $user = DB::table('users')
                        ->where('username', $request->login)
                        ->orWhere('email', $request->login)
                        ->first();

                    if (!$user || !Hash::check($value, $user->password)) {
                        $fail("Mật khẩu không chính xác.");
                    }
                }
            ]
        ]);
        $credentials = [
            $this->username() => $request->input('login'),
            'password' => $request->input('password'),
        ];
        if (auth()->attempt($credentials)) {
            return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công!');
        }
        return redirect()->back();
    }
}

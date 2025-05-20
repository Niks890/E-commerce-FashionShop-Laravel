<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public  function dashboard()
    {

        $months = collect(range(1, 12))->map(function ($month) {
            return DB::table(DB::raw("(SELECT $month as month) as m"));
        })->reduce(function ($query, $builder) {
            return $query ? $query->unionAll($builder) : $builder;
        });

        // Gói truy vấn subquery lại
        $subquery = DB::query()->fromSub($months, 'months');

        $results = DB::query()
            ->fromSub($subquery, 'months')
            ->leftJoin('orders as o', function ($join) {
                $join->on(DB::raw('MONTH(o.created_at)'), '=', 'months.month')
                    ->where(DB::raw('YEAR(o.created_at)'), '=', now()->year)
                    ->where('o.status', '=', 'Đã thanh toán');
            })
            ->select(
                'months.month',
                DB::raw('COALESCE(SUM(o.total), 0) as revenue')
            )
            ->groupBy('months.month')
            ->orderBy('months.month')
            ->get();

        $revenueByMonth = $results->mapWithKeys(function ($row) {
            $monthName = date('M', mktime(0, 0, 0, $row->month, 1)); // 'Jan', 'Feb', ...
            return [$monthName => (float) $row->revenue];
        })->toArray();

        $total = array_values($revenueByMonth); // mảng doanh thu thuần không chứa key


        $staffQuantity = DB::table('staff')->count();
        $customerQuantity = DB::table('customers')->count();
        $productQuantity = DB::table('products')->count();
        $orderQuantity = DB::table('orders')->where('status', 'Chờ xử lý')->count();

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
        return view('admin.dashboard', compact('staffQuantity', 'customerQuantity', 'productQuantity', 'orderQuantity', 'revenueByMonth', 'productOutOfStock', 'orderPending', 'total'));
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

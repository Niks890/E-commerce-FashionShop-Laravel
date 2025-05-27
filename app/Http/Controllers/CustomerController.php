<?php

namespace App\Http\Controllers;

use App\Mail\OrderCancellationMail;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class CustomerController extends Controller
{
    public function login()
    {
        return view('sites.login');
    }

    public function post_login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password_login' => 'required|min:6',
        ], [
            'login.required' => 'Vui lòng nhập email hoặc username.',
            'password_login.required' => 'Vui lòng nhập mật khẩu.',
            'password_login.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $request->login,
            'password'  => $request->password_login
        ];

        if (Auth::guard('customer')->attempt($credentials)) {
            if (Session::has('auth')) {
                Session::forget('auth');
                return redirect()->route('sites.cart');
            }
            return redirect()->route('sites.home')->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors(['login' => 'Email, Username hoặc mật khẩu không đúng.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        // $request->session()->invalidate(); // Xóa toàn bộ session
        $request->session()->regenerateToken();

        return redirect()->route('user.login');
    }

    public function register()
    {
        return redirect()->route('user.login');
    }

    public function post_register(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|max:200',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|min:6|max:200',
            're_password' => 'required|same:password',
        ], [
            'name.required' => 'Họ và tên không được để trống.',
            'email.required' => 'Vui lòng nhập email của bạn.',
            'email.email' => 'Vui lòng nhập email hợp lệ.',
            'email.unique' => 'Email này đã tồn tại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            're_password.required' => 'Vui lòng nhập lại mật khẩu.',
            're_password.same' => 'Mật khẩu xác nhận không khớp.',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return view('sites.success.register', compact('customer'));
    }

    public function profile(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        return view('sites.profile', compact('customer'));
    }

    public function update_profile(Request $request, Customer $customer)
    {
        // dd($customer);
        //   dd($request->all());
        $request->validate([
            'name' => 'required|min:3|max:200',
            'email' => 'required|email',
            'new_password' => 'min:6|max:200',
            'phone' => 'required',
            'address' => 'required'
        ], [
            'name.required' => 'Họ và tên không được để trống.',
            'email.required' => 'Vui lòng nhập email của bạn.',
            'email.email' => 'Vui lòng nhập email hợp lệ.',
            'new_password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'phone.required' => 'Số điện thoại không được để trống',
            'address.required' => 'Địa chỉ không được để trống'
        ]);


        $customer->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => bcrypt($request->new_password)
        ]);

        return redirect()->route('user.profile')->with('updateprofile', 'Cập nhật hồ sơ thành công!');
    }


    public function checkLogin(Request $request)
    {
        Session::put('auth', $request->auth);
    }



    public function getHistoryOrderOfCustomer()
    {
        if (Auth::guard('customer')->check()) {
            $customer_id = Auth::guard('customer')->user()->id;

            $query = DB::table('orders as o')
                ->join('customers as c', 'o.customer_id', '=', 'c.id')
                ->where('o.customer_id', $customer_id)
                ->select('o.*', 'c.name as customer_name');

            // Lọc theo từ khóa tìm kiếm (ID hoặc số điện thoại)
            if (request()->has('query') && request()->query('query') != '') {
                $searchQuery = request()->query('query');
                $query->where(function ($q) use ($searchQuery) {
                    if (is_numeric($searchQuery)) {
                        $q->where('o.id', $searchQuery)
                            ->orWhere('o.phone', 'like', "%$searchQuery%");
                    } else {
                        $q->where('o.phone', 'like', "%$searchQuery%");
                    }
                });
            }

            // Lọc theo trạng thái
            if (request()->has('status') && request()->query('status') != '') {
                $status = request()->query('status');
                $query->where('o.status', $status);
            }

            // Lọc theo ngày
            if (request()->has('date_from') && request()->query('date_from') != '') {
                $dateFrom = request()->query('date_from');
                $query->whereDate('o.created_at', '>=', $dateFrom);
            }

            if (request()->has('date_to') && request()->query('date_to') != '') {
                $dateTo = request()->query('date_to');
                $query->whereDate('o.created_at', '<=', $dateTo);
            }

            // Lấy danh sách trạng thái để hiển thị trong dropdown
            $statusList = DB::table('orders')
                ->where('customer_id', $customer_id)
                ->select('status')
                ->distinct()
                ->pluck('status');

            $historyOrder = $query->orderBy('o.id', 'DESC')->paginate(5);

            // Giữ lại các tham số filter khi phân trang
            $historyOrder->appends(request()->query());

            return view('sites.customer.order_history', compact('historyOrder', 'statusList'));
        }

        return redirect()->route('login');
    }

    public function showOrderDetailOfCustomer(Order $order)
    {
        $orderDetail = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->join('order_details as od', 'o.id', '=', 'od.order_id')
            ->join('product_variants as pv', 'pv.id', '=', 'od.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->where('o.id', $order->id)
            ->select('o.*', 'c.name as customer_name', 'p.product_name as product_name', 'p.image', 'pv.size', 'pv.color', 'od.quantity', 'od.price', 'od.code')
            ->get();

        return view('sites.customer.order_detail', compact('orderDetail'));
    }



    public function cancelOrder(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $order = Order::findOrFail($id);
            $order->status = 'Đã huỷ đơn hàng';
            $order->reason = $request->reason;
            $order->save();

            // Lấy danh sách chi tiết đơn hàng
            $orderDetails = OrderDetail::where('order_id', $order->id)->get();

            // Cộng ngược lại số lượng vào kho
            foreach ($orderDetails as $detail) {
                $variant = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)
                    ->lockForUpdate()
                    ->first();

                if ($variant) {
                    $variant->stock += $detail->quantity;
                    $variant->save();
                }
            }

            // Gửi email xác nhận hủy đơn hàng
            try {
                Mail::to($order->email)->queue(new OrderCancellationMail($order));
                Log::info('Email xác nhận hủy đơn hàng đã được đưa vào queue cho khách hàng: ' . $order->email . ' với đơn hàng ID: ' . $order->id);
            } catch (\Exception $mailException) {
                Log::error('Lỗi khi gửi email xác nhận hủy đơn hàng cho khách hàng: ' . $order->email . ' với đơn hàng ID: ' . $order->id . '. Lỗi: ' . $mailException->getMessage());
            }

            DB::commit();
            return response()->json(['message' => 'Hủy đơn hàng thành công!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi hủy đơn hàng ID: ' . $id . '. Lỗi: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra, vui lòng thử lại!'], 500);
        }
    }





    //ADMIN CUSTOMER

    // public function index(Request $request)
    // {
    //     $query = Customer::query();

    //     // Đếm số đơn hàng cho mỗi khách hàng
    //     $query->withCount('orders');

    //     // Tìm kiếm theo tên, email, số điện thoại
    //     if ($request->filled('search')) {
    //         $search = $request->get('search');
    //         $query->where(function ($q) use ($search) {
    //             $q->where('name', 'LIKE', "%{$search}%")
    //                 ->orWhere('email', 'LIKE', "%{$search}%")
    //                 ->orWhere('phone', 'LIKE', "%{$search}%");
    //         });
    //     }

    //     // Lọc theo ngày tạo
    //     if ($request->filled('from_date')) {
    //         $query->whereDate('created_at', '>=', $request->get('from_date'));
    //     }

    //     if ($request->filled('to_date')) {
    //         $query->whereDate('created_at', '<=', $request->get('to_date'));
    //     }

    //     // Lọc theo số đơn hàng
    //     if ($request->filled('order_count')) {
    //         $orderCount = $request->get('order_count');

    //         switch ($orderCount) {
    //             case '0':
    //                 $query->having('orders_count', '=', 0);
    //                 break;
    //             case '1-5':
    //                 $query->having('orders_count', '>=', 1)
    //                     ->having('orders_count', '<=', 5);
    //                 break;
    //             case '6-10':
    //                 $query->having('orders_count', '>=', 6)
    //                     ->having('orders_count', '<=', 10);
    //                 break;
    //             case '11+':
    //                 $query->having('orders_count', '>', 10);
    //                 break;
    //         }
    //     }

    //     // Sắp xếp và phân trang
    //     $customers = $query->orderBy('id', 'DESC')->paginate(5);

    //     return view('admin.customer.index', compact('customers'));
    // }


    public function index(Request $request)
    {
        $query = Customer::query();

        // Đếm số đơn hàng và voucher cho mỗi khách hàng
        // $query->withCount(['orders', 'vouchers']);
        $query->withCount(['orders']);

        // Tìm kiếm theo tên, email, số điện thoại
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Lọc theo ngày tạo
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->get('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->get('to_date'));
        }

        // Lọc theo số đơn hàng
        if ($request->filled('order_count')) {
            $orderCount = $request->get('order_count');

            switch ($orderCount) {
                case '0':
                    $query->having('orders_count', '=', 0);
                    break;
                case '1-5':
                    $query->having('orders_count', '>=', 1)
                        ->having('orders_count', '<=', 5);
                    break;
                case '6-10':
                    $query->having('orders_count', '>=', 6)
                        ->having('orders_count', '<=', 10);
                    break;
                case '11+':
                    $query->having('orders_count', '>', 10);
                    break;
            }
        }

        // // Lọc theo voucher đã tặng
        // if ($request->filled('voucher_status')) {
        //     $voucherStatus = $request->get('voucher_status');

        //     if ($voucherStatus === 'has_voucher') {
        //         $query->having('vouchers_count', '>', 0);
        //     } elseif ($voucherStatus === 'no_voucher') {
        //         $query->having('vouchers_count', '=', 0);
        //     }
        // }

        // Sắp xếp theo số đơn hàng
        if ($request->filled('order_sort')) {
            $orderSort = $request->get('order_sort');

            if ($orderSort === 'most_orders') {
                $query->orderBy('orders_count', 'DESC')->paginate(5);
            } elseif ($orderSort === 'least_orders') {
                $query->orderBy('orders_count', 'ASC')->paginate(5);
            }
        } else {
            $query->orderBy('id', 'DESC')->paginate(5);
        }

        $customers = $query->paginate(5);

        return view('admin.customer.index', compact('customers'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
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
        if (request()->has('query') && request()->query('query') != '') {
            $query = request()->query('query');

            $historyOrder = DB::table('orders as o')
                ->join('customers as c', 'o.customer_id', '=', 'c.id')
                ->orderBy('o.id', 'ASC')
                ->where('o.customer_id', Auth::guard('customer')->user()->id)
                ->where(function ($q) use ($query) {
                    if (is_numeric($query)) {
                        $q->where('o.id', $query) // Tìm chính xác theo ID
                            ->orWhere('o.phone', 'like', "$query"); // Tìm theo số điện thoại
                    } else {
                        $q->where('o.phone', 'like', "$query");
                    }
                })
                ->select('o.*', 'c.name as customer_name')
                ->paginate(5);

            return view('sites.customer.order_history', compact('historyOrder'));
        } else {
            if (Auth::guard('customer')->check()) {
                $customer_id = Auth::guard('customer')->user()->id;
                $historyOrder = DB::table('orders as o')
                    ->join('customers as c', 'o.customer_id', '=', 'c.id')
                    ->orderBy('o.id', 'ASC')
                    ->where('o.customer_id', $customer_id)
                    ->select('o.*', 'c.name as customer_name')
                    ->paginate(3);
            }
            return view('sites.customer.order_history', compact('historyOrder'));
        }
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


    public function searchOrderHistory() {}



    // public function cancelOrder(Request $request, $id)
    // {
    //     try {
    //         $order = Order::findOrFail($id);
    //         $order->status = 'Đã huỷ đơn hàng';
    //         $order->reason = $request->reason;
    //         $order->save();
    //         return response()->json(['message' => 'Hủy đơn hàng thành công!']);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'Có lỗi xảy ra, vui lòng thử lại!'], 500);
    //     }
    // }


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

            DB::commit();
            return response()->json(['message' => 'Hủy đơn hàng thành công!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Có lỗi xảy ra, vui lòng thử lại!'], 500);
        }
    }
}

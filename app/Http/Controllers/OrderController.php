<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;


use SebastianBergmann\CodeCoverage\Report\Xml\Totals;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->whereIn('o.status', ['Chờ xử lý', 'Đã huỷ đơn hàng']) // Sửa lại đúng logic
            ->orderBy('o.id', 'DESC')
            ->select('o.*', 'c.name as customer_name')
            ->paginate(5);

        return view('admin.order.order_pending', compact('data'));
    }


    public function orderApproval()
    {
        $data = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->where('o.status', 'Đã xử lý')
            ->orderBy('o.id', 'DESC')
            ->select('o.*', 'c.name as customer_name')
            ->paginate(5);
        return view('admin.order.order_approved', compact('data'));
    }

    public function orderSuccess()
    {
        $data = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->where('o.status', 'Đã thanh toán')
            ->orderBy('o.id', 'DESC')
            ->select('o.*', 'c.name as customer_name')
            ->paginate(5);
        return view('admin.order.order_success', compact('data'));
    }


    public function exportInvoice($id)
    {
        $orderDetail = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->join('order_details as od', 'o.id', '=', 'od.order_id')
            ->join('product_variants as pv', 'pv.id', '=', 'od.product_variant_id') // Thay đổi JOIN này
            ->join('products as p', 'p.id', '=', 'pv.product_id') // Lấy sản phẩm từ product_variants
            ->where('o.id', $id)
            ->select(
                'o.*',
                'c.name as customer_name',
                'c.email',
                'p.product_name',
                'p.id as product_id',
                'p.image',
                'od.quantity',
                'od.price',
                'od.code',
                'pv.size',
                'pv.color'
            )
            ->get();

        if ($orderDetail->isEmpty()) {
            return redirect()->back()->with('error', 'Đơn hàng không tồn tại!');
        }

        $pdf = Pdf::loadView('sites.export.pdf.invoice', compact('orderDetail'));
        return $pdf->download('invoice_order_' . $id . '.pdf');
    }





    /**
     * Store a newly created resource in storage.
     */

    // Hàm gốc
    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'address' => 'required',
    //         'phone' => 'required',
    //         'shipping_fee' => 'required|numeric',
    //         'total' => 'required|numeric',
    //         'note' => 'required',
    //         'receiver_name' => 'required',
    //         'email' => 'required|email',
    //         'VAT' => 'required|numeric',
    //         'customer_id' => 'required',
    //         'payment' => 'required',
    //     ], [
    //         'address.required' => 'Vui lòng nhập điểm giao hàng',
    //         'phone.required' => 'Vui lòng nhập số điện thoại',
    //         'note.required' => 'Vui lòng nhập ghi chú',
    //         'receiver_name.required' => 'Vui lòng nhập tên người nhận',
    //         'email.required' => 'Vui lòng nhập email hợp lệ',
    //     ]);
    //     // dd($request->all());
    //     // Tạo đơn hàng
    //     $order = new Order();
    //     $order->address = $data['address'];
    //     $order->phone = $data['phone'];
    //     $order->shipping_fee = $data['shipping_fee'];
    //     $order->total = $data['total'];
    //     $order->note = $data['note'];
    //     $order->receiver_name = $data['receiver_name'];
    //     $order->email = $data['email'];
    //     $order->VAT = $data['VAT'];
    //     $order->payment = $data['payment'];
    //     $order->customer_id = $data['customer_id'];
    //     $order->save();

    //     // Lấy giỏ hàng từ session
    //     $cart = session('cart', []);

    //     // Lọc ra các sản phẩm đã được chọn (checked = true)
    //     $selectedItems = array_filter($cart, function ($item) {
    //         return !empty($item->checked) && $item->checked;
    //     });

    //     if (empty($selectedItems)) {
    //         return redirect()->back()->with('error', 'Không có sản phẩm nào được chọn để thanh toán.');
    //     }

    //     // Tạo chi tiết đơn hàng từ các sản phẩm đã chọn
    //     foreach ($selectedItems as $item) {
    //         OrderDetail::create([
    //             'order_id' => $order->id,
    //             'product_id' => $item->id,
    //             'product_variant_id' => $item->product_variant_id,
    //             'quantity' => $item->quantity,
    //             'price' => $item->price,
    //             'size_and_color' => $item->size . '-' . $item->color,
    //             'code' => session('percent_discount', 0),
    //         ]);
    //     }

    //     // Nếu tất cả sản phẩm trong giỏ đều đã chọn, xóa toàn bộ giỏ hàng
    //     if (count($selectedItems) === count($cart)) {
    //         session()->forget('cart');
    //     } else {
    //         // Cập nhật lại giỏ hàng chỉ giữ lại sản phẩm chưa chọn
    //         $cart = array_filter($cart, function ($item) {
    //             return empty($item->checked) || !$item->checked;
    //         });
    //         session(['cart' => $cart]);
    //     }

    //     session()->forget('percent_discount');

    //     // Xử lý trừ đi số lượng sản phẩm trong kho theo số lượng đã được đặt
    //     $orderDetails = OrderDetail::where('order_id', $order->id)->get();

    //     foreach ($orderDetails as $detail) {
    //         // Tách size và color từ chuỗi size_and_color
    //         [$size, $color] = array_map('trim', explode('-', $detail->size_and_color));

    //         // Tìm đúng variant của sản phẩm trong bảng variant theo product_id, size và color
    //         $variant = ProductVariant::where('product_id', $detail->product_id)
    //             ->where('size', trim($size))
    //             ->where('color', trim($color))
    //             ->first();


    //         if ($variant) {
    //             $variant->stock -= $detail->quantity;
    //             $variant->save();

    //         } else {
    //             // Xử lý trường hợp không tìm thấy variant
    //             Log::warning("Không tìm thấy variant cho sản phẩm ID: {$detail->product_id}, size: {$size}, color: {$color}");
    //         }
    //     }
    //     Session::put('success_data', [
    //         'logo' => 'cod.png',
    //         'receiver_name' => $order->receiver_name,
    //         'order_id' => $order->id,
    //         'total' => $order->total
    //     ]);
    //     Session::forget('order_data'); // Xóa session sau khi lưu vào db
    //     return redirect()->route('sites.success.payment');

    //     // return redirect()->route('sites.home')->with('success', "Đặt hàng thành công!");
    // }


    // Pessimistic Lock (Khóa bi quan) là kiểu khóa mà khi một bản ghi đang được truy cập (đọc/ghi), nó sẽ bị khóa lại để ngăn chặn các giao dịch khác đọc hoặc sửa đổi.
    // Trong Laravel, ->lockForUpdate() sẽ khóa bản ghi được chọn cho đến khi transaction kết thúc. Điều này đảm bảo không có giao dịch nào khác có thể thay đổi dữ liệu trong khi nó đang được xử lý.
    // Xử lý lưu đơn hàng (bằng transaction và khoá Pessimistic Lock)
    public function store(Request $request)
    {
        $data = $request->validate([
            'address' => 'required',
            'phone' => 'required',
            'shipping_fee' => 'required|numeric',
            'total' => 'required|numeric',
            'note' => 'required',
            'receiver_name' => 'required',
            'email' => 'required|email',
            'VAT' => 'required|numeric',
            'customer_id' => 'required',
            'payment' => 'required',
        ], [
            'address.required' => 'Vui lòng nhập điểm giao hàng',
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'note.required' => 'Vui lòng nhập ghi chú',
            'receiver_name.required' => 'Vui lòng nhập tên người nhận',
            'email.required' => 'Vui lòng nhập email hợp lệ',
        ]);

        $errors = [];
        DB::beginTransaction();
        try {
            // Lấy giỏ hàng từ session
            $cart = session('cart', []);
            // Lọc ra các sản phẩm đã được chọn (checked = true)
            $selectedItems = array_filter($cart, function ($item) {
                return !empty($item->checked) && $item->checked;
            });

            if (empty($selectedItems)) {
                return redirect()->route('sites.cart')->with('error', 'Không có sản phẩm nào được chọn để thanh toán.');
            }

            // Kiểm tra tồn kho và loại bỏ sản phẩm hết hàng
            foreach ($selectedItems as $key => $item) {
                // Tìm đúng variant của sản phẩm trong bảng variant
                $variant = ProductVariant::where('product_id', $item->id)
                    ->where('size', trim($item->size))
                    ->where('color', trim($item->color))
                    ->lockForUpdate()
                    ->first();

                if (!$variant || $variant->stock < $item->quantity) {
                    // Xóa sản phẩm hết hàng khỏi giỏ
                    unset($cart[$key]);
                    $errors[] = 'Sản phẩm "' . $item->product_name . '" đã hết hàng và đã bị xóa khỏi giỏ hàng.';
                }
            }

            // Nếu có lỗi, cập nhật lại giỏ hàng và chuyển về trang giỏ
            if (!empty($errors)) {
                // Cập nhật lại giỏ hàng sau khi loại bỏ các sản phẩm hết hàng
                session(['cart' => $cart]);

                // Nếu giỏ hàng trống sau khi loại bỏ hết sản phẩm hết hàng
                if (empty($cart)) {
                    return redirect()->route('sites.cart')->with('error', 'Tất cả sản phẩm đã hết hàng.');
                }

                // Trả về trang giỏ hàng kèm theo thông báo lỗi
                return redirect()->route('sites.cart')->with('error', implode('<br>', $errors));
            }


            // Tạo đơn hàng
            $order = new Order();
            $order->address = $data['address'];
            $order->phone = $data['phone'];
            $order->shipping_fee = $data['shipping_fee'];
            $order->total = $data['total'];
            $order->note = $data['note'];
            $order->receiver_name = $data['receiver_name'];
            $order->email = $data['email'];
            $order->VAT = $data['VAT'];
            $order->payment = $data['payment'];
            $order->customer_id = $data['customer_id'];
            $order->save();

            // Tạo chi tiết đơn hàng từ các sản phẩm còn lại
            foreach ($selectedItems as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item->id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'size_and_color' => $item->size . '-' . $item->color,
                    'code' => session('percent_discount', 0),
                ]);
            }

            // Xử lý trừ đi số lượng sản phẩm trong kho theo số lượng đã được đặt
            foreach ($selectedItems as $item) {
                $variant = ProductVariant::where('product_id', $item->id)
                    ->where('size', trim($item->size))
                    ->where('color', trim($item->color))
                    ->lockForUpdate()
                    ->first();

                if ($variant) {
                    $variant->stock -= $item->quantity;
                    $variant->save();
                }
            }

            // Xóa giỏ hàng sau khi tạo đơn hàng thành công
            session()->forget('cart');
            session()->forget('percent_discount');

            // Lưu thông tin thành công vào session
            Session::put('success_data', [
                'logo' => 'cod.png',
                'receiver_name' => $order->receiver_name,
                'order_id' => $order->id,
                'total' => $order->total,
            ]);

            DB::commit();

            return redirect()->route('sites.success.payment');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error("Đặt hàng thất bại: " . $e->getMessage());

            // Cập nhật lại giỏ hàng nếu có sản phẩm hết hàng đã bị xóa
            session(['cart' => $cart]);

            // Nếu chưa có lỗi nào trước đó, thêm lỗi hệ thống
            if (empty($errors)) {
                // Thêm lỗi vào mảng $errors
                $errors[] = 'Đặt hàng thất bại (Do một số sản phẩm bạn chọn mua có thể đã hết hàng) dẫn đến lỗi trong quá trình tạo đơn hàng, bạn vui lòng thử lại!';
            }
            return redirect()->route('sites.cart')->with('error', implode('<br>', $errors));
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $data = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->join('order_details as od', 'o.id', '=', 'od.order_id')
            ->join('product_variants as pv', 'pv.id', '=', 'od.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->where('o.id', $order->id)
            ->select('o.*', 'c.name as customer_name', 'p.product_name as product_name', 'p.image', 'pv.size', 'pv.color', 'od.quantity', 'od.price', 'od.code')
            ->get();
        return view('admin.order.order_detail', compact('data'));
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        // dd($order);
        $order->status = "Đã xử lý";
        $order->save();
        return redirect()->route('order.approval')->with('success', "Duyệt đơn hàng thành công!");
    }


    public function orderTracking() {
        return view('sites.ordertracking.order_tracking');
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }

    public function search(Request $request) {}
}

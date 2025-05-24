<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


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

    public function search(Request $request)
    {
        $query = $request->get('query');
        $statusFilter = $request->get('status_filter');

        // Base query
        $orderQuery = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->whereIn('o.status', ['Chờ xử lý', 'Đã huỷ đơn hàng'])
            ->select('o.*', 'c.name as customer_name');

        // Apply search filter
        if (!empty($query)) {
            $orderQuery->where(function ($q) use ($query) {
                $q->where('o.id', 'LIKE', '%' . $query . '%')
                    ->orWhere('c.phone', 'LIKE', '%' . $query . '%')
                    ->orWhere('c.name', 'LIKE', '%' . $query . '%');
            });
        }

        // Apply status filter
        if (!empty($statusFilter)) {
            $orderQuery->where('o.status', $statusFilter);
        }

        // Get paginated data
        $data = $orderQuery->orderBy('o.id', 'DESC')->paginate(5);

        // Get counts for badges
        $totalCount = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->whereIn('o.status', ['Chờ xử lý', 'Đã huỷ đơn hàng'])
            ->count();

        $pendingCount = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->where('o.status', 'Chờ xử lý')
            ->count();

        $cancelledCount = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->where('o.status', 'Đã huỷ đơn hàng')
            ->count();

        return view('admin.order.order_pending', compact(
            'data',
            'totalCount',
            'pendingCount',
            'cancelledCount'
        ));
    }



    public function orderApproval()
    {
        // Chỉ hiển thị đơn hàng đã xử lý và đang giao hàng
        $data = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->whereIn('o.status', ['Đã xử lý', 'Đang giao hàng'])
            ->orderBy('o.id', 'DESC')
            ->select('o.*', 'c.name as customer_name')
            ->paginate(5);

        // Get counts for badges
        $processedCount = DB::table('orders as o')
            ->where('o.status', 'Đã xử lý')
            ->count();

        $shippingCount = DB::table('orders as o')
            ->where('o.status', 'Đang giao hàng')
            ->count();

        $totalApprovalCount = $processedCount + $shippingCount;

        return view('admin.order.order_approved', compact(
            'data',
            'processedCount',
            'shippingCount',
            'totalApprovalCount'
        ));
    }

    public function searchOrderApproval(Request $request)
    {
        $query = $request->get('query');
        $statusFilter = $request->get('status_filter');

        // Valid statuses - chỉ còn "Đã xử lý" và "Đang giao hàng"
        $validStatuses = ['Đã xử lý', 'Đang giao hàng'];

        // Base query
        $orderQuery = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->select('o.*', 'c.name as customer_name');

        // Apply status filter
        if (!empty($statusFilter) && in_array($statusFilter, $validStatuses)) {
            $orderQuery->where('o.status', $statusFilter);
        } else {
            // Default: chỉ hiển thị đơn hàng đã xử lý và đang giao hàng
            $orderQuery->whereIn('o.status', $validStatuses);
        }

        // Apply search filter
        if (!empty($query)) {
            $orderQuery->where(function ($q) use ($query) {
                $q->where('o.id', 'LIKE', '%' . $query . '%')
                    ->orWhere('c.phone', 'LIKE', '%' . $query . '%')
                    ->orWhere('c.name', 'LIKE', '%' . $query . '%');
            });
        }

        // Get paginated data
        $data = $orderQuery->orderBy('o.id', 'DESC')->paginate(5);

        // Get counts for badges - chỉ tính 2 trạng thái
        $processedCount = DB::table('orders as o')
            ->where('o.status', 'Đã xử lý')
            ->count();

        $shippingCount = DB::table('orders as o')
            ->where('o.status', 'Đang giao hàng')
            ->count();

        $totalApprovalCount = $processedCount + $shippingCount;

        return view('admin.order.order_approved', compact(
            'data',
            'processedCount',
            'shippingCount',
            'totalApprovalCount'
        ));
    }


    public function orderSuccess()
    {
        // Hiển thị đơn hàng đã giao thành công và đã thanh toán
        $data = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->whereIn('o.status', ['Đã giao hàng', 'Đã thanh toán'])
            ->orderBy('o.id', 'DESC')
            ->select('o.*', 'c.name as customer_name')
            ->paginate(5);

        // Get counts for badges
        $deliveredCount = DB::table('orders as o')
            ->where('o.status', 'Đã giao hàng')
            ->count();

        $paidCount = DB::table('orders as o')
            ->where('o.status', 'Đã thanh toán')
            ->count();

        $totalSuccessCount = $deliveredCount + $paidCount;

        return view('admin.order.order_success', compact(
            'data',
            'deliveredCount',
            'paidCount',
            'totalSuccessCount'
        ));
    }

    public function searchOrderSuccess(Request $request)
    {
        $query = $request->get('query');
        $statusFilter = $request->get('status_filter');

        // Valid statuses - đơn hàng thành công
        $validStatuses = ['Đã giao hàng', 'Đã thanh toán'];

        // Base query
        $orderQuery = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->select('o.*', 'c.name as customer_name');

        // Apply status filter
        if (!empty($statusFilter) && in_array($statusFilter, $validStatuses)) {
            $orderQuery->where('o.status', $statusFilter);
        } else {
            // Default: hiển thị tất cả đơn hàng thành công
            $orderQuery->whereIn('o.status', $validStatuses);
        }

        // Apply search filter
        if (!empty($query)) {
            $orderQuery->where(function ($q) use ($query) {
                $q->where('o.id', 'LIKE', '%' . $query . '%')
                    ->orWhere('c.phone', 'LIKE', '%' . $query . '%')
                    ->orWhere('c.name', 'LIKE', '%' . $query . '%');
            });
        }

        // Get paginated data
        $data = $orderQuery->orderBy('o.id', 'DESC')->paginate(5);

        // Get counts for badges
        $deliveredCount = DB::table('orders as o')
            ->where('o.status', 'Đã giao hàng')
            ->count();

        $paidCount = DB::table('orders as o')
            ->where('o.status', 'Đã thanh toán')
            ->count();

        $totalSuccessCount = $deliveredCount + $paidCount;

        return view('admin.order.order_success', compact(
            'data',
            'deliveredCount',
            'paidCount',
            'totalSuccessCount'
        ));
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

            try {
                Mail::to($order->email)->queue(new OrderConfirmationMail($order));
                Log::info('Email xác nhận đơn hàng đã được đưa vào queue cho khách hàng: ' . $order->email . ' với đơn hàng ID: ' . $order->id);
            } catch (\Exception $mailException) {
                Log::error('Lỗi khi gửi email xác nhận đơn hàng cho khách hàng: ' . $order->email . ' với đơn hàng ID: ' . $order->id . '. Lỗi: ' . $mailException->getMessage());
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


    public function orderTracking()
    {
        return view('sites.ordertracking.order_tracking');
    }


    public function orderTrackingAdmin()
    {
        return view('admin.tracking-order.tracking-order');
    }
}

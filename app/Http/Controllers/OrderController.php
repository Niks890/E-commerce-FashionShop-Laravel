<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmationMail;
use App\Mail\OrderDeliverySuccessMail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatusHistory;
use App\Models\ProductVariant;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Events\StockUpdated;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Hashids\Hashids;
use StockUpdated as GlobalStockUpdated;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

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
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

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

        // Apply date filter
        if (!empty($dateFrom)) {
            $orderQuery->whereDate('o.created_at', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $orderQuery->whereDate('o.created_at', '<=', $dateTo);
        }

        // Get paginated data
        $data = $orderQuery->orderBy('o.id', 'DESC')->paginate(5);

        // Get counts for badges - cần cập nhật các hàm count để bao gồm bộ lọc ngày
        $countQuery = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->whereIn('o.status', ['Chờ xử lý', 'Đã huỷ đơn hàng']);

        if (!empty($dateFrom)) {
            $countQuery->whereDate('o.created_at', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $countQuery->whereDate('o.created_at', '<=', $dateTo);
        }

        $totalCount = $countQuery->count();

        $pendingCount = clone $countQuery;
        $pendingCount = $pendingCount->where('o.status', 'Chờ xử lý')->count();

        $cancelledCount = clone $countQuery;
        $cancelledCount = $cancelledCount->where('o.status', 'Đã huỷ đơn hàng')->count();

        return view('admin.order.order_pending', compact(
            'data',
            'totalCount',
            'pendingCount',
            'cancelledCount'
        ));
    }



    public function orderApproval()
    {
        // Chỉ hiển thị đơn hàng đã xử lý và Đã gửi cho đơn vị vận chuyển
        $data = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->whereIn('o.status', ['Đã xử lý', 'Đã gửi cho đơn vị vận chuyển'])
            ->orderBy('o.id', 'DESC')
            ->select('o.*', 'c.name as customer_name')
            ->paginate(5);

        // Get counts for badges
        $processedCount = DB::table('orders as o')
            ->where('o.status', 'Đã xử lý')
            ->count();

        $shippingCount = DB::table('orders as o')
            ->where('o.status', 'Đã gửi cho đơn vị vận chuyển')
            ->count();

        $totalApprovalCount = $processedCount + $shippingCount;

        $employeesWithDeliveryCount = Staff::select('staff.id AS staff_id', 'staff.name AS staff_name')
            ->selectRaw('COUNT(orders.id) AS delivery_count')
            ->leftJoin('orders', 'staff.id', '=', 'orders.staff_delivery_id')
            ->where('staff.position', 'Nhân viên giao hàng')
            ->orWhere('orders.status', ['Đang giao hàng', 'Đã lấy hàng'])
            ->groupBy('staff.id', 'staff.name')
            ->get();

        return view('admin.order.order_approved', compact(
            'data',
            'processedCount',
            'shippingCount',
            'totalApprovalCount',
            'employeesWithDeliveryCount'
        ));
    }

    public function searchOrderApproval(Request $request)
    {
        $query = $request->get('query');
        $statusFilter = $request->get('status_filter');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Valid statuses
        $validStatuses = ['Đã xử lý', 'Đã gửi cho đơn vị vận chuyển'];

        // Base query
        $orderQuery = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->select('o.*', 'c.name as customer_name');

        // Apply status filter
        if (!empty($statusFilter) && in_array($statusFilter, $validStatuses)) {
            $orderQuery->where('o.status', $statusFilter);
        } else {
            // Default: chỉ hiển thị đơn hàng đã xử lý và Đã gửi cho đơn vị vận chuyển
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

        // Apply date filter
        if (!empty($dateFrom)) {
            $orderQuery->whereDate('o.created_at', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $orderQuery->whereDate('o.created_at', '<=', $dateTo);
        }

        // Get paginated data
        $data = $orderQuery->orderBy('o.id', 'DESC')->paginate(5);

        // Get counts for badges - với điều kiện lọc ngày
        $countQuery = DB::table('orders as o')
            ->whereIn('o.status', $validStatuses);

        if (!empty($dateFrom)) {
            $countQuery->whereDate('o.created_at', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $countQuery->whereDate('o.created_at', '<=', $dateTo);
        }

        $processedCount = clone $countQuery;
        $processedCount = $processedCount->where('o.status', 'Đã xử lý')->count();

        $shippingCount = clone $countQuery;
        $shippingCount = $shippingCount->where('o.status', 'Đã gửi cho đơn vị vận chuyển')->count();

        $totalApprovalCount = $processedCount + $shippingCount;

        $employeesWithDeliveryCount = Staff::select('staff.id AS staff_id', 'staff.name AS staff_name')
            ->selectRaw('COUNT(orders.id) AS delivery_count')
            ->leftJoin('orders', 'staff.id', '=', 'orders.staff_delivery_id')
            ->where('staff.position', 'Nhân viên giao hàng')
            ->orWhere('orders.status', ['Đang giao hàng', 'Đã lấy hàng'])
            ->groupBy('staff.id', 'staff.name')
            ->get();

        return view('admin.order.order_approved', compact(
            'data',
            'processedCount',
            'shippingCount',
            'totalApprovalCount',
            'employeesWithDeliveryCount'
        ));
    }

    public function orderSuccess()
    {
        // Hiển thị đơn hàng đã giao thành công và đã thanh toán
        $data = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->whereIn('o.status', ['Giao hàng thành công', 'Đã thanh toán'])
            ->orderBy('o.id', 'DESC')
            ->select('o.*', 'c.name as customer_name')
            ->paginate(5);

        // Get counts for badges
        $deliveredCount = DB::table('orders as o')
            ->where('o.status', 'Giao hàng thành công')
            ->count();

        $paidCount = DB::table('orders as o')
            ->where('o.status', 'Đã thanh toán')
            ->count();

        $totalSuccessCount = $deliveredCount + $paidCount;
        $employeesWithDeliveryCount = Staff::select('staff.id AS staff_id', 'staff.name AS staff_name')
            ->selectRaw('COUNT(orders.id) AS delivery_count')
            ->leftJoin('orders', 'staff.id', '=', 'orders.staff_delivery_id')
            ->where('staff.position', 'Nhân viên giao hàng')
            ->orWhere('orders.status', ['Đang giao hàng', 'Đã lấy hàng'])
            ->groupBy('staff.id', 'staff.name')
            ->get();

        return view('admin.order.order_success', compact(
            'data',
            'deliveredCount',
            'paidCount',
            'totalSuccessCount',
            'employeesWithDeliveryCount'
        ));
    }

    public function searchOrderSuccess(Request $request)
    {
        $query = $request->get('query');
        $statusFilter = $request->get('status_filter');
        $dateFilter = $request->get('date_filter');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Valid statuses - đơn hàng thành công
        $validStatuses = ['Giao hàng thành công', 'Đã thanh toán'];

        // Base query
        $orderQuery = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->select('o.*', 'c.name as customer_name');

        // Apply status filter
        if (!empty($statusFilter)) {
            $orderQuery->where('o.status', $statusFilter);
        } else {
            // Default: hiển thị tất cả đơn hàng thành công
            $orderQuery->whereIn('o.status', $validStatuses);
        }

        // Apply date filters
        if (!empty($dateFilter) || (!empty($startDate) && !empty($endDate))) {
            if ($dateFilter === 'today') {
                $orderQuery->whereDate('o.created_at', Carbon::today());
            } elseif ($dateFilter === 'yesterday') {
                $orderQuery->whereDate('o.created_at', Carbon::yesterday());
            } elseif ($dateFilter === 'this_week') {
                $orderQuery->whereBetween('o.created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
            } elseif ($dateFilter === 'last_week') {
                $orderQuery->whereBetween('o.created_at', [
                    Carbon::now()->subWeek()->startOfWeek(),
                    Carbon::now()->subWeek()->endOfWeek()
                ]);
            } elseif ($dateFilter === 'this_month') {
                $orderQuery->whereBetween('o.created_at', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ]);
            } elseif ($dateFilter === 'last_month') {
                $orderQuery->whereBetween('o.created_at', [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth()
                ]);
            } elseif ($dateFilter === 'this_year') {
                $orderQuery->whereBetween('o.created_at', [
                    Carbon::now()->startOfYear(),
                    Carbon::now()->endOfYear()
                ]);
            } elseif ($dateFilter === 'custom' && $startDate && $endDate) {
                $orderQuery->whereBetween('o.created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            }
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
            ->where('o.status', 'Giao hàng thành công')
            ->count();

        $paidCount = DB::table('orders as o')
            ->where('o.status', 'Đã thanh toán')
            ->count();

        $totalSuccessCount = $deliveredCount + $paidCount;

        $employeesWithDeliveryCount = Staff::select('staff.id AS staff_id', 'staff.name AS staff_name')
            ->selectRaw('COUNT(orders.id) AS delivery_count')
            ->leftJoin('orders', 'staff.id', '=', 'orders.staff_delivery_id')
            ->where('staff.position', 'Nhân viên giao hàng')
            ->orWhere('orders.status', ['Đang giao hàng', 'Đã lấy hàng'])
            ->groupBy('staff.id', 'staff.name')
            ->get();

        return view('admin.order.order_success', compact(
            'data',
            'deliveredCount',
            'paidCount',
            'totalSuccessCount',
            'employeesWithDeliveryCount'
        ));
    }


    public function exportInvoice($id)
    {
        $orderDetail = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->join('order_details as od', 'o.id', '=', 'od.order_id')
            ->join('product_variants as pv', 'pv.id', '=', 'od.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->where('o.id', $id)
            ->select(
                'o.*',
                'c.name as customer_name',
                'c.email',
                'p.product_name',
                'p.id as product_id',
                'p.image',
                'p.sku',
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

        // Tạo mã hash cho link chia sẻ
        $hashids = new Hashids(env('HASHIDS_SALT'), env('HASHIDS_LENGTH'));
        $expiryTime = now()->addDays(1)->timestamp;
        $encodedId = $hashids->encode($orderDetail[0]->id, $expiryTime);

        // Tạo link chia sẻ
        $shareLink = url('/order/order-detail/' . $encodedId);

        // Tạo QR Code (phiên bản mới)
        $qrCode = new QrCode($shareLink);

        $writer = new PngWriter();
        $qrResult = $writer->write($qrCode);

        // Chuyển đổi QR code thành base64
        $qrCodeBase64 = base64_encode($qrResult->getString());
        $qrCodeDataUri = 'data:image/png;base64,' . $qrCodeBase64;

        $pdf = Pdf::loadView('sites.export.pdf.invoice', compact('orderDetail', 'encodedId', 'qrCodeDataUri', 'shareLink'));
        return $pdf->download('invoice_order_' . $id . '.pdf');
    }

    public function handleShareOrder($hash)
    {
        $hashids = new Hashids(env('HASHIDS_SALT'), env('HASHIDS_LENGTH'));
        $decoded = $hashids->decode($hash);

        if (empty($decoded) || count($decoded) < 2) {
            abort(404, 'Link không hợp lệ hoặc đã hết hạn.');
        }

        $orderId = $decoded[0];
        $expiryTimestamp = $decoded[1];

        // Kiểm tra thời gian hết hạn
        if (time() > $expiryTimestamp) {
            abort(404, 'Link chia sẻ đã hết hạn.');
        }

        // return redirect()->route('sites.showOrderDetailOfCustomer', ['order' => $orderId]);
        return redirect()->route('order.show', ['order' => $orderId]);
    }



    // Pessimistic Lock (Khóa bi quan) là kiểu khóa mà khi một bản ghi đang được truy cập (đọc/ghi), nó sẽ bị khóa lại để ngăn chặn các giao dịch khác đọc hoặc sửa đổi.
    // Trong Laravel, ->lockForUpdate() sẽ khóa bản ghi được chọn cho đến khi transaction kết thúc. Điều này đảm bảo không có giao dịch nào khác có thể thay đổi dữ liệu trong khi nó đang được xử lý.
    // Xử lý lưu đơn hàng (bằng transaction và khoá Pessimistic Lock) (không có check stock)
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
                $variant = ProductVariant::where('product_id', $item->id)
                    ->where('size', trim($item->size))
                    ->where('color', trim($item->color))
                    ->lockForUpdate()
                    ->first();

                if (!$variant || $variant->available_stock < $item->quantity) {
                    unset($cart[$key]);
                    $errors[] = 'Sản phẩm "' . $item->product_name . '" đã hết hàng và đã bị xóa khỏi giỏ hàng.';
                }
            }

            if (!empty($errors)) {
                session(['cart' => $cart]);
                if (empty($cart)) {
                    return redirect()->route('sites.cart')->with('error', 'Tất cả sản phẩm đã hết hàng.');
                }
                return redirect()->route('sites.cart')->with('error', implode('<br>', $errors));
            }

            // Xử lý voucher
            $voucher = null;
            $voucherId = session()->get('voucher_id', null);

            if (!empty($voucherId)) {
                $voucher = Voucher::where('id', $voucherId)
                    ->where('vouchers_start_date', '<=', now())
                    ->where('vouchers_end_date', '>=', now())
                    ->lockForUpdate()
                    ->first();

                if (!$voucher) {
                    return redirect()->route('sites.cart')->with('error', 'Voucher không hợp lệ hoặc đã hết hạn.');
                }

                // Kiểm tra số lần sử dụng (chỉ tính các voucher đã dùng thực sự)
                $usageCount = VoucherUsage::where('voucher_id', $voucher->id)
                    ->whereNotNull('order_id')
                    ->count();

                if ($usageCount >= $voucher->vouchers_usage_limit) {
                    return redirect()->route('sites.cart')->with('error', 'Voucher đã hết lượt sử dụng.');
                }

                // Kiểm tra xem customer đã sử dụng voucher này chưa (chỉ tính khi đã dùng thực sự)
                $voucherUsed = VoucherUsage::where('voucher_id', $voucher->id)
                    ->where('customer_id', $data['customer_id'])
                    ->whereNotNull('order_id')
                    ->exists();

                if ($voucherUsed) {
                    return redirect()->route('sites.cart')->with('error', 'Bạn đã sử dụng voucher này rồi.');
                }
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
            $order->status = 'Chờ xử lý';
            $order->customer_id = $data['customer_id'];
            $order->save();

            $orderhistories = new OrderStatusHistory();
            $orderhistories->order_id = $order->id;
            $orderhistories->status = 'Chờ xử lý';
            $orderhistories->save();

            // Tạo chi tiết đơn hàng
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

            // Trừ số lượng sản phẩm trong kho
            foreach ($selectedItems as $item) {
                $variant = ProductVariant::where('product_id', $item->id)
                    ->where('size', trim($item->size))
                    ->where('color', trim($item->color))
                    ->lockForUpdate()
                    ->first();

                if ($variant) {
                    $variant->stock -= $item->quantity;
                    $variant->available_stock -= $item->quantity;
                    $variant->save();
                }
            }

            // Xử lý voucher usage
            if ($voucher) {
                // Kiểm tra xem đã có record voucher_usage chưa (đã tặng nhưng chưa dùng)
                $existingVoucherUsage = VoucherUsage::where('voucher_id', $voucher->id)
                    ->where('customer_id', $data['customer_id'])
                    ->whereNull('order_id')
                    ->first();

                if ($existingVoucherUsage) {
                    // Nếu đã có record (được tặng trước đó), cập nhật
                    $existingVoucherUsage->update([
                        'order_id' => $order->id,
                        'used_at' => now(),
                    ]);
                } else {
                    // Nếu chưa có, tạo mới
                    VoucherUsage::create([
                        'voucher_id' => $voucher->id,
                        'customer_id' => $data['customer_id'],
                        'order_id' => $order->id,
                        'used_at' => now(),
                    ]);
                }

                // Chỉ trừ số lần sử dụng còn lại nếu đây là lần dùng thực sự đầu tiên
                if (!$existingVoucherUsage) {
                    $voucher->vouchers_usage_limit -= 1;
                    $voucher->save();
                }
            }

            // Gửi email xác nhận
            try {
                Mail::to($order->email)->queue(new OrderConfirmationMail($order));
                Log::info('Email xác nhận đơn hàng đã được đưa vào queue cho khách hàng: ' . $order->email . ' với đơn hàng ID: ' . $order->id);
            } catch (\Exception $mailException) {
                Log::error('Lỗi khi gửi email xác nhận đơn hàng cho khách hàng: ' . $order->email . ' với đơn hàng ID: ' . $order->id . '. Lỗi: ' . $mailException->getMessage());
            }

            // Xóa giỏ hàng
            if (count($selectedItems) === count($cart)) {
                session()->forget('cart');
            } else {
                $cart = array_filter($cart, function ($item) {
                    return empty($item->checked) || !$item->checked;
                });
                session(['cart' => $cart]);
            }

            // Xóa session voucher
            if (session()->has('percent_discount') && session()->has('voucher_id')) {
                session()->forget('percent_discount');
                session()->forget('voucher_id');
            }

            // Lưu thông tin thành công
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
            session(['cart' => $cart]);

            if (empty($errors)) {
                $errors[] = 'Đặt hàng thất bại (Do một số sản phẩm bạn chọn mua có thể đã hết hàng) dẫn đến lỗi trong quá trình tạo đơn hàng, bạn vui lòng thử lại!';
            }
            return redirect()->route('sites.cart')->with('error', implode('<br>', $errors));
        }
    }

    // // cải tiến thêm check stock và rollback()
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

    //     $errors = [];
    //     DB::beginTransaction();
    //     try {
    //         // Lấy giỏ hàng từ session
    //         $cart = session('cart', []);
    //         // Lọc ra các sản phẩm đã được chọn (checked = true)
    //         $selectedItems = array_filter($cart, function ($item) {
    //             return !empty($item->checked) && $item->checked;
    //         });

    //         if (empty($selectedItems)) {
    //             return redirect()->route('sites.cart')->with('error', 'Không có sản phẩm nào được chọn để thanh toán.');
    //         }

    //         // Kiểm tra tồn kho và cập nhật session cart ngay lập tức
    //         $hasStockIssue = false;
    //         foreach ($selectedItems as $key => $item) {
    //             // Tìm đúng variant của sản phẩm trong bảng variant
    //             $variant = ProductVariant::where('product_id', $item->id)
    //                 ->where('size', trim($item->size))
    //                 ->where('color', trim($item->color))
    //                 ->lockForUpdate()
    //                 ->first();

    //             if (!$variant) {
    //                 // Xóa sản phẩm không tồn tại khỏi giỏ
    //                 unset($cart[$key]);
    //                 $errors[] = 'Sản phẩm "' . $item->product_name . '" không tồn tại và đã bị xóa khỏi giỏ hàng.';
    //                 $hasStockIssue = true;
    //             } elseif ($variant->available_stock < $item->quantity) {
    //                 if ($variant->available_stock > 0) {
    //                     // Cập nhật số lượng theo stock còn lại
    //                     $cart[$key]->quantity = $variant->available_stock;
    //                     $errors[] = 'Sản phẩm "' . $item->product_name . '" chỉ còn ' . $variant->available_stock . ' sản phẩm. Số lượng đã được điều chỉnh.';
    //                 } else {
    //                     // Xóa sản phẩm hết hàng khỏi giỏ
    //                     unset($cart[$key]);
    //                     $errors[] = 'Sản phẩm "' . $item->product_name . '" đã hết hàng và đã bị xóa khỏi giỏ hàng.';
    //                 }
    //                 $hasStockIssue = true;
    //             }
    //         }

    //         // Nếu có vấn đề về stock, cập nhật lại giỏ hàng và rollback
    //         if ($hasStockIssue) {
    //             DB::rollBack();

    //             // Cập nhật lại giỏ hàng ngay lập tức
    //             session(['cart' => array_values($cart)]);

    //             // Nếu giỏ hàng trống sau khi loại bỏ hết sản phẩm hết hàng
    //             if (empty($cart)) {
    //                 return redirect()->route('sites.cart')->with('error', 'Tất cả sản phẩm đã hết hàng.');
    //             }

    //             // Trả về trang giỏ hàng kèm theo thông báo lỗi
    //             return redirect()->route('sites.cart')->with('error', implode('<br>', $errors));
    //         }

    //         // Cập nhật lại danh sách selected items sau khi kiểm tra stock
    //         $selectedItems = array_filter($cart, function ($item) {
    //             return !empty($item->checked) && $item->checked;
    //         });

    //         if (empty($selectedItems)) {
    //             DB::rollBack();
    //             return redirect()->route('sites.cart')->with('error', 'Không có sản phẩm nào được chọn để thanh toán.');
    //         }

    //         // Tạo đơn hàng
    //         $order = new Order();
    //         $order->address = $data['address'];
    //         $order->phone = $data['phone'];
    //         $order->shipping_fee = $data['shipping_fee'];
    //         $order->total = $data['total'];
    //         $order->note = $data['note'];
    //         $order->receiver_name = $data['receiver_name'];
    //         $order->email = $data['email'];
    //         $order->VAT = $data['VAT'];
    //         $order->payment = $data['payment'];
    //         $order->status = 'Chờ xử lý';
    //         $order->customer_id = $data['customer_id'];
    //         $order->save();

    //         $orderhistories = new OrderStatusHistory();
    //         $orderhistories->order_id = $order->id;
    //         $orderhistories->status = 'Chờ xử lý';
    //         $orderhistories->save();

    //         // Tạo chi tiết đơn hàng từ các sản phẩm còn lại
    //         foreach ($selectedItems as $item) {
    //             OrderDetail::create([
    //                 'order_id' => $order->id,
    //                 'product_id' => $item->id,
    //                 'product_variant_id' => $item->product_variant_id,
    //                 'quantity' => $item->quantity,
    //                 'price' => $item->price,
    //                 'size_and_color' => $item->size . '-' . $item->color,
    //                 'code' => session('percent_discount', 0),
    //             ]);
    //         }

    //         // Xử lý trừ đi số lượng sản phẩm trong kho theo số lượng đã được đặt
    //         foreach ($selectedItems as $item) {
    //             $variant = ProductVariant::where('product_id', $item->id)
    //                 ->where('size', trim($item->size))
    //                 ->where('color', trim($item->color))
    //                 ->lockForUpdate()
    //                 ->first();

    //             if ($variant) {
    //                 // trừ stock cả 2 sau khi đặt hàng thành công
    //                 $variant->stock -= $item->quantity;
    //                 $variant->available_stock -= $item->quantity;
    //                 $variant->save();
    //                 // event(new GlobalStockUpdated($variant->id, $variant->available_stock));
    //             }
    //         }

    //         try {
    //             Mail::to($order->email)->queue(new OrderConfirmationMail($order));
    //             Log::info('Email xác nhận đơn hàng đã được đưa vào queue cho khách hàng: ' . $order->email . ' với đơn hàng ID: ' . $order->id);
    //         } catch (\Exception $mailException) {
    //             Log::error('Lỗi khi gửi email xác nhận đơn hàng cho khách hàng: ' . $order->email . ' với đơn hàng ID: ' . $order->id . '. Lỗi: ' . $mailException->getMessage());
    //         }

    //         // Xóa giỏ hàng sau khi tạo đơn hàng thành công
    //         if (count($selectedItems) === count($cart)) {
    //             session()->forget('cart');
    //         } else {
    //             // Cập nhật lại giỏ hàng chỉ giữ lại sản phẩm chưa chọn
    //             $cart = array_filter($cart, function ($item) {
    //                 return empty($item->checked) || !$item->checked;
    //             });
    //             session(['cart' => array_values($cart)]);
    //         }
    //         session()->forget('percent_discount');

    //         // Lưu thông tin thành công vào session
    //         Session::put('success_data', [
    //             'logo' => 'cod.png',
    //             'receiver_name' => $order->receiver_name,
    //             'order_id' => $order->id,
    //             'total' => $order->total,
    //         ]);

    //         DB::commit();

    //         return redirect()->route('sites.success.payment');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error("Đặt hàng thất bại: " . $e->getMessage());

    //         // Cập nhật lại giỏ hàng nếu có sản phẩm hết hàng đã bị xóa
    //         session(['cart' => array_values($cart)]);

    //         // Nếu chưa có lỗi nào trước đó, thêm lỗi hệ thống
    //         if (empty($errors)) {
    //             $errors[] = 'Đặt hàng thất bại do lỗi hệ thống. Vui lòng thử lại!';
    //         }
    //         return redirect()->route('sites.cart')->with('error', implode('<br>', $errors));
    //     }
    // }



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
            ->select('o.*', 'c.name as customer_name', 'p.product_name as product_name', 'p.image', 'pv.size', 'p.sku', 'pv.color', 'od.quantity', 'od.price', 'od.code')
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

        $orderhistories = new OrderStatusHistory();
        $orderhistories->order_id = $order->id;
        $orderhistories->status = "Đã xử lý";
        $orderhistories->updated_by = auth()->user()->id - 1;
        $orderhistories->save();
        return redirect()->route('order.approval')->with('success', "Duyệt đơn hàng thành công!");
    }


    public function updateOrderStatusDelivery(Request $request, Order $order)
    {
        $data = $request->all();
        $order = Order::find($data['order_id']);
        $order->status = "Đã gửi cho đơn vị vận chuyển";
        $order->staff_delivery_id = $data['updated_by'];
        $order->save();

        $orderhistories = new OrderStatusHistory();
        $orderhistories->order_id = $order->id;
        $orderhistories->status = "Đã gửi cho đơn vị vận chuyển";
        $orderhistories->updated_by = $data['updated_by'];
        $orderhistories->save();
        return redirect()->route('order.approval')->with('success', "Đơn hàng đã được gửi cho đơn vị vận chuyển!");
    }


    public function manageDeliveryOrders(Request $request)
    {
        // Khởi tạo query lấy tất cả đơn hàng của nhân viên giao hàng đó
        $ordersQuery = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->whereIn('o.status', [
                'Đã gửi cho đơn vị vận chuyển',
                'Đang giao hàng',
                'Giao hàng thành công',
                'Đã huỷ đơn hàng'
            ])
            ->where('o.staff_delivery_id', auth()->user()->id - 1)
            ->select(
                'o.*',
                'c.name as customer_name',
            )
            ->orderBy('o.id', 'DESC');

        // Lấy query gốc để tính toán các bộ đếm tổng
        $baseQueryForCounts = clone $ordersQuery;

        // Áp dụng tìm kiếm theo query
        $searchQuery = $request->input('query');
        if ($searchQuery) {
            $ordersQuery->where(function ($q) use ($searchQuery) {
                $q->where('o.id', 'like', '%' . $searchQuery . '%')
                    ->orWhere('c.phone', 'like', '%' . $searchQuery . '%');
            });
        }

        // Áp dụng lọc theo trạng thái
        $statusFilter = $request->input('status_filter');
        if ($statusFilter) {
            $ordersQuery->where('o.status', $statusFilter);
        }

        // Áp dụng lọc theo ngày tạo
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate) {
            $ordersQuery->whereDate('o.created_at', '>=', $startDate);
        }

        if ($endDate) {
            $ordersQuery->whereDate('o.created_at', '<=', $endDate);
        }

        // Lấy dữ liệu đã phân trang
        $data = $ordersQuery->paginate(5)->appends($request->except('page'));

        // Lấy các bộ đếm trạng thái từ query gốc
        $totalOrders = $baseQueryForCounts->count();
        $processedOrders = (clone $baseQueryForCounts)->where('o.status', 'Đã gửi cho đơn vị vận chuyển')->count();
        $shippingOrders = (clone $baseQueryForCounts)->where('o.status', 'Đang giao hàng')->count();
        $successOrders = (clone $baseQueryForCounts)->where('o.status', 'Giao hàng thành công')->count();
        $failedOrders = (clone $baseQueryForCounts)->where('o.status', 'Đã huỷ đơn hàng')->count();

        return view('admin.tracking-order.tracking-order', compact(
            'data',
            'totalOrders',
            'processedOrders',
            'shippingOrders',
            'successOrders',
            'failedOrders'
        ));
    }

    // Phương thức cập nhật trạng thái đơn hàng sử dụng form submit
    public function updateOrderStatus(Request $request, $orderId)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Đang giao hàng,Giao hàng thành công,Đã huỷ đơn hàng',
        ]);

        try {
            $order = Order::findOrFail($orderId);
            $currentStatus = $order->status;
            $newStatus = $validated['status'];

            // Validate status transition
            $allowedTransitions = [
                'Đã gửi cho đơn vị vận chuyển' => ['Đang giao hàng', 'Đã huỷ đơn hàng'],
                'Đang giao hàng' => ['Giao hàng thành công', 'Đã huỷ đơn hàng'],
            ];

            if (in_array($currentStatus, ['Giao hàng thành công', 'Đã huỷ đơn hàng'])) {
                return redirect()->back()->with('error', 'Không thể cập nhật trạng thái cho đơn hàng đã hoàn tất hoặc đã hủy.');
            }

            if (!in_array($newStatus, $allowedTransitions[$currentStatus] ?? [])) {
                return redirect()->back()->with('error', 'Chuyển trạng thái không hợp lệ.');
            }

            DB::transaction(function () use ($order, $newStatus) {
                $order->update(['status' => $newStatus]);

                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => $newStatus,
                ]);

                // Gửi email khi đơn hàng thành công
                if ($newStatus === 'Giao hàng thành công') {
                    $this->sendDeliverySuccessEmail($order);
                }
            });

            return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Đơn hàng không tồn tại.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    protected function sendDeliverySuccessEmail($order)
    {
        try {
            $order->load(['orderDetails.Product', 'orderDetails.ProductVariant']);
            Mail::to($order->email)->queue(new OrderDeliverySuccessMail($order));
            Log::info('Email xác nhận giao hàng thành công đã gửi cho khách hàng: ' . $order->email . ' - Đơn hàng ID: ' . $order->id);
        } catch (\Exception $e) {
            Log::error('Lỗi khi gửi email xác nhận giao hàng thành công: ' . $e->getMessage() . ' - Đơn hàng ID: ' . $order->id);
        }
    }




    public function orderTracking($orderId)
    {
        // Lấy thông tin đơn hàng cơ bản
        $order = DB::table('orders as o')
            ->join('customers as c', 'o.customer_id', '=', 'c.id')
            ->where('o.id', $orderId)
            ->select(
                'o.*',
                'c.name as customer_name',
                'c.phone as customer_phone',
                'c.address as customer_address'
            )
            ->first();

        if (!$order) {
            abort(404, 'Đơn hàng không tồn tại');
        }

        // Lấy chi tiết sản phẩm trong đơn hàng
        $orderDetails = DB::table('order_details as od')
            ->join('product_variants as pv', 'pv.id', '=', 'od.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->where('od.order_id', $orderId)
            ->select(
                'od.*',
                'p.product_name',
                'p.image as product_image',
                'pv.size',
                'pv.color'
            )
            ->get();

        // Gắn orderDetails vào order object
        $order->orderDetails = $orderDetails;

        // Sử dụng thông tin từ customer thay vì order nếu order không có
        $order->phone = $order->phone ?? $order->customer_phone;
        $order->address = $order->address ?? $order->customer_address;

        // Lấy lịch sử trạng thái đơn hàng (timeline)
        $orderStatusHistory = OrderStatusHistory::where('order_id', $orderId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Lấy thông tin nhân viên giao hàng (nếu có)
        $deliveryPerson = Order::with(['staffDelivery:id,name,phone,sex,status,avatar'])
            ->find($orderId);

        return view('sites.ordertracking.order_tracking', [
            'dataOrder' => $order,
            'orderStatusHistory' => $orderStatusHistory,
            'deliveryPerson' => $deliveryPerson
        ]);
    }
}

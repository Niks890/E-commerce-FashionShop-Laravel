<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatusHistory;
use App\Models\ProductVariant;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        switch ($request->payment) {
            case 'vnpay':
                return $this->checkoutVnpay($request);
                break;
            case 'momo':
                return $this->checkoutMomo($request);
                break;
            case 'zalopay':
                return $this->checkoutZaloPay($request);
                break;
            default:
                return abort('404', 'Trang không tồn tại');
                break;
        }
    }

    // thông tin test Ngân hàng: NCB
    // Số thẻ: 9704198526191432198
    // Tên chủ thẻ:NGUYEN VAN A
    // Ngày phát hành:07/15
    // Mật khẩu OTP:123456

    // Chưa làm trang thông báo giao dịch thành công và xem lại đơn hàng ạ nên về sẽ về trang chủ khi giao dịch thành công

    public function checkoutVnpay(Request $request)
    {
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        Session::put('order_data', $request->all()); // Lưu toàn bộ dữ liệu vào session trước khi chuyển hướng VNPAY
        // link trả về trang checkout
        $vnp_Returnurl = "http://127.0.0.1:8000/vnpay-return";

        $vnp_TmnCode = "N528S1UI"; //Mã website tại VNPAY
        $vnp_HashSecret = "DJONF4NR7QM5BQ0RYCJNFLDOGSZGPZMN"; //Chuỗi bí mật
        // mã ảo
        $vnp_TxnRef = Str::random(10); //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = "Thanh toán đơn hàng test";
        $vnp_OrderType = "billpayment";
        $vnp_Amount = ($request->total) * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = "NCB";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }

        //var_dump($inputData);
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret); //
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array(
            'code' => '00',
            'message' => 'success',
            'data' => $vnp_Url
        );
        if ($request->input('redirect')) {
            header('Location: ' . $vnp_Url);
            die();
        } else {
            echo json_encode($returnData);
        }
        return redirect()->away($vnp_Url);
    }



    // Xử lý thanh toán VNPAY (Persimistic Lock)
    public function vnpayReturn(Request $request)
    {
        $vnp_ResponseCode = $request->vnp_ResponseCode; // Mã phản hồi từ VNPAY
        $vnp_TxnRef = $request->vnp_TxnRef; // Mã giao dịch đơn hàng
        $vnp_Amount = $request->vnp_Amount / 100; // Số tiền thanh toán (chuyển về đơn vị VNĐ)

        DB::beginTransaction();
        try {
            if ($vnp_ResponseCode == "00") { // Thanh toán thành công
                if (Session::has('order_data')) {
                    $data = Session::get('order_data');

                    // Lấy giỏ hàng từ session
                    $cart = session('cart', []);

                    // Lọc ra các sản phẩm đã được chọn (checked = true)
                    $selectedItems = array_filter($cart, function ($item) {
                        return !empty($item->checked) && $item->checked;
                    });

                    if (empty($selectedItems)) {
                        return redirect()->back()->with('error', 'Không có sản phẩm nào được chọn để thanh toán.');
                    }

                    // Kiểm tra tồn kho và loại bỏ sản phẩm hết hàng
                    $errors = [];
                    foreach ($selectedItems as $key => $item) {
                        // Tìm đúng variant của sản phẩm trong bảng variant
                        $variant = ProductVariant::where('product_id', $item->id)
                            ->where('size', trim($item->size))
                            ->where('color', trim($item->color))
                            ->lockForUpdate()
                            ->first();

                        if (!$variant || $variant->available_stock < $item->quantity) {
                            // Xóa sản phẩm hết hàng khỏi giỏ
                            unset($cart[$key]);
                            $errors[] = 'Sản phẩm "' . $item->product_name . '" đã hết hàng và đã bị xóa khỏi giỏ hàng.';
                        }
                    }


                    // Nếu có lỗi, chuyển về trang giỏ kèm thông báo
                    if (!empty($errors)) {

                        // Cập nhật lại giỏ hàng sau khi loại bỏ các sản phẩm hết hàng
                        session(['cart' => $cart]);

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

                    // Tạo đơn hàng lưu vào db
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
                    $order->status = 'Đã thanh toán'; // Đánh dấu đơn hàng đã thanh toán
                    $order->transaction_id = $vnp_TxnRef; // Lưu mã giao dịch VNPAY
                    $order->save();


                    $orderhistories = new OrderStatusHistory();
                    $orderhistories->order_id = $order->id;
                    $orderhistories->status = 'Đã thanh toán';
                    $orderhistories->save();

                    // Tạo chi tiết đơn hàng từ các sản phẩm đã chọn
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

                    // Nếu tất cả sản phẩm trong giỏ đều đã chọn, xóa toàn bộ giỏ hàng
                    if (count($selectedItems) === count($cart)) {
                        session()->forget('cart');
                    } else {
                        // Cập nhật lại giỏ hàng chỉ giữ lại sản phẩm chưa chọn
                        $cart = array_filter($cart, function ($item) {
                            return empty($item->checked) || !$item->checked;
                        });
                        session(['cart' => $cart]);
                    }

                    // Trừ số lượng tồn kho
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

                    try {
                        Mail::to($order->email)->queue(new OrderConfirmationMail($order));
                        Log::info('Email xác nhận đơn hàng đã được đưa vào queue cho khách hàng: ' . $order->email . ' với đơn hàng ID: ' . $order->id);
                    } catch (\Exception $mailException) {
                        Log::error('Lỗi khi gửi email xác nhận đơn hàng cho khách hàng: ' . $order->email . ' với đơn hàng ID: ' . $order->id . '. Lỗi: ' . $mailException->getMessage());
                    }


                    if (session()->has('percent_discount') && session()->has('voucher_id')) {
                        session()->forget('percent_discount');
                        session()->forget('voucher_id');
                    }
                    Session::forget('order_data'); // Xóa session sau khi lưu vào db

                    // Lưu thông tin hiển thị thanh toán thành công
                    Session::put('success_data', [
                        'logo' => 'vnpay.png',
                        'receiver_name' => $order->receiver_name,
                        'order_id' => $order->id,
                        'total' => $order->total
                    ]);

                    // Commit transaction khi không có lỗi
                    DB::commit();

                    return redirect()->route('sites.success.payment');
                }
            } else {
                DB::rollBack();
                return redirect()->route('sites.cart')->with('error', 'Thanh toán thất bại hoặc bị hủy!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            session(['cart' => $cart]);
            Log::error("Lỗi thanh toán VNPAY: " . $e->getMessage());
            return redirect()->route('sites.cart')->with('error', 'Đã xảy ra lỗi trong quá trình xử lý thanh toán (Sản phẩm bạn mua có thể đã hết hàng). Vui lòng thử lại sau.');
        }
    }



    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }


    // Dữ liệu test momo
    // No	Tên	Số thẻ	    Hạn ghi trên thẻ	        OTP	Trường hợp test
    // 1	NGUYEN VAN A	9704 0000 0000 0018	03/07	OTP	Thành công
    // 2	NGUYEN VAN A	9704 0000 0000 0026	03/07	OTP	Thẻ bị khóa
    // 3	NGUYEN VAN A	9704 0000 0000 0034	03/07	OTP	Nguồn tiền không đủ
    // 4	NGUYEN VAN A	9704 0000 0000 0042	03/07	OTP	Hạn mức thẻ
    // SDT 0923441111  => Chỉ cho 30 củ thôi

    public function checkoutMomo(Request $request)
    {
        // Trang tạo giao dịch khi thực hiện thanh toán
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

        Session::put('order_data', $request->all());

        $partnerCode = 'MOMOBKUN20180529';
        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $orderInfo = "Thanh toán qua MoMo";
        $amount = $request->total;
        $orderId = time() . "";

        // Trả về sau khi giao dịch
        $redirectUrl = "http://127.0.0.1:8000/momo-return";

        $ipnUrl = "http://127.0.0.1:8000/momo-return";
        $extraData = "";
        $requestId = time() . "";
        $requestType = "payWithATM";
        //before sign HMAC SHA256 signature
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        $data = array(
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        );
        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);  // decode json
        return redirect()->to($jsonResult['payUrl']);
    }


    // Xử lý thanh toán Momo (bình thường)
    public function momoReturn(Request $request)
    {
        $partnerCode = $request->partnerCode;
        $orderId = $request->orderId; // Mã đơn hàng
        $requestId = $request->requestId;
        $amount = $request->amount;
        $orderInfo = $request->orderInfo;
        $orderType = $request->orderType;
        $transId = $request->orderId; // Mã giao dịch lấy đại mã đơn hàng từ hàm time() gì của nó ai biết
        $resultCode = $request->resultCode; // Kết quả giao dịch
        // dd($request->all());

        if ($resultCode !== "") {
            if (Session::has('order_data')) {
                $data = Session::get('order_data');

                // DB::beginTransaction();
                try {

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
                    $order->customer_id = $data['customer_id'];
                    $order->status = 'Đã thanh toán';
                    $order->transaction_id = $transId; // Lưu mã giao dịch MoMo
                    $order->save();

                    $orderhistories = new OrderStatusHistory();
                    $orderhistories->order_id = $order->id;
                    $orderhistories->status = 'Đã thanh toán';
                    $orderhistories->save();


                    // Lấy giỏ hàng từ session
                    $cart = session('cart', []);

                    // Lọc ra các sản phẩm đã được chọn (checked = true)
                    $selectedItems = array_filter($cart, function ($item) {
                        return !empty($item->checked) && $item->checked;
                    });

                    if (empty($selectedItems)) {
                        return redirect()->back()->with('error', 'Không có sản phẩm nào được chọn để thanh toán.');
                    }

                    // Tạo chi tiết đơn hàng từ các sản phẩm đã chọn
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

                    if (session()->has('percent_discount') && session()->has('voucher_id')) {
                        session()->forget('percent_discount');
                        session()->forget('voucher_id');
                    }
                    Session::forget('order_data');

                    try {
                        Mail::to($order->email)->queue(new OrderConfirmationMail($order));
                        Log::info('Email xác nhận đơn hàng đã được đưa vào queue cho khách hàng: ' . $order->email . ' với đơn hàng ID: ' . $order->id);
                    } catch (\Exception $mailException) {
                        Log::error('Lỗi khi gửi email xác nhận đơn hàng cho khách hàng: ' . $order->email . ' với đơn hàng ID: ' . $order->id . '. Lỗi: ' . $mailException->getMessage());
                    }

                    // Nếu tất cả sản phẩm trong giỏ đều đã chọn, xóa toàn bộ giỏ hàng
                    if (count($selectedItems) === count($cart)) {
                        session()->forget('cart');
                    } else {
                        // Cập nhật lại giỏ hàng chỉ giữ lại sản phẩm chưa chọn
                        $cart = array_filter($cart, function ($item) {
                            return empty($item->checked) || !$item->checked;
                        });
                        session(['cart' => $cart]);
                    }

                    // Cập nhật tồn kho
                    $orderDetails = OrderDetail::where('order_id', $order->id)->get();
                    foreach ($orderDetails as $detail) {
                        [$size, $color] = explode('-', $detail->size_and_color);
                        $variant = ProductVariant::where('product_id', $detail->product_id)
                            ->where('size', trim($size))
                            ->where('color', trim($color))
                            ->first();

                        if ($variant) {
                            $variant->available_stock -= $detail->quantity;
                            $variant->stock -= $detail->quantity;
                            $variant->save();
                        }
                    }
                    Session::put('success_data', [
                        'logo' => 'momo.png',
                        'receiver_name' => $order->receiver_name,
                        'order_id' => $order->id,
                        'total' => $order->total
                    ]);
                    // DB::commit();

                    return redirect()->route('sites.success.payment');
                } catch (Exception $e) {
                    dd($e->getMessage(), $e->getFile(), $e->getLine());
                }
            }
        } else {
            return redirect()->route('sites.cart')->with('error', 'Thanh toán thất bại hoặc bị hủy!');
        }
    }

    // Xử lý thanh toán Momo (Persimistic Lock) ko dùng đc do chưa xử lý trường hợp mã lỗi thanh toán
    // public function momoReturn(Request $request)
    // {
    //     $partnerCode = $request->partnerCode;
    //     $orderId = $request->orderId; // Mã đơn hàng
    //     $requestId = $request->requestId;
    //     $amount = $request->amount;
    //     $orderInfo = $request->orderInfo;
    //     $orderType = $request->orderType;
    //     $transId = $request->orderId; // Mã giao dịch lấy từ orderId
    //     $resultCode = $request->resultCode; // Kết quả giao dịch

    //     dd('Result Code: ' . $resultCode);
    //     Mã Result Code mặc định là 0
    //     DB::beginTransaction(); // Bắt đầu giao dịch
    //     try {
    //         if ($resultCode !== "") {
    //             if (Session::has('order_data')) {
    //                 $data = Session::get('order_data');

    //                 $order = new Order();
    //                 $order->address = $data['address'];
    //                 $order->phone = $data['phone'];
    //                 $order->shipping_fee = $data['shipping_fee'];
    //                 $order->total = $data['total'];
    //                 $order->note = $data['note'];
    //                 $order->receiver_name = $data['receiver_name'];
    //                 $order->email = $data['email'];
    //                 $order->VAT = $data['VAT'];
    //                 $order->payment = $data['payment'];
    //                 $order->customer_id = $data['customer_id'];
    //                 $order->status = 'Đã thanh toán';
    //                 $order->transaction_id = $transId; // Lưu mã giao dịch MoMo
    //                 $order->save();

    //                 // Lấy giỏ hàng từ session
    //                 $cart = session('cart', []);

    //                 // Lọc ra các sản phẩm đã được chọn (checked = true)
    //                 $selectedItems = array_filter($cart, function ($item) {
    //                     return !empty($item->checked) && $item->checked;
    //                 });

    //                 if (empty($selectedItems)) {
    //                     DB::rollBack(); // Rollback transaction nếu không có sản phẩm nào được chọn
    //                     return redirect()->back()->with('error', 'Không có sản phẩm nào được chọn để thanh toán.');
    //                 }

    //                 // Kiểm tra tồn kho và loại bỏ sản phẩm hết hàng
    //                 $errors = [];
    //                 foreach ($selectedItems as $key => $item) {
    //                     // Tìm đúng variant của sản phẩm trong bảng variant
    //                     $variant = ProductVariant::where('product_id', $item->id)
    //                         ->where('size', trim($item->size))
    //                         ->where('color', trim($item->color))
    //                         ->lockForUpdate()
    //                         ->first();

    //                     if (!$variant || $variant->available_stock < $item->quantity) {
    //                         // Xóa sản phẩm hết hàng khỏi giỏ
    //                         unset($cart[$key]);
    //                         $errors[] = 'Sản phẩm bạn chọn đã hết hàng và đã bị xóa khỏi giỏ hàng.';
    //                     }
    //                 }

    //                 // Nếu có lỗi, chuyển về trang giỏ kèm thông báo
    //                 if (!empty($errors)) {
    //                     // Cập nhật lại giỏ hàng sau khi loại bỏ các sản phẩm hết hàng
    //                     session(['cart' => $cart]);
    //                     DB::rollBack(); // Rollback nếu có sản phẩm hết hàng
    //                     return redirect()->route('sites.cart')->with('error', implode('<br>', $errors));
    //                 }

    //                 // Tạo chi tiết đơn hàng từ các sản phẩm đã chọn
    //                 foreach ($selectedItems as $item) {
    //                     OrderDetail::create([
    //                         'order_id' => $order->id,
    //                         'product_id' => $item->id,
    //                         'product_variant_id' => $item->product_variant_id,
    //                         'quantity' => $item->quantity,
    //                         'price' => $item->price,
    //                         'size_and_color' => $item->size . '-' . $item->color,
    //                         'code' => session('percent_discount', 0),
    //                     ]);
    //                 }

    //                 // Trừ số lượng tồn kho
    //                 foreach ($selectedItems as $item) {
    //                     $variant = ProductVariant::where('product_id', $item->id)
    //                         ->where('size', trim($item->size))
    //                         ->where('color', trim($item->color))
    //                         ->lockForUpdate()
    //                         ->first();

    //                     if ($variant) {
    //                         $variant->stock -= $item->quantity;
    //                         $variant->available_stock -= $item->quantity;
    //                         $variant->save();
    //                     }
    //                 }

    //                 // Xóa giỏ hàng và session giảm giá sau khi tạo đơn hàng thành công
    //                 session()->forget('cart');
    //                 session()->forget('percent_discount');
    //                 Session::forget('order_data'); // Xóa session sau khi lưu vào db

    //                 // Lưu thông tin hiển thị thanh toán thành công
    //                 Session::put('success_data', [
    //                     'logo' => 'momo.png',
    //                     'receiver_name' => $order->receiver_name,
    //                     'order_id' => $order->id,
    //                     'total' => $order->total
    //                 ]);

    //                 DB::commit(); // Commit transaction

    //                 return redirect()->route('sites.success.payment');
    //             }
    //         } else {
    //             return redirect()->route('sites.cart')->with('error', 'Thanh toán thất bại hoặc bị hủy!');
    //         }
    //     } catch (Exception $e) {
    //         // cập nhật lại cart nếu có lỗi
    //         session(['cart' => $cart]);
    //         DB::rollBack(); // Rollback transaction nếu có lỗi
    //         dd($e->getMessage(), $e->getFile(), $e->getLine());
    //     }
    // }


    // zalopay cần hosting mới dùng đc
    public function checkoutZaloPay(Request $request)
    {
        Session::put('order_data', $request->all());
        Log::info('Order data stored in session:', $request->all());

        $config = [
            "app_id" => "2553",
            "key1" => "PcY4iZIKFCIdgZvA6ueMcMHHUbRLYjPL",
            "key2" => "kLtgPl8HHhfvMuDHPwKfgfsY4Ydm9eIz",
            "endpoint" => "https://sb-openapi.zalopay.vn/v2/create"
        ];

        $embeddata = json_encode([
            'redirecturl' => route('payment.zalopay.return'),
        ]);

        $items = '[]';
        $transID = rand(0, 1000000);
        $order = [
            "app_id" => $config["app_id"],
            "app_time" => round(microtime(true) * 1000),
            "app_trans_id" => date("ymd") . "_" . $transID,
            "app_user" => "user123",
            "item" => $items,
            "embed_data" => $embeddata,
            "amount" => $request->total,
            "description" => "Thanh toán đơn hàng #$transID",
            "bank_code" => "",
            "return_url" => route('payment.zalopay.return')
        ];

        // Tạo mã bảo mật MAC
        $data = implode("|", [
            $order["app_id"],
            $order["app_trans_id"],
            $order["app_user"],
            $order["amount"],
            $order["app_time"],
            $order["embed_data"],
            $order["item"]
        ]);
        $order["mac"] = hash_hmac("sha256", $data, $config["key1"]);

        // Gửi yêu cầu lên ZaloPay
        $context = stream_context_create([
            "http" => [
                "header" => "Content-type: application/x-www-form-urlencoded\r\n",
                "method" => "POST",
                "content" => http_build_query($order)
            ]
        ]);

        $resp = file_get_contents($config["endpoint"], false, $context);
        if ($resp === false) {
            Log::error('Lỗi khi kết nối ZaloPay.');
            return redirect()->route('sites.cart')->with('message', 'Lỗi khi kết nối ZaloPay.');
        }

        $result = json_decode($resp, true);
        if (!isset($result["order_url"])) {
            Log::error('Lỗi khi tạo đơn hàng với ZaloPay.', $result);
            return redirect()->route('sites.cart')->with('message', 'Lỗi khi tạo đơn hàng với ZaloPay.');
        }

        Log::info('Redirecting to ZaloPay:', ['url' => $result["order_url"]]);

        if (isset($result["return_code"]) && $result["return_code"] == 1) {
            return redirect()->away($result["order_url"]);
        }
    }
    public function zalopayReturn(Request $request)
    {
        $data = $request->all();
        try {
            $key2 = "kLtgPl8HHhfvMuDHPwKfgfsY4Ydm9eIz";
            $postdata = file_get_contents('php://input');
            Log::info('ZaloPay Callback Raw Data:', ['data' => $postdata]);

            // Kiểm tra xem dữ liệu có hợp lệ không
            $postdatajson = json_decode($postdata, true);
            if (!$postdatajson) {
                Log::error('Dữ liệu ZaloPay không hợp lệ:', ['data' => $postdata]);
                return redirect()->route('sites.cart')->with('message', 'Dữ liệu từ ZaloPay không hợp lệ.');
            }

            // Kiểm tra trường "data" có tồn tại trong callback hay không
            if (!isset($postdatajson["data"])) {
                Log::error('Trường "data" không tồn tại trong callback từ ZaloPay.', ['postdatajson' => $postdatajson]);
                return redirect()->route('sites.cart')->with('message', 'Dữ liệu callback từ ZaloPay không hợp lệ.');
            }

            $mac = hash_hmac("sha256", $postdatajson["data"], $key2);
            if ($mac !== $postdatajson["mac"]) {
                Log::error('MAC không hợp lệ:', ['received' => $postdatajson["mac"], 'expected' => $mac]);
                return redirect()->route('sites.cart')->with('message', 'Giao dịch thất bại: MAC không hợp lệ.');
            }

            $datajson = json_decode($postdatajson["data"], true);
            $app_trans_id = $datajson["app_trans_id"];

            if (!Session::has('order_data')) {
                Log::error('Không tìm thấy dữ liệu đơn hàng trong session.');
                return redirect()->route('sites.cart')->with('message', 'Không tìm thấy dữ liệu đơn hàng.');
            }

            $data = Session::get('order_data');
            Log::info('Dữ liệu đơn hàng lấy từ session:', $data);

            try {
                $order = new Order();
                $order->address = $data['address'];
                $order->phone = $data['phone'];
                $order->shipping_fee = $data['shipping_fee'];
                $order->total = $data['total'];
                $order->note = $data['note'];
                $order->receiver_name = $data['receiver_name'];
                $order->email = $data['email'];
                $order->VAT = $data['VAT'];
                $order->payment = 'ZaloPay';
                $order->customer_id = $data['customer_id'];
                $order->status = 'Đã thanh toán';
                $order->transaction_id = $app_trans_id;
                $order->save();
                Log::info('Đơn hàng đã được lưu:', ['order_id' => $order->id]);

                if (Session::has('cart') && count(Session::get('cart')) > 0) {
                    foreach (Session::get('cart') as $item) {
                        OrderDetail::create([
                            'order_id' => $order->id,
                            'product_id' => $item->id,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'size_and_color' => $item->size . '-' . $item->color
                        ]);
                    }
                    Session::forget('cart');
                }

                $orderDetails = OrderDetail::where('order_id', $order->id)->get();
                foreach ($orderDetails as $detail) {
                    [$size, $color] = explode('-', $detail->size_and_color);
                    $variant = ProductVariant::where('product_id', $detail->product_id)
                        ->where('size', trim($size))
                        ->where('color', trim($color))
                        ->first();

                    if ($variant) {
                        $variant->available_stock -= $detail->quantity;
                        $variant->stock -= $detail->quantity;
                        $variant->save();
                    }
                }

                Session::forget('order_data');
                return redirect()->route('sites.cart')->with('message', 'Thanh toán thành công!');
            } catch (Exception $e) {
                Log::error('Lỗi khi lưu đơn hàng:', ['error' => $e->getMessage()]);
                return redirect()->route('sites.cart')->with('message', 'Lỗi khi lưu đơn hàng: ' . $e->getMessage());
            }
        } catch (Exception $e) {
            Log::error('Lỗi hệ thống:', ['error' => $e->getMessage()]);
            return redirect()->route('sites.cart')->with('message', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
}

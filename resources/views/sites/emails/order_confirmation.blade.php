<!DOCTYPE html>
<html>

<head>
    <style>
        /* Global Styles */
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f0f2f5;
            /* Lighter, softer background */
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        /* Container */
        .container {
            max-width: 600px;
            margin: 30px auto;
            /* More vertical margin */
            background-color: #ffffff;
            border-radius: 10px;
            /* More rounded corners */
            overflow: hidden;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
            /* Stronger, but still subtle shadow */
        }

        /* Header */
        .header {
            background-color: #4CAF50;
            /* A fresh green, often associated with success/confirmation */
            color: #ffffff;
            padding: 25px 20px;
            /* Increased padding */
            text-align: center;
            border-bottom: 5px solid #45a049;
            /* Darker green border for depth */
        }

        .header h2 {
            margin: 0;
            font-size: 26px;
            font-weight: 700;
            /* Bolder */
            letter-spacing: 0.5px;
        }

        /* Content Area */
        .content {
            padding: 25px 35px;
            /* More padding for content */
        }

        .content p {
            margin-bottom: 12px;
            font-size: 15px;
        }

        .content strong {
            color: #222222;
            /* Even darker bold text */
        }

        /* Section Headings */
        h3 {
            color: #4CAF50;
            /* Match header color */
            font-size: 20px;
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }

        /* Product Table */
        table {
            width: 100%;
            border-collapse: collapse;
            /* Use collapse for cleaner lines */
            margin-top: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            /* Ensures rounded corners are visible */
        }

        th,
        td {
            padding: 14px 18px;
            /* More padding in table cells */
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
            /* Very light horizontal lines */
        }

        th {
            background-color: #f8f8f8;
            color: #555555;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.2px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        td img {
            display: block;
            border-radius: 6px;
            /* Slightly more rounded image corners */
            object-fit: cover;
            border: 1px solid #eeeeee;
        }

        /* Summary Section */
        .summary-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px dashed #dddddd;
            text-align: right;
        }

        .summary-section p {
            margin: 8px 0;
            font-size: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-section p strong {
            min-width: 150px;
            /* Align labels */
            text-align: left;
        }

        .summary-section .summary-row {
            font-size: 20px;
            color: #4CAF50;
            font-weight: 700;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e0e0e0;
        }

        /* Call to Action Button/Link */
        .call-to-action {
            display: block;
            width: fit-content; /* Adjust width to content */
            margin: 30px auto;
            padding: 12px 25px;
            background-color: #007bff;
            /* Standard blue for links */
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .call-to-action:hover {
            background-color: #0056b3;
        }

        /* Footer */
        .footer {
            background-color: #f6f6f6;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #777777;
            border-top: 1px solid #eeeeee;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Xác nhận đơn hàng của bạn đã được đặt thành công!</h2>
        </div>
        <div class="content">
            <p>Xin chào **{{ $order->receiver_name }}**,</p>
            <p>Cảm ơn bạn đã đặt hàng tại cửa hàng của chúng tôi. Đơn hàng của bạn đã được xác nhận và sẽ sớm được xử lý. Dưới đây là thông tin chi tiết đơn hàng của bạn:</p>

            <h3>Thông tin đơn hàng</h3>
            <p>
                <strong>Mã đơn hàng:</strong> #{{ $order->id }}<br>
                <strong>Ngày đặt hàng:</strong> {{ $order->created_at->format('d/m/Y H:i') }}<br>
                <strong>Tên người nhận:</strong> {{ $order->receiver_name }}<br>
                <strong>Địa chỉ giao hàng:</strong> {{ $order->address }}<br>
                <strong>Số điện thoại:</strong> {{ $order->phone }}<br>
                <strong>Email:</strong> {{ $order->email }}<br>
                <strong>Ghi chú:</strong> {{ $order->note ?? 'Không có ghi chú' }}
            </p>

            <h3>Chi tiết sản phẩm:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Ảnh</th>
                        <th>Số lượng</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderDetails as $item)
                        <tr>
                            <td>{{ $item->product->product_name }} ({{ $item->size_and_color }})</td>
                            <td><img src="{{ $item->product->image }}" alt="{{ $item->product->product_name }}"
                                    width="50" height="50"></td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price) }} VNĐ</td>
                            <td>{{ number_format($item->quantity * $item->price) }} VNĐ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @php
                // Tính toán tổng tiền sản phẩm trước khi áp dụng VAT và chiết khấu
                $subtotalProducts = 0;
                foreach ($order->orderDetails as $item) {
                    $subtotalProducts += $item->quantity * $item->price;
                }

                $discountPercentage = 0;
                if ($order->orderDetails->isNotEmpty() && isset($order->orderDetails->first()->code)) {
                    // Giả sử item->code là giá trị phần trăm (ví dụ: 0.1 cho 10%)
                    // Nếu nó là số nguyên (ví dụ 10 cho 10%), hãy chia cho 100: $order->orderDetails->first()->code / 100
                    $discountPercentage = $order->orderDetails->first()->code; // Hoặc $order->orderDetails->first()->code / 100
                }

                $discountAmount = $subtotalProducts * $discountPercentage;
                $vatAmount = $order->VAT;
                $shippingFee = $order->shipping_fee;

                $finalTotal = $subtotalProducts - $discountAmount + $vatAmount + $shippingFee;
            @endphp

            <div class="summary-section">
                <p><strong>Tổng tiền sản phẩm:</strong> <span>{{ number_format($subtotalProducts) }} VNĐ</span></p>
                @if ($discountPercentage > 0)
                    <p><strong>Chiết khấu ({{ $discountPercentage * 100 }}%):</strong> <span>-{{ number_format($discountAmount) }} VNĐ</span></p>
                @endif
                <p><strong>Phí vận chuyển:</strong> <span>{{ number_format($shippingFee) }} VNĐ</span></p>
                <p><strong>VAT (10%):</strong> <span>{{ number_format($vatAmount) }} VNĐ</span></p>
                <p class="summary-row"><strong>Tổng tiền thanh toán:</strong> <span>{{ number_format($finalTotal) }} VNĐ</span></p>
            </div>

            <a href="{{ route('sites.showOrderDetailOfCustomer', $order->id) }}" class="call-to-action">
                Xem trạng thái đơn hàng của bạn
            </a>
        </div>
        <div class="footer">
            <p>Trân trọng,<br>**{{ config('app.name') }}**</p>
            <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi.</p>
        </div>
    </div>
</body>
</html>

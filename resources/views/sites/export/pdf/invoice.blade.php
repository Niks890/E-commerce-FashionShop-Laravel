<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn Đơn Hàng #{{ $orderDetail[0]->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 15px;
            /* Reduced padding */
            font-size: 10px;
            /* Reduced font size */
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 15px;
            /* Reduced padding */
            border: 1px solid #eee;
            background: #fff;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
            /* Lighter shadow */
            border-radius: 6px;
            /* Slightly smaller border radius */
        }

        .header-title {
            text-align: center;
            font-size: 20px;
            /* Reduced title size */
            font-weight: bold;
            margin-bottom: 10px;
            /* Reduced margin */
            color: #333;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            /* Reduced margin */
        }

        .info-box {
            width: 50%;
            background: #fdfdfd;
            padding: 12px;
            /* Reduced padding */
            border-radius: 6px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            /* Lighter shadow */
            vertical-align: top;
            border: 1px solid #eee;
        }

        .info-box:first-child {
            padding-right: 10px;
            /* Reduced space between info boxes */
        }

        .info-box h4 {
            margin-bottom: 8px;
            /* Reduced margin */
            text-transform: uppercase;
            font-size: 12px;
            /* Reduced font size for heading */
            color: #555;
            border-bottom: 1px solid #eee;
            padding-bottom: 6px;
            /* Reduced padding */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            /* Reduced margin */
            font-size: 9.5px;
            /* Reduced font size for table data */
        }

        th,
        td {
            padding: 6px 8px;
            /* Reduced padding */
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f8f8f8;
            font-weight: bold;
            color: #444;
        }

        .text-right {
            text-align: right;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            /* Reduced space before signatures */
        }

        .signature-box {
            width: 50%;
            text-align: center;
            padding-top: 30px;
            /* Reduced padding */
            padding-bottom: 50px;
            /* Reduced padding */
            vertical-align: top;
        }

        table {
            page-break-inside: avoid;
            word-wrap: break-word;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            /* Reduced margin */
            font-size: 10px;
            /* Reduced font size */
            border: 1px solid #eee;
        }

        .summary-table td {
            padding: 6px 8px;
            /* Reduced padding */
            border-bottom: 1px solid #eee;
        }

        .summary-table tr:last-child td {
            border-bottom: none;
        }

        .summary-table strong {
            font-size: 12px;
            /* Reduced font size for total */
            color: #333;
        }

        .product-image {
            width: 50px;
            /* Reduced image size */
            height: auto;
            border-radius: 3px;
            /* Slightly smaller border radius */
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header-title">HÓA ĐƠN MUA HÀNG</div>
        <p style="text-align: center; margin-bottom: 15px; color: #666;"><strong>Ngày in hóa đơn:</strong>
            {{ now()->format('d/m/Y H:i') }}</p>

        <table class="info-table">
            <tr>
                <td class="info-box">
                    <h4>Thông tin cửa hàng</h4>
                    <p><strong>Cửa hàng:</strong> TST Fashion</p>
                    <p><strong>Địa chỉ:</strong> 3/2, Ninh Kiều, Cần Thơ</p>
                    <p><strong>Số điện thoại:</strong> 0123 456 789</p>
                    <p><strong>Email:</strong> support@TSTfashion.com</p>
                </td>
                <td class="info-box" style="padding-left: 10px;">
                    <h4>Thông tin đơn hàng #{{ $orderDetail[0]->id }}</h4>
                    <p><strong>Mã đơn hàng:</strong> #{{ $orderDetail[0]->id }}</p>
                    <p><strong>Ngày đặt hàng:</strong>
                        {{ \Carbon\Carbon::parse($orderDetail[0]->created_at)->format('d/m/Y H:i') }}</p>
                    <p><strong>Khách hàng:</strong> {{ $orderDetail[0]->customer_name }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $orderDetail[0]->phone }}</p>
                    <p><strong>Email:</strong> {{ $orderDetail[0]->email }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $orderDetail[0]->address }}</p>
                    <p><strong>Thanh toán:</strong> {{ $orderDetail[0]->payment }}</p>
                    <p><strong>Ghi chú:</strong> {{ $orderDetail[0]->note }}</p>
                    <p><strong>Trạng thái đơn hàng:</strong> {{ $orderDetail[0]->status }}</p>
                </td>
            </tr>
        </table>


        <h3 style="text-align: center; margin-top: 25px; margin-bottom: 10px; color: #333;">CHI TIẾT ĐƠN HÀNG</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sản phẩm</th>
                    <th>SKU</th>
                    <th>Hình Ảnh</th>
                    <th>Size & Màu</th>
                    <th>Số lượng</th>
                    <th class="text-right">Đơn giá</th>
                    <th class="text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 1;
                    $totalPriceCart = 0;
                    $vat = 0.1;
                    $ship = 30000;
                    $discount = $orderDetail[0]->code ?? 0;
                @endphp
                @foreach ($orderDetail as $item)
                    @php
                        $subtotal = $item->quantity * $item->price * (1 - $discount);
                        $totalPriceCart += $subtotal;
                    @endphp
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $item->product_name . ' - #' . $item->product_id }}</td>
                        <td>{{ $item->sku }}</td>
                        <td><img src="{{ $item->image }}" class="product-image"></td>
                        <td>{{ $item->size . ' - ' . $item->color }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->price, 0, ',', '.') }} đ</td>
                        <td class="text-right">{{ number_format($subtotal, 0, ',', '.') }} đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            if ($totalPriceCart >= 500000) {
                $ship = 0;
            }
            $vatPrice = $totalPriceCart * $vat;
            $total = $totalPriceCart + $vatPrice + $ship;
        @endphp

        <table class="summary-table">
            <tr>
                <td>Tạm tính:</td>
                <td class="text-right">{{ number_format($totalPriceCart, 0, ',', '.') }} đ</td>
            </tr>
            <tr>
                <td>VAT (10%):</td>
                <td class="text-right">{{ number_format($vatPrice, 0, ',', '.') }} đ</td>
            </tr>
            <tr>
                <td>Phí vận chuyển:</td>
                <td class="text-right">{{ number_format($ship, 0, ',', '.') }} đ</td>
            </tr>
            <tr>
                <td>Chiết khấu:</td>
                <td class="text-right">{{ $discount * 100 }}%</td>
            </tr>
            <tr>
                <td><strong>Tổng thanh toán:</strong></td>
                <td class="text-right"><strong>{{ number_format($total, 0, ',', '.') }} đ</strong></td>
            </tr>
        </table>
        <div class="return-date">
            <p class="font-italic font-weight-bold">Ngày đặt hàng:
                {{ \Carbon\Carbon::parse($orderDetail[0]->created_at)->format('d/m/Y H:i') }}</p>
            <strong class="font-italic font-weight-bold">Ngày giao dự kiến:
                {{ \Carbon\Carbon::parse($orderDetail[0]->created_at)->addDays(3)->format('d/m/Y') }}</strong>
        </div>

        <table class="signature-table">
            <tr>
                <td class="signature-box">
                    <p><strong>Khách hàng</strong></p>
                    <p style="margin-top: 5px;">(Ký và ghi rõ họ tên)</p>
                    <p>{{ $orderDetail[0]->customer_name }}</p>
                </td>
                <td class="signature-box">
                    <p><strong>Nhân viên bán hàng</strong></p>
                    <p style="margin-top: 5px;">(Ký và ghi rõ họ tên)</p>
                </td>
            </tr>
        </table>
        <p style="text-align: center; margin-top: 15px; color: #666; font-style: italic;">Cảm ơn quý khách đã mua hàng
            tại TST Fashion!</p>
    </div>
</body>

</html>

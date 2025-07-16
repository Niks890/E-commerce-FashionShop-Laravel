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
            padding: 10px;
            font-size: 8px;
            position: relative;
        }

        .invoice-box {
            max-width: 600px;
            margin: auto;
            padding: 10px;
            border: 1px solid #eee;
            background: #fff;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
            border-radius: 4px;
            position: relative;
        }

        .header-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .info-box {
            width: 50%;
            background: #fdfdfd;
            padding: 8px;
            border-radius: 4px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            vertical-align: top;
            border: 1px solid #eee;
        }

        .info-box:first-child {
            padding-right: 8px;
        }

        .info-box h4 {
            margin-bottom: 6px;
            text-transform: uppercase;
            font-size: 9px;
            color: #555;
            border-bottom: 1px solid #eee;
            padding-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 7px;
        }

        th,
        td {
            padding: 4px 6px;
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
        }

        .signature-box {
            width: 50%;
            text-align: center;
            padding-top: 30px;
            padding-bottom: 50px;
            vertical-align: top;
        }

        table {
            page-break-inside: avoid;
            word-wrap: break-word;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8px;
            border: 1px solid #eee;
        }

        .summary-table td {
            padding: 4px 6px;
            border-bottom: 1px solid #eee;
        }

        .summary-table tr:last-child td {
            border-bottom: none;
        }

        .summary-table strong {
            font-size: 9px;
            color: #333;
        }

        .product-image {
            width: 35px;
            height: auto;
            border-radius: 2px;
            vertical-align: middle;
        }

        /* QR code style đã được chỉnh sửa */
        .qr-code-container {
            position: absolute;
            top: 10px;
            /* Điều chỉnh lại vị trí top */
            right: 10px;
            background: white;
            padding: 4px;
            /* Giảm padding */
            border-radius: 4px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
            text-align: center;
            width: 60px;
            /* Giảm width */
            height: 60px;
            /* Thêm height cố định */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .qr-code-image {
            width: 50px;
            /* Giảm kích thước ảnh */
            height: 50px;
            display: block;
        }

        .qr-code-text {
            margin-top: 2px;
            font-size: 5px;
            /* Giảm font size */
            color: #666;
            line-height: 1;
            font-weight: 500;
        }

        .header-section {
            position: relative;
            padding-right: 75px;
            /* Giảm padding để QR code gọn hơn */
            min-height: 70px;
            /* Điều chỉnh lại min-height */
            padding-top: 5px;
            /* Thêm padding top */
        }

        .return-date {
            margin-top: 20px;
            text-align: center;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header-section">
            <div class="header-title">HÓA ĐƠN MUA HÀNG</div>
            <p style="text-align: center; margin-bottom: 10px; color: #666; font-size: 7px;"><strong>Ngày in hóa
                    đơn:</strong>
                {{ now()->format('d/m/Y H:i') }}</p>

            <!-- QR Code được tạo từ server -->
            <div class="qr-code-container">
                <img src="{{ $qrCodeDataUri }}" class="qr-code-image" alt="QR Code">
                <p class="qr-code-text">Quét mã QR</p>
            </div>
        </div>

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

        <h3 style="text-align: center; margin-top: 15px; margin-bottom: 8px; color: #333; font-size: 10px;">CHI TIẾT ĐƠN
            HÀNG</h3>
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
            <p class="font-italic font-weight-bold" style="font-size: 7px;">Ngày đặt hàng:
                {{ \Carbon\Carbon::parse($orderDetail[0]->created_at)->format('d/m/Y H:i') }}</p>
            <strong class="font-italic font-weight-bold" style="font-size: 7px;">Ngày giao dự kiến:
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
        <p style="text-align: center; margin-top: 15px; color: #666; font-style: italic;">
            Cảm ơn quý khách đã mua hàng tại TST Fashion!
        </p>
    </div>
</body>

</html>

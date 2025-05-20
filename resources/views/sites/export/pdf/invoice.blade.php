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
            padding: 20px;
            font-size: 12px;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 15px;
            border: 1px solid #ddd;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .header-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .info-box {
            width: 50%;
            background: #f9f9f9;
            padding: 12px;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            vertical-align: top;
        }

        .info-box h4 {
            margin-bottom: 8px;
            text-transform: uppercase;
            font-size: 12px;
            color: #333;
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 11px;
        }

        th,
        td {
            padding: 6px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #f4f4f4;
        }

        .text-right {
            text-align: right;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .signature-box {
            width: 50%;
            text-align: center;
            padding-top: 40px;
            padding-bottom: 60px;
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
            font-size: 12px;
        }

        .summary-table td {
            padding: 6px;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header-title">HÓA ĐƠN MUA HÀNG</div>
        <p style="text-align: center;"><strong>Ngày in hóa đơn:</strong> {{ now()->format('d/m/Y') }}</p>
        <table class="info-table">
            <tr>
                <td class="info-box">
                    <h4>Thông tin cửa hàng</h4>
                    <p><strong>Cửa hàng:</strong> TST Fashion</p>
                    <p><strong>Địa chỉ:</strong> 3/2, Ninh Kiều, Cần Thơ</p>
                    <p><strong>Số điện thoại:</strong> 0123 456 789</p>
                    <p><strong>Email:</strong> support@TSTfashion.com</p>
                </td>
                <td class="info-box">
                    <h4>Thông tin đơn hàng #{{ $orderDetail[0]->id }}</h4>
                    <p><strong>Mã đơn hàng:</strong> #{{ $orderDetail[0]->id }}</p>
                    <p><strong>Ngày đặt hàng:</strong> {{ $orderDetail[0]->created_at }}</p>
                    <p><strong>Khách hàng:</strong> {{ $orderDetail[0]->customer_name }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $orderDetail[0]->phone }}</p>
                    <p><strong>Email:</strong> {{ $orderDetail[0]->email }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $orderDetail[0]->address }}</p>
                </td>
            </tr>
        </table>


        <h3 style="text-align: center">Chi tiết đơn hàng</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sản phẩm</th>
                    <th>Hình Ảnh</th>
                    <th>Size & Màu</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Thành tiền</th>
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
                        if ($totalPriceCart >= 500000) {
                            $ship = 0;
                        }
                        $vatPrice = $totalPriceCart * $vat;
                        $total = $totalPriceCart + $vatPrice + $ship;
                    @endphp
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $item->product_name . ' - #' . $item->product_id }}</td>
                        <td><img src="{{ $item->image }}" width="50" class="rounded"></td>
                        <td>{{ $item->size . ' - ' . $item->color }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price, 0, ',', '.') }} đ</td>
                        <td>{{ number_format($subtotal, 0, ',', '.') }} đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

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

        <table class="signature-table">
            <tr>
                <td class="signature-box">
                    <p><strong>Khách hàng</strong></p>
                    <p>(Ký và ghi rõ họ tên)</p>
                </td>
                <td class="signature-box">
                    <p><strong>Nhân viên bán hàng</strong></p>
                    <p>(Ký và ghi rõ họ tên)</p>
                </td>
            </tr>
        </table>
        <p style="text-align: center; margin-top: 10px;"><em>Cảm ơn quý khách đã mua hàng tại TST Fashion!</em></p>
    </div>
</body>

</html>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo Cáo Sản Phẩm Bán Chạy</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            line-height: 1.5;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .report-info {
            margin-bottom: 20px;
        }
        .report-info strong {
            display: inline-block;
            width: 120px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 6px 10px;
            text-align: left;
        }
        .variant-table {
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .variant-table td, .variant-table th {
            font-size: 12px;
        }
    </style>
</head>
<body>
    <h2>BÁO CÁO SẢN PHẨM BÁN CHẠY</h2>

    <div class="report-info">
        <p><strong>Từ ngày:</strong> {{ $from }}</p>
        <p><strong>Đến ngày:</strong> {{ $to }}</p>
        <p><strong>Thời gian xuất:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tên sản phẩm</th>
                <th>Tổng số lượng bán</th>
                <th>Tổng doanh thu</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product['product_name'] }}</td>
                    <td>{{ $product['total_sold'] }}</td>
                    <td>{{ number_format($product['total_revenue'], 0, ',', '.') }}₫</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <table class="variant-table">
                            <thead>
                                <tr>
                                    <th>Màu sắc</th>
                                    <th>Kích cỡ</th>
                                    <th>Số lượng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product['variants'] as $variant)
                                    <tr>
                                        <td>{{ $variant['color'] }}</td>
                                        <td>{{ $variant['size'] }}</td>
                                        <td>{{ $variant['quantity'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

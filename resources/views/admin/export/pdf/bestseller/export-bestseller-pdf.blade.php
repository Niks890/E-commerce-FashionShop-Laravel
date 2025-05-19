<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thống kê sản phẩm bán chạy</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            line-height: 1.6;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        .date-range {
            margin-bottom: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table,
        th,
        td {
            border: 1px solid #444;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    <h2>Thống kê sản phẩm bán chạy</h2>
    <div class="date-range">
        <strong>Khoảng thời gian:</strong>
        {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tên sản phẩm</th>
                <th>Số lượng bán</th>
                <th>Tổng doanh thu (₫)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $item['product_name'] }}</td>
        <td>{{ number_format($item['total_sold']) }}</td>
        <td>{{ number_format($item['total_revenue']) }}</td>
    </tr>

    @if(!empty($item['variants']))
        <tr>
            <td></td>
            <td colspan="3">
                <table style="width: 100%; border-collapse: collapse; margin-top: 5px;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #999; background: #eee;">Màu</th>
                            <th style="border: 1px solid #999; background: #eee;">Size</th>
                            <th style="border: 1px solid #999; background: #eee;">Số lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item['variants'] as $variant)
                            <tr>
                                <td style="border: 1px solid #ccc;">{{ $variant['color'] }}</td>
                                <td style="border: 1px solid #ccc;">{{ $variant['size'] }}</td>
                                <td style="border: 1px solid #ccc;">{{ $variant['quantity'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
    @endif
@endforeach


        </tbody>
    </table>
</body>

</html>

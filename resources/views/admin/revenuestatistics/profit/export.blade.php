<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-success { color: green; }
        .text-danger { color: red; }
        .total-row { font-weight: bold; }
    </style>
</head>
<body>
    <h2>{{ $title }}</h2>
    <table>
        <thead>
            <tr>
                <th>Thời gian</th>
                <th>Doanh thu</th>
                <th>Chi phí</th>
                <th>Lợi nhuận</th>
            </tr>
        </thead>
        <tbody>
            @foreach($profitData as $data)
            <tr>
                <td>{{ $data['label'] }}</td>
                <td>{{ number_format($data['doanhthu']) }} VNĐ</td>
                <td>{{ number_format($data['chiphi']) }} VNĐ</td>
                <td class="{{ $data['loiNhuan'] >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($data['loiNhuan']) }} VNĐ
                </td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td>Tổng cộng</td>
                <td>{{ number_format($totalRevenue) }} VNĐ</td>
                <td>{{ number_format($totalCost) }} VNĐ</td>
                <td class="{{ $totalProfit >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($totalProfit) }} VNĐ
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>

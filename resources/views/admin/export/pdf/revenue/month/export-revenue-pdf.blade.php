<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        .total-row td {
            font-weight: bold;
            background-color: #e8f5e9;
        }

        .right-align {
            text-align: right;
        }
    </style>
</head>
<body>

    <h2>Doanh thu theo {{ ucfirst($period) }} - Năm {{ $year }}</h2>

    <table>
        <thead>
            <tr>
                <th>Thời gian</th>
                <th>Doanh thu (VNĐ)</th>
            </tr>
        </thead>
        <tbody>
            @php $sum = 0; @endphp
            @foreach ($labels as $key => $label)
                @php $sum += $totals[$key]; @endphp
                <tr>
                    <td>{{ $label }}</td>
                    <td class="right-align">{{ number_format($totals[$key], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td>Tổng cộng</td>
                <td class="right-align">{{ number_format($sum, 0, ',', '.') }} VNĐ</td>
            </tr>
        </tbody>
    </table>

</body>
</html>

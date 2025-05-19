<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Thống kê doanh thu</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        h3 {
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <h3>Thống kê doanh thu từ {{ $from }} đến {{ $to }}</h3>
    <table>
        <thead>
            <tr>
                <th>Ngày</th>
                <th>Tổng tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row->ngaytao)->format('d/m/Y') }}</td>
                    <td>{{ number_format($row->tongtien) }} đ</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

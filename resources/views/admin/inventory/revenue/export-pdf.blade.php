<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Báo cáo tồn kho</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; }
        .subtitle { font-size: 12px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #2980b9; color: white; padding: 8px; text-align: center; }
        td { padding: 6px; border: 1px solid #ddd; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-danger { color: #e74c3c; }
        .text-warning { color: #f39c12; }
        .text-success { color: #27ae60; }
        .footer { margin-top: 20px; font-size: 10px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">BÁO CÁO TỒN KHO HIỆN TẠI</div>
        <div class="subtitle">
            Ngày xuất: {{ $export_time }} |
            Tổng sản phẩm: {{ $inventory->count() }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên sản phẩm</th>
                <th>Mã SP</th>
                <th>Màu</th>
                <th>Size</th>
                <th>Số lượng</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventory as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->product_name }}</td>
                <td class="text-center">{{ $item->product_id }}</td>
                <td class="text-center">{{ $item->color }}</td>
                <td class="text-center">{{ $item->size }}</td>
                <td class="text-center
                    @if($item->stock <= 0) text-danger
                    @elseif($item->stock < 5) text-warning
                    @else text-success @endif">
                    {{ $item->stock }}
                </td>
                <td class="text-center">
                    @if($item->stock <= 0)
                        <span>Hết hàng</span>
                    @elseif($item->stock < 5)
                        <span>Gần hết</span>
                    @else
                        <span>Còn hàng</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Trang quản lý TFashionShop | Hệ thống quản lý kho hàng
    </div>
</body>
</html

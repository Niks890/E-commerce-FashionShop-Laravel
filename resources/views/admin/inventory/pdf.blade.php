<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Phiếu nhập hàng #{{ $inventory->id }}</title>
    <style>
        body {
            /* Sử dụng font hỗ trợ tiếng Việt tốt nhất cho PDF */
            font-family: "DejaVu Sans", "Times New Roman", Times, serif;
            font-size: 11px;
            /* Giảm cỡ chữ tổng thể */
            line-height: 1.3;
            /* Giảm khoảng cách dòng */
            color: #333;
            margin: 0;
            padding: 10px;
            /* Giảm padding trang */
        }

        .container {
            max-width: 950px;
            /* Giữ nguyên hoặc giảm nhẹ nếu cần */
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 15px;
            /* Giảm padding container */
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
            /* Giảm độ đậm bóng */
            position: relative;
        }

        .company-info {
            text-align: center;
            margin-bottom: 15px;
            /* Giảm margin dưới */
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
            /* Giảm padding dưới */
        }

        .company-name {
            font-size: 18px;
            /* Giảm cỡ chữ tên công ty */
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            color: #2c3e50;
        }

        .company-details {
            font-size: 10px;
            /* Giảm cỡ chữ chi tiết công ty */
            margin: 2px 0 0;
        }

        .header {
            text-align: center;
            margin: 10px 0 15px;
            /* Giảm margin */
        }

        .title {
            font-size: 16px;
            /* Giảm cỡ chữ tiêu đề phiếu */
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
            color: #2c3e50;
            position: relative;
            display: inline-block;
            padding: 0 10px;
            /* Giảm padding */
        }

        .title:before,
        .title:after {
            content: "";
            position: absolute;
            height: 1px;
            border-top: 1px solid #ccc;
            top: 50%;
            width: 30px;
            /* Giảm chiều dài đường kẻ */
        }

        .title:before {
            left: -30px;
        }

        .title:after {
            right: -30px;
        }

        .document-number {
            font-style: italic;
            margin-top: 3px;
            font-size: 10px;
            /* Giảm cỡ chữ số phiếu */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            /* Giảm margin dưới bảng */
        }

        .info-table td {
            padding: 4px 6px;
            /* Giảm padding ô thông tin */
            border: 1px solid #eee;
            vertical-align: top;
        }

        .info-table .label {
            font-weight: bold;
            width: 20%;
            /* Rút ngắn width label */
            background-color: #f8f9fa;
        }

        .product-table th,
        .product-table td {
            padding: 4px 6px;
            /* Giảm padding ô sản phẩm */
            border: 1px solid #ddd;
            text-align: center;
        }

        .product-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            /* Giảm cỡ chữ header bảng sản phẩm */
        }

        .product-table tbody tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        .summary-table {
            width: 45%;
            /* Giảm chiều rộng bảng tổng kết */
            margin-left: auto;
            margin-top: 10px;
            /* Giảm margin trên */
        }

        .summary-table td {
            padding: 4px 8px;
            /* Giảm padding ô tổng kết */
            border: 1px solid #ddd;
        }

        .summary-table .label {
            font-weight: bold;
            background-color: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .signature-group {
            margin-top: 30px;
            /* Giảm margin trên nhóm chữ ký */
            page-break-inside: avoid;
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .signature-column {
            width: 33.33%;
            text-align: center;
            display: table-cell;
            padding: 0 3px;
            /* Giảm padding giữa các cột */
            vertical-align: top;
        }

        .signature-line {
            height: 1px;
            border-top: 1px dashed #bbb;
            margin: 30px auto 5px;
            /* Giảm margin trên/dưới đường kẻ */
            width: 60%;
            /* Giảm chiều dài đường kẻ */
        }

        .notes {
            margin-top: 20px;
            /* Giảm margin trên ghi chú */
            padding: 6px;
            /* Giảm padding ghi chú */
            border: 1px dashed #eee;
            background-color: #fafafa;
            page-break-before: auto;
            page-break-after: avoid;
            font-size: 10px;
            /* Giảm cỡ chữ ghi chú */
        }

        .total-amount {
            font-weight: bold;
            font-size: 12px;
            /* Giữ cỡ chữ tổng tiền vừa phải */
            color: #c00;
        }

        .watermark {
            position: absolute;
            opacity: 0.05;
            /* Tăng độ mờ của watermark */
            font-size: 60px;
            /* Giảm cỡ chữ watermark */
            color: #ccc;
            transform: rotate(-45deg);
            top: 45%;
            left: 10%;
            /* Điều chỉnh vị trí */
            width: 80%;
            text-align: center;
            z-index: -1;
            white-space: nowrap;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="watermark">PHIẾU NHẬP HÀNG</div>
        <p style="text-align: center; margin-bottom: 15px; color: #666;"><strong>Ngày in phiếu nhập:</strong>
            {{ now()->format('d/m/Y/ H:i') }}</p>

        <div class="company-info">
            <h1 class="company-name">Công ty TNHH TFashionShop</h1>
            <p class="company-details">
                Địa chỉ: 3/2, Phường Xuân Khánh, Quận Ninh Kiều, TP. Cần Thơ<br>
                Điện thoại: 0292.3.789.123 - Email: tfashionshop@gmail.com<br>
                Mã số thuế: 0123456789
            </p>
        </div>

        <div class="header">
            <div class="title">Phiếu nhập hàng</div>
            <div class="document-number">Số: #{{ $inventory->id }}</div>
        </div>

        <table class="info-table">
            <tr>
                <td class="label">NV lập phiếu:</td>
                <td>{{ $inventory->staff->name }}</td>
                <td class="label">Ngày tạo:</td>
                <td>{{ $createdDate }}</td>
            </tr>
            <tr>
                <td class="label">Nhà CC:</td>
                <td>{{ $inventory->provider->name }} - #{{ $inventory->provider->id }}</td>

            </tr>
            <tr>
                @if ($inventory->status == 'approved' && $inventory->approved_by != null)
                    <td class="label">Ngày cập nhật (Duyệt):</td>
                    <td>{{ $updatedDate }}</td>
                    <td class="label">Người duyệt phiếu:</td>
                    <td>{{ $inventory->approvedBy->name }}</td>
                @endif
            </tr>
            <tr>
                <td class="label">Đ/c NCC:</td>
                <td>{{ $inventory->provider->address ?? 'N/A' }}</td>
                <td class="label">Trạng thái:</td>
                <td>
                    @if ($inventory->status == 'approved')
                        <span style="color: #28a745;">● Đã duyệt</span>
                    @elseif ($inventory->status == 'pending')
                        <span style="color: #ffc107;">● Chờ duyệt</span>
                    @else
                        <span style="color: #dc3545;">● Đã huỷ</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">SĐT NCC:</td>
                <td>{{ $inventory->provider->phone ?? 'N/A' }}</td>
                <td class="label">Tổng tiền:</td>
                <td class="total-amount">{{ number_format($inventory->total) }} đ</td>
            </tr>
        </table>

        <h4 style="margin: 12px 0 6px; border-bottom: 1px solid #eee; padding-bottom: 3px; font-size: 12px; text-align: center;">CHI TIẾT
            HÀNG HÓA NHẬP</h4>

        <table class="product-table">
            <thead>
                <tr>
                    <th width="4%">STT</th>
                    <th width="22%">Tên SP</th>
                    <th width="12%">Thương hiệu</th>
                    <th>SKU</th>
                    <th width="12%">Danh mục</th>
                    <th width="12%">Màu/Size</th>
                    <th width="8%">SL</th>
                    <th width="15%">Giá nhập</th>
                    <th width="15%">Thành tiền</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($inventory->InventoryDetails as $index => $InventoryDetails)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-left">{{ $InventoryDetails->product->product_name }}</td>
                        <td>{{ $InventoryDetails->product->brand ?? 'N/A' }}</td>
                        <td>{{ $InventoryDetails->product->sku ?? 'N/A' }}</td>
                        <td>{{ $InventoryDetails->product->Category->category_name ?? 'N/A' }}</td>
                        <td>
                            @if ($InventoryDetails->ProductVariant)
                                {{ $InventoryDetails->ProductVariant->color ?? 'N/A' }}/{{ $InventoryDetails->ProductVariant->size ?? 'N/A' }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $InventoryDetails->quantity ?? 'N/A' }}</td>
                        <td class="text-right">{{ number_format($InventoryDetails->price) ?? 'N/A' }} đ</td>
                        <td class="text-right">
                            {{ number_format(($InventoryDetails->price ?? 0) * ($InventoryDetails->quantity ?? 0)) }} đ
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="summary-table">
            <tr>
                <td class="label">Tổng tiền hàng:</td>
                <td class="text-right">
                    {{ number_format($inventory->InventoryDetails->sum(function ($detail) {return ($detail->price ?? 0) * ($detail->quantity ?? 0);})) }}
                    đ</td>
            </tr>
            <tr>
                <td class="label">Chiết khấu:</td>
                <td class="text-right">{{ $inventory->discount ? number_format($inventory->discount) . ' đ' : '0 đ' }}
                </td>
            </tr>
            <tr>
                <td class="label">VAT ({{ ($inventory->vat_rate ?? 0) * 100 }}%):</td>
                <td class="text-right">{{ $inventory->vat ? number_format($inventory->vat) . ' đ' : '0 đ' }}</td>
            </tr>
            <tr>
                <td class="label">Tổng cộng:</td>
                <td class="text-right total-amount">{{ number_format($inventory->total) }} đ</td>
            </tr>
        </table>

        <div class="signature-group">
            <div class="signature-column">
                <p><strong>NGƯỜI GIAO HÀNG</strong></p>
                <div class="signature-line"></div>
                <p style="font-size: 9px;">(Ký, ghi rõ họ tên)</p>
            </div>
            <div class="signature-column">
                <p><strong>THỦ KHO DUYỆT PHIẾU</strong></p>
                <div class="signature-line"></div>
                <p style="font-size: 9px;">(Ký, ghi rõ họ tên)</p>
                <p>{{ $inventory->approvedBy->name ?? 'N/A' }}</p>
            </div>
            <div class="signature-column">
                <p><strong>NGƯỜI LẬP PHIẾU</strong></p>
                <div class="signature-line"></div>
                <p style="font-size: 9px;">(Ký, ghi rõ họ tên)</p>
                <p>{{ $inventory->Staff->name ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="notes">
            <p><strong>Ghi chú:</strong> {{ $inventory->note ?? 'Không có ghi chú.' }}</p>
        </div>

        <div style="text-align: center; margin-top: 8px; font-style: italic; font-size: 9px; color: #555;">
            Phiếu này có giá trị khi có đầy đủ chữ ký của các bên liên quan
        </div>
    </div>
</body>

</html>

<!DOCTYPE html>
<html>
<head>
    <title>Đơn hàng #{{ $order->id }} giao thành công</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 650px; margin: 0 auto; background: #ffffff; }
        .header { background-color: #4CAF50; color: white; padding: 30px 20px; text-align: center; }
        .content { padding: 30px; }
        .order-summary { margin-bottom: 25px; }
        .product-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .product-table th { background: #f5f5f5; text-align: left; padding: 12px; font-weight: 600; }
        .product-table td { padding: 12px; border-bottom: 1px solid #eee; vertical-align: top; }
        .product-image { width: 70px; height: 70px; object-fit: cover; border-radius: 4px; }
        .price-section { background: #f9f9f9; padding: 20px; margin-top: 20px; border-radius: 4px; }
        .price-row { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .total-row { font-weight: bold; font-size: 1.1em; border-top: 1px solid #ddd; padding-top: 12px; margin-top: 12px; }
        .footer { text-align: center; padding: 20px; color: #777; font-size: 14px; background: #f5f5f5; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin:0;font-size:24px;">ĐƠN HÀNG #{{ $order->id }} ĐÃ GIAO THÀNH CÔNG</h1>
        </div>

        <div class="content">
            <p>Xin chào <strong>{{ $order->receiver_name }}</strong>,</p>

            <p>Cảm ơn bạn đã mua sắm tại {{ config('app.name') }}. Đơn hàng của bạn đã được giao thành công.</p>

            <div class="order-summary">
                <h3 style="margin-top:0;">Thông tin đơn hàng</h3>
                <p><strong>Mã đơn hàng:</strong> #{{ $order->id }}</p>
                <p><strong>Ngày đặt hàng:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Ngày giao hàng:</strong> {{ now()->format('d/m/Y') }}</p>
                <p><strong>Địa chỉ giao hàng:</strong> {{ $order->address }}</p>
                <p><strong>Phương thức thanh toán:</strong> {{ $order->payment }}</p>
            </div>

            <h3>Chi tiết sản phẩm</h3>
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $subtotal = 0;
                        $totalDiscount = 0;
                    @endphp

                    @foreach($order->orderDetails as $item)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                @if($item->product->image)
                                <img src="{{$item->product->image}}" class="product-image" alt="{{ $item->product->product_name }}">
                                @endif
                                <div>
                                    <div>{{ $item->product->product_name }}</div>
                                    @if($item->productVariant)
                                    <small style="color:#666;">{{ $item->productVariant->color }} / {{ $item->productVariant->size }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ number_format($item->price, 0, ',', '.') }}đ</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</td>
                    </tr>
                    @php
                        $subtotal += $item->price * $item->quantity;
                        $totalDiscount += $item->price * $item->quantity * ($item->code ?? 0);
                    @endphp
                    @endforeach
                </tbody>
            </table>

            <div class="price-section">
                <div class="price-row">
                    <span>Tạm tính:</span>
                    <span>{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                </div>

                @if($totalDiscount > 0)
                <div class="price-row">
                    <span>Chiết khấu ({{ number_format($order->orderDetails->first()->code * 100, 0) }}%):</span>
                    <span>-{{ number_format($totalDiscount, 0, ',', '.') }}đ</span>
                </div>
                @endif

                @php
                    $vatRate = 0.1;
                    $vat = ($subtotal - $totalDiscount) * $vatRate;
                @endphp

                <div class="price-row">
                    <span>VAT (10%):</span>
                    <span>{{ number_format($vat, 0, ',', '.') }}đ</span>
                </div>

                @if($order->shipping_fee > 0)
                <div class="price-row">
                    <span>Phí vận chuyển:</span>
                    <span>{{ number_format($order->shipping_fee, 0, ',', '.') }}đ</span>
                </div>
                @endif

                @php
                    $total = $subtotal - $totalDiscount + $vat + $order->shipping_fee;
                @endphp

                <div class="price-row total-row">
                    <span>Tổng cộng:</span>
                    <span>{{ number_format($total, 0, ',', '.') }}đ</span>
                </div>
            </div>

            <p style="margin-top:25px;">Nếu có bất kỳ vấn đề gì với đơn hàng, vui lòng liên hệ với chúng tôi trong vòng 7 ngày.</p>

            <p>Trân trọng,<br>
            <strong>{{ config('app.name') }}</strong></p>
        </div>

        <div class="footer">
            <p>Đây là email tự động, vui lòng không trả lời.</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

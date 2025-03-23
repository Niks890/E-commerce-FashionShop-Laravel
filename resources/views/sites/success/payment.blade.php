<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán thành công</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .success-container {
            text-align: center;
            margin-top: 80px;
        }
        .card {
            max-width: 550px;
            margin: auto;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            background-color: #fff;
        }
        .success-icon {
            font-size: 70px;
            color: #28a745;
            margin-bottom: 15px;
        }
        .vnpay-logo {
            max-width: 120px;
            display: block;
            margin: 0 auto 15px;
        }
        .order-info p {
            margin-bottom: 8px;
            font-size: 16px;
        }
        .btn-custom {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
        }
        .btn + .btn {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container success-container">
        <div class="card">
            <div class="card-body">
                <img src="{{ asset('client/img/payment/' . Session::get('success_data')['logo']) }}" alt="Logo" class="vnpay-logo">
                <i class="fa-solid fa-circle-check success-icon"></i>
                <h2 class="text-success">Thanh toán thành công!</h2>
                <p class="text-muted">Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đang được xử lý.</p>
                <hr>
                <div class="order-info text-left">
                    <p><strong>🧑 Họ tên:</strong> {{ Session::get('success_data')['receiver_name'] }}</p>
                    <p><strong>📦 Mã đơn hàng:</strong> {{ Session::get('success_data')['order_id'] }}</p>
                    <p><strong>⏰ Thời gian:</strong> {{ now()->format('d/m/Y - H:i') }}</p>
                    <p><strong>💰 Số tiền thanh toán:</strong> {{ number_format(Session::get('success_data')['total'], 0, ',', '.') . ' đ' }}</p>
                    <p><strong>✅ Trạng thái:</strong> Thành công</p>
                </div>
                <hr>
                <p class="text-muted">📧 Email xác nhận đã được gửi đến bạn.</p>
                <p class="text-muted">Mọi vấn đề vui lòng liên hệ <strong>1900 1234</strong>.</p>
                <a href="{{ route('sites.home') }}" class="btn btn-primary btn-custom">🏠 Quay lại trang chủ</a>
                <a href="{{ route('sites.showOrderDetailOfCustomer', Session::get('success_data')['order_id']) }}" class="btn btn-secondary btn-custom">📜 Xem đơn hàng</a>
                <a href="{{ route('sites.home') }}#product-list-home" class="btn btn-success btn-custom">🛒 Tiếp tục mua sắm</a>
            </div>
        </div>
    </div>
</body>
</html>

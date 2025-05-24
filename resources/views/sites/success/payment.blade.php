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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .success-container {
            padding: 40px 15px;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card {
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: none;
            overflow: hidden;
            position: relative;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, #28a745, #20c997);
        }
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin: 15px 0;
            text-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
            animation: bounce 1s ease;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-20px);}
            60% {transform: translateY(-10px);}
        }
        .vnpay-logo {
            max-width: 140px;
            display: block;
            margin: 0 auto 20px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }
        .order-info {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
        }
        .order-info p {
            margin-bottom: 12px;
            font-size: 16px;
            display: flex;
            align-items: center;
        }
        .order-info strong {
            min-width: 160px;
            display: inline-block;
            color: #495057;
        }
        .btn-custom {
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 8px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-custom i {
            margin-right: 8px;
            font-size: 18px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .contact-info {
            margin: 20px 0;
            padding: 15px;
            background-color: #e9f7ef;
            border-radius: 8px;
            color: #28a745;
            font-weight: 500;
        }
        .thank-you-text {
            font-size: 18px;
            color: #6c757d;
            margin-bottom: 25px;
        }
        .status-badge {
            background-color: #d4edda;
            color: #155724;
            padding: 4px 12px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container success-container">
        <div class="card">
            <div class="card-body text-center">
                <img src="{{ asset('client/img/payment/' . Session::get('success_data')['logo']) }}" alt="Logo" class="vnpay-logo">
                <i class="fa-solid fa-circle-check success-icon"></i>
                <h2 class="text-success mb-3" style="font-weight: 700;">Thanh toán thành công!</h2>
                <p class="thank-you-text">Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đang được xử lý.</p>

                <div class="order-info text-left">
                    <p><strong><i class="fas fa-user"></i> Họ tên:</strong> {{ Session::get('success_data')['receiver_name'] }}</p>
                    <p><strong><i class="fas fa-receipt"></i> Mã đơn hàng:</strong> #{{ Session::get('success_data')['order_id'] }}</p>
                    <p><strong><i class="far fa-clock"></i> Thời gian:</strong> {{ now()->format('d/m/Y - H:i') }}</p>
                    <p><strong><i class="fas fa-money-bill-wave"></i> Số tiền:</strong> {{ number_format(Session::get('success_data')['total'], 0, ',', '.') }} đ</p>
                    <p><strong><i class="fas fa-check-circle"></i> Trạng thái:</strong> <span class="status-badge">Thành công</span></p>
                </div>

                <div class="contact-info">
                    <i class="fas fa-envelope"></i> Email xác nhận đã được gửi đến bạn bạn điền trong biểu mẫu<br>
                    <i class="fas fa-phone-alt"></i> Mọi vấn đề vui lòng liên hệ <strong>1900 1234</strong>
                </div>

                <div class="button-group">
                    <a href="{{ route('sites.home') }}" class="btn btn-primary btn-custom">
                        <i class="fas fa-home"></i> Trang chủ
                    </a>
                    <a href="{{ route('sites.showOrderDetailOfCustomer', Session::get('success_data')['order_id']) }}" class="btn btn-secondary btn-custom">
                        <i class="fas fa-file-invoice"></i> Chi tiết đơn hàng
                    </a>
                    <a href="{{ route('sites.home') }}#product-list-home" class="btn btn-success btn-custom">
                        <i class="fas fa-shopping-cart"></i> Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

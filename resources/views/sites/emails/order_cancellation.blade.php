<!DOCTYPE html>
<html>
<head>
    <title>Xác nhận hủy đơn hàng #{{ $order->id }}</title>
    <style>
        /* Base styles */
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        /* Email container */
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Header */
        .email-header {
            background-color: #dc3545;
            color: white;
            padding: 25px 20px;
            text-align: center;
            border-bottom: 4px solid #c82333;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        /* Content */
        .email-content {
            padding: 30px;
        }

        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .message {
            margin-bottom: 25px;
            font-size: 16px;
            color: #4a5568;
        }

        .order-info {
            background-color: #f8f9fa;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }

        .reason-box {
            background-color: #fff8f8;
            border: 1px solid #ffdddd;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }

        .reason-title {
            font-weight: 600;
            color: #dc3545;
            margin-bottom: 8px;
        }

        .action-text {
            margin: 25px 0;
            font-size: 15px;
        }

        /* Footer */
        .email-footer {
            padding: 20px;
            text-align: center;
            background-color: #f8f9fa;
            border-top: 1px solid #eaeaea;
            font-size: 14px;
            color: #718096;
        }

        .company-name {
            font-weight: 600;
            color: #2d3748;
            margin-top: 5px;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }

            .email-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>XÁC NHẬN HỦY ĐƠN HÀNG</h1>
        </div>

        <!-- Content -->
        <div class="email-content">
            <p class="greeting">Xin chào {{ $order->receiver_name }},</p>

            <div class="order-info">
                <p>Mã đơn hàng: <strong>#{{ $order->id }}</strong></p>
                <p>Ngày hủy: <strong>{{ now()->format('d/m/Y H:i') }}</strong></p>
            </div>

            <p class="message">Chúng tôi xác nhận đơn hàng của bạn đã được hủy thành công theo yêu cầu.</p>

            @if($order->reason)
                <div class="reason-box">
                    <div class="reason-title">Lý do hủy đơn hàng:</div>
                    <p>{{ $order->reason }}</p>
                </div>
            @endif

            <p class="action-text">
                Nếu bạn không thực hiện thao tác này hoặc cần hỗ trợ thêm,
                vui lòng liên hệ với chúng tôi qua email hoặc số điện thoại hỗ trợ khách hàng.
            </p>

            <p>Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ của chúng tôi.</p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>Trân trọng,</p>
            <p class="company-name">{{ config('app.name') }}</p>
            <p>
                <small>Đây là email tự động, vui lòng không trả lời.</small>
            </p>
        </div>
    </div>
</body>
</html>

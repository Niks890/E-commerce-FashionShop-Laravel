<!DOCTYPE html>
<html>

<head>
    <title>Voucher của bạn đã đến!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .header {
            background-color: #f8f8f8;
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .content {
            margin-top: 20px;
        }

        .voucher-details {
            background-color: #e6ffe6;
            border: 1px dashed #00cc00;
            padding: 15px;
            text-align: center;
            margin-top: 20px;
        }

        .voucher-code {
            font-size: 2em;
            font-weight: bold;
            color: #008000;
            margin-bottom: 10px;
        }

        .footer {
            margin-top: 30px;
            font-size: 0.9em;
            color: #777;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Xin chào {{ $customerName }}!</h2>
        </div>
        <div class="content">
            <p>Chúng tôi xin thông báo rằng bạn vừa nhận được một voucher đặc biệt từ chúng tôi. Cảm ơn bạn đã luôn ủng
                hộ!</p>

            @if ($messageContent)
                <p>Lời nhắn từ người gửi:</p>
                <p style="font-style: italic; border-left: 3px solid #008000; padding-left: 10px;">
                    "{{ $messageContent }}"</p>
            @endif

            <div class="voucher-details">
                <p>Voucher của bạn là:</p>
                <div class="voucher-code">{{ $voucherCode }}</div>
                <p>{{ $voucherDescription }}</p>
                <p>Hãy sử dụng voucher này để nhận ưu đãi đặc biệt của chúng tôi!</p>
                <p>Ngày hết hạn voucher: {{ $expiryDate }}</p>
            </div>

            <p>Nếu bạn có bất kỳ câu hỏi nào, đừng ngần ngại liên hệ với chúng tôi.</p>
            <p>Trân trọng,</p>
            <p>Đội ngũ của chúng tôi</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Tên công ty của bạn. All rights reserved.</p>
        </div>
    </div>
</body>

</html>

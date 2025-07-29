<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin liên hệ từ khách hàng</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            position: relative;
        }

        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
            position: relative;
            z-index: 2;
        }

        .header .subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            position: relative;
            z-index: 2;
        }

        .content {
            padding: 40px 30px;
        }

        .info-card {
            background: #f8fafc;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            border-left: 5px solid #4f46e5;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #4f46e5, #7c3aed, #ec4899);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .info-card:hover::before {
            transform: scaleX(1);
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.1);
        }

        .info-label {
            display: flex;
            align-items: center;
            font-weight: 600;
            color: #374151;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .info-label .icon {
            margin-right: 10px;
            width: 20px;
            height: 20px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
        }

        .info-value {
            color: #6b7280;
            font-size: 15px;
            line-height: 1.6;
            padding-left: 30px;
        }

        .message-content {
            background: #ffffff;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-top: 10px;
            font-size: 15px;
            line-height: 1.7;
            color: #374151;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
        }

        .footer {
            background: #f9fafb;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .footer p {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .footer .brand {
            color: #4f46e5;
            font-weight: 600;
            text-decoration: none;
        }

        .timestamp {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
            margin-top: 10px;
        }

        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 15px;
            }

            .header, .content, .footer {
                padding: 25px 20px;
            }

            .header h1 {
                font-size: 20px;
            }

            .info-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>📧 Thông tin liên hệ mới</h1>
            <p class="subtitle">Từ khách hàng: <strong>{{ $data['name'] }}</strong></p>
        </div>

        <div class="content">
            <div class="info-card">
                <div class="info-label">
                    <span class="icon">👤</span>
                    Tên khách hàng
                </div>
                <div class="info-value">{{ $data['name'] }}</div>
            </div>

            <div class="info-card">
                <div class="info-label">
                    <span class="icon">✉️</span>
                    Địa chỉ email
                </div>
                <div class="info-value">
                    <a href="mailto:{{ $data['email'] }}" style="color: #4f46e5; text-decoration: none;">
                        {{ $data['email'] }}
                    </a>
                </div>
            </div>

            <div class="info-card">
                <div class="info-label">
                    <span class="icon">💬</span>
                    Nội dung liên hệ
                </div>
                <div class="message-content">
                    {{ $data['message'] }}
                </div>
            </div>

            <div class="timestamp">
                📅 Nhận lúc: {{ now()->format('d/m/Y H:i:s') }}
            </div>
        </div>

        <div class="footer">
            <p>Email này được gửi tự động từ hệ thống liên hệ của website.</p>
            <p>Vui lòng phản hồi khách hàng trong thời gian sớm nhất.</p>
            <a href="#" class="brand">🏢 TFashionshop</a>
        </div>
    </div>
</body>
</html>

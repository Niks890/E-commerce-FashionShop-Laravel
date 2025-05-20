<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #a8dadc, #457b9d); /* Màu nền gradient dịu mắt hơn */
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            overflow: hidden; /* Ngăn cuộn trang */
        }

        .login-container {
            max-width: 420px; /* Tăng kích thước tối đa một chút */
            width: 90%; /* Đảm bảo responsive tốt hơn */
            padding: 2.5rem; /* Tăng padding */
            background: #ffffff;
            border-radius: 20px; /* Bo tròn nhiều hơn */
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15); /* Shadow nổi bật hơn */
            animation: fadeIn 0.8s ease-out; /* Hiệu ứng fade-in khi tải trang */
            position: relative;
            overflow: hidden;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
            background: rgba(69, 123, 157, 0.1); /* Hình dạng trang trí */
            border-radius: 50%;
            z-index: 0;
        }

        .login-container::after {
            content: '';
            position: absolute;
            bottom: -30px;
            right: -30px;
            width: 100px;
            height: 100px;
            background: rgba(168, 218, 220, 0.1); /* Hình dạng trang trí */
            border-radius: 50%;
            z-index: 0;
        }

        .form-control {
            border-radius: 10px; /* Bo tròn các input */
            padding: 0.75rem 1rem; /* Tăng padding input */
            border: 1px solid #ced4da;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #457b9d; /* Màu border khi focus hài hòa hơn */
            box-shadow: 0 0 0 0.2rem rgba(69, 123, 157, 0.25); /* Shadow khi focus */
            background-color: #f8f9fa; /* Nền nhạt khi focus */
        }

        .btn-primary {
            background-color: #1d3557; /* Màu nút chính mạnh mẽ hơn */
            border-color: #1d3557;
            border-radius: 10px;
            font-weight: 600;
            padding: 0.75rem 1.25rem; /* Tăng padding nút */
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #457b9d; /* Màu hover dịu hơn */
            border-color: #457b9d;
            transform: translateY(-2px); /* Hiệu ứng nhấc nhẹ */
        }

        .form-label {
            font-weight: 600; /* Tăng độ đậm label */
            color: #34495e; /* Màu label đậm hơn */
            margin-bottom: 0.5rem;
        }

        .text-small {
            font-size: 0.9rem; /* Tăng kích thước chữ nhỏ */
            color: #6c757d;
        }

        .text-primary {
            color: #1d3557 !important; /* Đổi màu tiêu đề chính */
        }

        .text-danger {
            font-size: 0.85rem;
            margin-top: 0.25rem;
            display: block; /* Đảm bảo lỗi nằm trên dòng riêng */
        }

        .d-flex.justify-content-between.align-items-center.mb-3 a {
            color: #457b9d; /* Màu link quên mật khẩu */
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .d-flex.justify-content-between.align-items-center.mb-3 a:hover {
            color: #1d3557;
        }

        .text-center.mt-3 a {
            color: #457b9d; /* Màu link về trang chủ */
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .text-center.mt-3 a:hover {
            color: #1d3557;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .login-container {
                padding: 1.5rem;
            }
            .btn-primary {
                padding: 0.6rem 1rem;
                font-size: 0.95rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h3 class="text-center mb-5 text-primary">Đăng nhập hệ thống</h3>
        <form method="POST" action="{{ route('admin.post_login') }}">
            @csrf
            <div class="mb-4">
                <label for="exampleInputLogin" class="form-label">Email hoặc Tên đăng nhập</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input name="login" type="text" class="form-control" id="exampleInputLogin"
                        placeholder="Nhập email hoặc username" value="{{ old('login') }}">
                </div>
                @error('login')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-4">
                <label for="exampleInputPassword" class="form-label">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input name="password" type="password" class="form-control" id="exampleInputPassword"
                        placeholder="Nhập mật khẩu">
                </div>
                @error('password')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            {{-- <div class="d-flex justify-content-between align-items-center mb-4">
                <a class="text-decoration-none text-small" href="forget-password.html">Quên mật khẩu?</a>
                </div> --}}
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary btn-lg">Đăng nhập</button>
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('sites.home') }}" class="text-decoration-none">← Về trang chủ</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

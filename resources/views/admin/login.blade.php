<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #e3f2fd, #fff);
            font-family: 'Segoe UI', sans-serif;
        }

        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 2rem;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .btn-primary {
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
        }

        .form-label {
            font-weight: 500;
        }

        .text-small {
            font-size: 0.875rem;
        }
    </style>
</head>

<body>
    <div class="d-flex justify-content-center align-items-center min-vh-100">
        <div class="login-container">
            <h3 class="text-center mb-4 text-primary">Đăng nhập hệ thống</h3>
            <form method="POST" action="{{ route('admin.post_login') }}">
                @csrf
                <div class="mb-3">
                    <label for="exampleInputLogin" class="form-label">Email hoặc Tên đăng nhập</label>
                    <input name="login" type="text" class="form-control" id="exampleInputLogin" placeholder="Nhập email hoặc username">
                    @error('login')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword" class="form-label">Mật khẩu</label>
                    <input name="password" type="password" class="form-control" id="exampleInputPassword" placeholder="Nhập mật khẩu">
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a class="text-decoration-none text-primary text-small" href="forget-password.html">Quên mật khẩu?</a>
                </div>
                <div class="d-grid mb-2">
                    <button type="submit" class="btn btn-primary btn-lg">Đăng nhập</button>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('sites.home') }}" class="text-decoration-none">← Về trang chủ</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>

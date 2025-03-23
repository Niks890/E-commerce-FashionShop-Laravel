<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký thành công</title>
    <link href="https://cdn.tutorialjinni.com/bootstrap/5.2.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tutorialjinni.com/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('client/css/register-success.css') }}">
</head>
<body>
    <div class="card">
        <i class="fa-solid fa-circle-check success-icon"></i>
        <h2>🎉 Đăng ký thành công!</h2>
        <p class="user-info"><strong>Họ tên:</strong> {{ $customer->name }}</p>
        <p class="user-info"><strong>Email:</strong> {{ $customer->email }}</p>
        <p>Cảm ơn bạn đã đăng ký tài khoản. Hãy đăng nhập để bắt đầu trải nghiệm website của chúng tôi!</p>
        <a href="{{ route('user.login') }}" class="btn btn-primary mt-3" onclick="clearFormState()">🔑 Quay về trang đăng nhập</a>
    </div>

    <script>
        function clearFormState() {
            localStorage.removeItem("activeForm");
        }
    </script>
</body>
</html>

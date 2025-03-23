<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gửi liên hệ thành công</title>
    <link href="https://cdn.tutorialjinni.com/bootstrap/5.2.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tutorialjinni.com/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('client/css/register-success.css') }}">
</head>
<body>
    <div class="card">
        <i class="fa-solid fa-circle-check success-icon"></i>
        <h2>🎉 Gửi liên hệ thành công!</h2>
        <p>Cảm ơn bạn đã liên hệ với chúng tôi</p>
        <a href="{{ route('sites.home') }}" class="btn btn-primary mt-3" onclick="clearFormState()">🔑 Quay về trang chủ</a>
    </div>

    <script>
        function clearFormState() {
            localStorage.removeItem("activeForm");
        }
    </script>
</body>
</html>

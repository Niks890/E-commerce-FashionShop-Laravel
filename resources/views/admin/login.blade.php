<!DOCTYPE html>
<html>

<head>
    <title>Đăng nhập</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.tutorialjinni.com/bootstrap/5.2.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tutorialjinni.com/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
</head>

<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="login-container shadow-sm border">
            <h2 class="text-center mb-4 text-primary">Đăng nhập</h2>
            <form method="POST" action="{{ route('admin.post_login') }}">
                @csrf
                <div class="mb-3">
                    <label for="exampleInputLogin" class="form-label">Email hoặc Username:</label>
                    <input name="login" type="text" class="form-control" id="exampleInputLogin" aria-describedby="emailHelp">
                    @error('login')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword" class="form-label">Mật khẩu</label>
                    <input name="password" type="password" class="form-control" id="exampleInputPassword">
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <p class="small text-center"><a class="text-primary" href="forget-password.html">Quên mật khẩu?</a></p>
                <div class="d-grid">
                    <input class="btn btn-primary" type="submit" value="Đăng nhập">
                </div>
                <div class="d-flex justify-content-center mt-3">
                    <a href="{{route('sites.home')}}" class="text-center">Về trang chủ</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>

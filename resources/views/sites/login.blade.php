<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.tutorialjinni.com/bootstrap/5.2.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tutorialjinni.com/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" href="{{ asset('assets/img/TSTShop/TST_Shop.ico') }}" type="image/x-icon" />
    <link rel="stylesheet" href="style.css">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="{{ asset('client/css/login.css') }}">
</head>

<body>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form action="{{ route('user.post_register') }}" method="POST">
                @csrf
                <h1>Tạo tài khoản mới</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>hoặc sử dụng email của bạn cho việc đăng ký</span>
                <input type="text" placeholder="Họ và tên" name="name">
                @error('name')
                    <small class="text-danger validate-error">{{ $message }}</small>
                @enderror
                <input type="email" placeholder="Email" name="email" required>
                @error('email')
                    <small class="text-danger validate-error">{{ $message }}</small>
                @enderror
                <input type="password" placeholder="Password" name="password">
                @error('password')
                    <small class="text-danger validate-error">{{ $message }}</small>
                @enderror
                <input type="password" placeholder="Xác nhận Password" name="re_password">
                @error('re_password')
                    <small class="text-danger validate-error">{{ $message }}</small>
                @enderror
                <button type="submit">Đăng Ký</button>
            </form>
        </div>
        <div class="form-container sign-in">
            <form action="{{ route('user.post_login') }}" method="POST">
                @csrf
                <h1>Đăng Nhập</h1>
                <div class="social-icons">
                    <a href="{{ route('auth.google') }}" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>hoặc sử dụng tài khoản của bạn cho việc đăng nhập</span>
                <input type="text" placeholder="Email hoặc Username" name="login" required>
                @error('login')
                    <small class="text-danger validate-error">{{ $message }}</small>
                @enderror
                <input type="password" placeholder="Password" name="password_login" required>
                @error('password_login')
                    <small class="text-danger validate-error">{{ $message }}</small>
                @enderror
                <a href="#">Quên mật khẩu?</a>
                <button type="submit">Đăng Nhập</button>
                <a class="text-decoration-underline" href="{{ route('sites.home') }}">Về trang chủ</a>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Đăng Ký</h1>
                    <p>Sử dụng tài khoản của bạn để trải nghiệm các dịch vụ trên website chúng tôi.</p>
                    <button class="hidden" id="login">Đăng Nhập</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Xin chào!</h1>
                    <p>Bạn chưa có tài khoản?</p>
                    <button class="hidden" id="register">Đăng Ký</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const container = document.getElementById('container');
            const registerBtn = document.getElementById('register');
            const loginBtn = document.getElementById('login');
            registerBtn.addEventListener('click', () => {
                container.classList.add("active");
                localStorage.setItem("activeForm", "register");
            });
            loginBtn.addEventListener('click', () => {
                container.classList.remove("active");
                localStorage.setItem("activeForm", "login");
            });
            //Kiểm tra nếu quay lại từ trang đăng ký thành công, reset về đăng nhập
            if (!document.referrer.includes("register")) {
                localStorage.removeItem("activeForm");
            }
            const hasErrors = {{ session('register_form') || ($errors->any() && old('name')) ? 'true' : 'false' }};
            if (hasErrors) {
                container.classList.add("active", "no-transition");
                localStorage.setItem("activeForm", "register");
            } else {
                if (localStorage.getItem("activeForm") === "register") {
                    container.classList.add("active");
                } else {
                    container.classList.remove("active");
                }
            }
            // Xóa class "no-transition" sau khi trang đã load để không ảnh hưởng đến các lần chuyển đổi tiếp theo
            setTimeout(() => {
                container.classList.remove("no-transition");
            }, 100);
        });
    </script>



</body>

</html>

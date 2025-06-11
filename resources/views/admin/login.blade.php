<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
     <link rel="icon" href="{{ asset('assets/img/TSTShop/LogoTSTFashionShop.webp') }}" type="image/x-icon" />
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

        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
            animation: shake 0.5s ease-in-out;
        }

        .form-control.is-valid {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
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

        .btn-primary:disabled {
            background-color: #6c757d;
            border-color: #6c757d;
            transform: none;
            cursor: not-allowed;
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
            opacity: 0;
            animation: fadeInError 0.3s ease-in-out forwards;
        }

        @keyframes fadeInError {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-color: #ced4da;
            transition: all 0.3s ease;
        }

        .form-control:focus + .input-group-text,
        .input-group:focus-within .input-group-text {
            border-color: #457b9d;
            background-color: #e3f2fd;
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
        <form method="POST" action="{{ route('admin.post_login') }}" id="loginForm">
            @csrf
            <div class="mb-4">
                <label for="exampleInputLogin" class="form-label">Email hoặc Tên đăng nhập</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input name="login" type="text" class="form-control" id="exampleInputLogin"
                        placeholder="Nhập email hoặc username" value="{{ old('login') }}">
                </div>
                <small class="text-danger" id="loginError"></small>
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
                    <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </span>
                </div>
                <small class="text-danger" id="passwordError"></small>
                @error('password')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                    <span class="loading-spinner" id="loadingSpinner"></span>
                    <span id="btnText">Đăng nhập</span>
                </button>
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('sites.home') }}" class="text-decoration-none">← Về trang chủ</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const loginInput = document.getElementById('exampleInputLogin');
            const passwordInput = document.getElementById('exampleInputPassword');
            const loginError = document.getElementById('loginError');
            const passwordError = document.getElementById('passwordError');
            const submitBtn = document.getElementById('submitBtn');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const btnText = document.getElementById('btnText');
            const togglePassword = document.getElementById('togglePassword');
            const eyeIcon = document.getElementById('eyeIcon');

            // Toggle hiển thị mật khẩu
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                if (type === 'text') {
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                } else {
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                }
            });

            // Validate email
            function validateEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            // Validate username (chỉ chứa chữ cái, số, dấu gạch dưới, dấu chấm)
            function validateUsername(username) {
                const usernameRegex = /^[a-zA-Z0-9._]{3,20}$/;
                return usernameRegex.test(username);
            }

            // Validate login field (email hoặc username)
            function validateLogin(value) {
                if (!value || value.trim() === '') {
                    return 'Vui lòng nhập email hoặc tên đăng nhập';
                }

                if (value.length < 3) {
                    return 'Email hoặc tên đăng nhập phải có ít nhất 3 ký tự';
                }

                if (value.length > 50) {
                    return 'Email hoặc tên đăng nhập không được quá 50 ký tự';
                }

                // Kiểm tra xem có phải email không
                if (value.includes('@')) {
                    if (!validateEmail(value)) {
                        return 'Định dạng email không hợp lệ';
                    }
                } else {
                    // Kiểm tra username
                    if (!validateUsername(value)) {
                        return 'Tên đăng nhập chỉ được chứa chữ cái, số, dấu chấm và dấu gạch dưới (3-20 ký tự)';
                    }
                }

                return '';
            }

            // Validate password
            function validatePassword(value) {
                if (!value || value.trim() === '') {
                    return 'Vui lòng nhập mật khẩu';
                }

                if (value.length < 6) {
                    return 'Mật khẩu phải có ít nhất 6 ký tự';
                }

                if (value.length > 100) {
                    return 'Mật khẩu không được quá 100 ký tự';
                }

                return '';
            }

            // Hiển thị lỗi
            function showError(element, message) {
                element.textContent = message;
                element.style.display = message ? 'block' : 'none';
            }

            // Set trạng thái input
            function setInputState(input, isValid) {
                input.classList.remove('is-valid', 'is-invalid');
                if (isValid === true) {
                    input.classList.add('is-valid');
                } else if (isValid === false) {
                    input.classList.add('is-invalid');
                }
            }

            // Validate realtime cho login
            loginInput.addEventListener('input', function() {
                const error = validateLogin(this.value);
                showError(loginError, error);
                setInputState(this, error === '' ? true : false);
            });

            // Validate realtime cho password
            passwordInput.addEventListener('input', function() {
                const error = validatePassword(this.value);
                showError(passwordError, error);
                setInputState(this, error === '' ? true : false);
            });

            // Validate khi blur (rời khỏi input)
            loginInput.addEventListener('blur', function() {
                const error = validateLogin(this.value);
                showError(loginError, error);
                setInputState(this, error === '' ? true : false);
            });

            passwordInput.addEventListener('blur', function() {
                const error = validatePassword(this.value);
                showError(passwordError, error);
                setInputState(this, error === '' ? true : false);
            });

            // Xử lý submit form
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const loginValue = loginInput.value;
                const passwordValue = passwordInput.value;

                const loginErrorMsg = validateLogin(loginValue);
                const passwordErrorMsg = validatePassword(passwordValue);

                // Hiển thị lỗi
                showError(loginError, loginErrorMsg);
                showError(passwordError, passwordErrorMsg);

                // Set trạng thái input
                setInputState(loginInput, loginErrorMsg === '');
                setInputState(passwordInput, passwordErrorMsg === '');

                // Nếu có lỗi thì không submit
                if (loginErrorMsg || passwordErrorMsg) {
                    // Focus vào input đầu tiên có lỗi
                    if (loginErrorMsg) {
                        loginInput.focus();
                    } else if (passwordErrorMsg) {
                        passwordInput.focus();
                    }
                    return;
                }

                // Hiển thị loading
                submitBtn.disabled = true;
                loadingSpinner.style.display = 'inline-block';
                btnText.textContent = 'Đang xử lý...';

                // Simulate server delay (remove this in production)
                setTimeout(() => {
                    // Thực tế sẽ submit form
                    form.submit();
                }, 1000);
            });

            // Reset trạng thái khi focus vào input
            loginInput.addEventListener('focus', function() {
                if (this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                    showError(loginError, '');
                }
            });

            passwordInput.addEventListener('focus', function() {
                if (this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                    showError(passwordError, '');
                }
            });
        });
    </script>
</body>

</html>

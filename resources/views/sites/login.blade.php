<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.tutorialjinni.com/bootstrap/5.2.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tutorialjinni.com/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" href="{{ asset('assets/img/TSTShop/LogoTSTFashionShop.webp') }}" type="image/x-icon" />
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="{{ asset('client/css/login.css') }}">
</head>

<body>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form action="{{ route('user.post_register') }}" method="POST" id="registerForm">
                @csrf
                <h1>Tạo tài khoản mới</h1>
                <div class="social-icons">
                    <a href="javascript:void(0);" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="javascript:void(0);" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="javascript:void(0);" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="javascript:void(0);" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>Sử dụng email của bạn cho việc đăng ký</span>
                <input type="text" placeholder="Họ và tên" name="name" id="regName">
                @error('name')
                    <small class="text-danger validate-error">{{ $message }}</small>
                @enderror
                <small class="validate-error" id="regNameError"></small>

                <input type="email" placeholder="Email" name="email" required id="regEmail">
                @error('email')
                    <small class="text-danger validate-error">{{ $message }}</small>
                @enderror
                <small class="validate-error" id="regEmailError"></small>

                <div class="password-toggle">
                    <input type="password" placeholder="Password" name="password" id="regPassword">
                    <i class="toggle-password fas fa-eye" data-target="regPassword"></i>
                </div>
                @error('password')
                    <small class="text-danger validate-error">{{ $message }}</small>
                @enderror
                <small class="validate-error" id="regPasswordError"></small>

                <div class="password-toggle">
                    <input type="password" placeholder="Xác nhận Password" name="re_password" id="regRePassword">
                    <i class="toggle-password fas fa-eye" data-target="regRePassword"></i>
                </div>
                @error('re_password')
                    <small class="text-danger validate-error">{{ $message }}</small>
                @enderror
                <small class="validate-error" id="regRePasswordError"></small>

                <button type="submit">Đăng Ký</button>
            </form>
        </div>
        <div class="form-container sign-in">
            <form action="{{ route('user.post_login') }}" method="POST" id="loginForm">
                @csrf
                <h1>Đăng Nhập</h1>
                <div class="social-icons">
                    <a href="{{ route('auth.google') }}" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="javascript:void(0);" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="javascript:void(0);" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="javascript:void(0);" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>Dùng email đã đăng ký của bạn cho việc đăng nhập</span>
                <input type="text" placeholder="Email hoặc Username" name="login" required id="loginField">
                @error('login')
                    <small class="text-danger validate-error">{{ $message }}</small>
                @enderror
                <small class="validate-error" id="loginFieldError"></small>

                <div class="password-toggle">
                    <input type="password" placeholder="Password" name="password_login" required id="loginPassword">
                    <i class="toggle-password fas fa-eye" data-target="loginPassword"></i>
                </div>
                @error('password_login')
                    <small class="text-danger validate-error">{{ $message }}</small>
                @enderror
                <small class="validate-error" id="loginPasswordError"></small>

                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Quên mật
                    khẩu?</a>
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

    {{-- Modal Forgot Password --}}
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">Đặt lại mật khẩu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="sendOtpForm" action="{{ route('password.send_otp') }}" method="POST">
                        @csrf
                        <div class="otp-instruction">
                            <i class="fas fa-info-circle"></i> Vui lòng nhập <strong>Email</strong> của bạn để nhận mã
                            OTP xác nhận.
                        </div>
                        <div class="mb-3">
                            <label for="emailOrUsername" class="form-label">Email đã đăng ký tài của bạn</label>
                            <input type="text" class="form-control" id="emailOrUsername" name="identifier"
                                required placeholder="Nhập email của bạn">
                            <small class="text-danger" id="otpIdentifierError"></small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-2"></i> Gửi mã OTP
                        </button>
                    </form>

                    <form id="verifyOtpForm" action="{{ route('password.verify_otp') }}" method="POST"
                        style="display: none;">
                        @csrf
                        <input type="hidden" name="identifier_hidden" id="identifierHidden">
                        <div class="otp-instruction">
                            <i class="fas fa-info-circle"></i> Mã OTP đã được gửi đến địa chỉ Email của
                            bạn. Vui lòng kiểm tra và nhập mã ở đây.
                        </div>
                        <div class="mb-3">
                            <label for="otpCode" class="form-label">Mã OTP (6 chữ số)</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="otpCode" name="otp_code" required
                                    placeholder="Nhập mã OTP 6 chữ số" maxlength="6">
                                <button type="button" class="btn btn-outline-primary" id="resendOtpBtn" disabled>
                                    <span id="resendText">Gửi lại mã</span>
                                    <span id="countdown"> (60s)</span>
                                </button>
                            </div>
                            <small class="text-danger" id="otpCodeError"></small>
                        </div>
                        <div class="mb-3 password-toggle-modal">
                            <label for="newPassword" class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password"
                                required placeholder="Nhập mật khẩu mới">
                            <i class="toggle-password-modal fas fa-eye" data-target="newPassword"></i>
                            <small class="text-danger" id="newPasswordError"></small>
                        </div>
                        <div class="mb-3 password-toggle-modal">
                            <label for="confirmNewPassword" class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" id="confirmNewPassword"
                                name="new_password_confirmation" required placeholder="Nhập lại mật khẩu mới">
                            <i class="toggle-password-modal fas fa-eye" data-target="confirmNewPassword"></i>
                            <small class="text-danger" id="confirmNewPasswordError"></small>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check-circle me-2"></i> Xác nhận và Đổi mật khẩu
                        </button>
                        <button type="button" class="btn btn-outline-secondary w-100 mt-2"
                            onclick="showSendOtpForm()">
                            <i class="fas fa-redo me-2"></i> Gửi lại OTP
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
<script>
    // =================== PASSWORD TOGGLE FUNCTIONALITY ===================
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(function(icon) {
            icon.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (input.type === 'password') {
                    input.type = 'text';
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                }
            });
        });

        // Toggle password visibility for modal
        document.querySelectorAll('.toggle-password-modal').forEach(function(icon) {
            icon.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (input.type === 'password') {
                    input.type = 'text';
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                }
            });
        });

        // =================== VALIDATION FUNCTIONS ===================
        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function validatePassword(password) {
            // Ít nhất 6 ký tự, có chữ hoa, chữ thường và số
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{6,}$/;
            return passwordRegex.test(password);
        }

        function validateName(name) {
            return name.trim().length >= 2 && name.trim().length <= 50;
        }

        function validateLoginField(value) {
            return value.trim().length >= 3;
        }

        function validateOTP(otp) {
            return /^\d{6}$/.test(otp);
        }

        // Show/Hide Error Messages
        function showError(fieldId, errorId, message) {
            const field = document.getElementById(fieldId);
            const errorElement = document.getElementById(errorId);

            if (field && errorElement) {
                field.classList.add('error-field');
                field.classList.remove('success-field');
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            }
        }

        function showSuccess(fieldId, errorId) {
            const field = document.getElementById(fieldId);
            const errorElement = document.getElementById(errorId);

            if (field && errorElement) {
                field.classList.add('success-field');
                field.classList.remove('error-field');
                errorElement.textContent = '';
                errorElement.style.display = 'none';
            }
        }

        function clearValidation(fieldId, errorId) {
            const field = document.getElementById(fieldId);
            const errorElement = document.getElementById(errorId);

            if (field && errorElement) {
                field.classList.remove('error-field', 'success-field');
                errorElement.textContent = '';
                errorElement.style.display = 'none';
            }
        }

        // =================== REGISTER FORM VALIDATION ===================
        // Real-time validation cho form đăng ký
        document.getElementById('regName').addEventListener('blur', function() {
            const name = this.value.trim();
            if (name === '') {
                showError('regName', 'regNameError', 'Họ và tên không được để trống');
            } else if (!validateName(name)) {
                showError('regName', 'regNameError', 'Họ và tên phải từ 2-50 ký tự');
            } else {
                showSuccess('regName', 'regNameError');
            }
        });

        document.getElementById('regEmail').addEventListener('blur', function() {
            const email = this.value.trim();
            if (email === '') {
                showError('regEmail', 'regEmailError', 'Email không được để trống');
            } else if (!validateEmail(email)) {
                showError('regEmail', 'regEmailError', 'Email không đúng định dạng');
            } else {
                showSuccess('regEmail', 'regEmailError');
            }
        });

        document.getElementById('regPassword').addEventListener('blur', function() {
            const password = this.value;
            if (password === '') {
                showError('regPassword', 'regPasswordError', 'Mật khẩu không được để trống');
            } else if (password.length < 6) {
                showError('regPassword', 'regPasswordError', 'Mật khẩu phải có ít nhất 6 ký tự');
            } else if (!validatePassword(password)) {
                showError('regPassword', 'regPasswordError',
                    'Mật khẩu phải có chữ hoa, chữ thường và số');
            } else {
                showSuccess('regPassword', 'regPasswordError');
            }
        });

        document.getElementById('regRePassword').addEventListener('blur', function() {
            const rePassword = this.value;
            const password = document.getElementById('regPassword').value;

            if (rePassword === '') {
                showError('regRePassword', 'regRePasswordError',
                    'Xác nhận mật khẩu không được để trống');
            } else if (rePassword !== password) {
                showError('regRePassword', 'regRePasswordError', 'Mật khẩu xác nhận không khớp');
            } else {
                showSuccess('regRePassword', 'regRePasswordError');
            }
        });

        // Submit validation cho form đăng ký
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const name = document.getElementById('regName').value.trim();
            const email = document.getElementById('regEmail').value.trim();
            const password = document.getElementById('regPassword').value;
            const rePassword = document.getElementById('regRePassword').value;

            let isValid = true;

            // Validate Name
            if (name === '') {
                showError('regName', 'regNameError', 'Họ và tên không được để trống');
                isValid = false;
            } else if (!validateName(name)) {
                showError('regName', 'regNameError', 'Họ và tên phải từ 2-50 ký tự');
                isValid = false;
            } else {
                showSuccess('regName', 'regNameError');
            }

            // Validate Email
            if (email === '') {
                showError('regEmail', 'regEmailError', 'Email không được để trống');
                isValid = false;
            } else if (!validateEmail(email)) {
                showError('regEmail', 'regEmailError', 'Email không đúng định dạng');
                isValid = false;
            } else {
                showSuccess('regEmail', 'regEmailError');
            }

            // Validate Password
            if (password === '') {
                showError('regPassword', 'regPasswordError', 'Mật khẩu không được để trống');
                isValid = false;
            } else if (password.length < 6) {
                showError('regPassword', 'regPasswordError', 'Mật khẩu phải có ít nhất 6 ký tự');
                isValid = false;
            } else if (!validatePassword(password)) {
                showError('regPassword', 'regPasswordError',
                    'Mật khẩu phải có chữ hoa, chữ thường và số');
                isValid = false;
            } else {
                showSuccess('regPassword', 'regPasswordError');
            }

            // Validate Re-Password
            if (rePassword === '') {
                showError('regRePassword', 'regRePasswordError',
                    'Xác nhận mật khẩu không được để trống');
                isValid = false;
            } else if (rePassword !== password) {
                showError('regRePassword', 'regRePasswordError', 'Mật khẩu xác nhận không khớp');
                isValid = false;
            } else {
                showSuccess('regRePassword', 'regRePasswordError');
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        // =================== LOGIN FORM VALIDATION ===================
        // Real-time validation cho form đăng nhập
        document.getElementById('loginField').addEventListener('blur', function() {
            const login = this.value.trim();
            if (login === '') {
                showError('loginField', 'loginFieldError', 'Email hoặc Username không được để trống');
            } else if (!validateLoginField(login)) {
                showError('loginField', 'loginFieldError',
                    'Email hoặc Username phải có ít nhất 3 ký tự');
            } else {
                showSuccess('loginField', 'loginFieldError');
            }
        });

        document.getElementById('loginPassword').addEventListener('blur', function() {
            const password = this.value;
            if (password === '') {
                showError('loginPassword', 'loginPasswordError', 'Mật khẩu không được để trống');
            } else if (password.length < 6) {
                showError('loginPassword', 'loginPasswordError', 'Mật khẩu phải có ít nhất 6 ký tự');
            } else {
                showSuccess('loginPassword', 'loginPasswordError');
            }
        });

        // Submit validation cho form đăng nhập
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const login = document.getElementById('loginField').value.trim();
            const password = document.getElementById('loginPassword').value;

            let isValid = true;

            // Validate Login Field
            if (login === '') {
                showError('loginField', 'loginFieldError', 'Email hoặc Username không được để trống');
                isValid = false;
            } else if (!validateLoginField(login)) {
                showError('loginField', 'loginFieldError',
                    'Email hoặc Username phải có ít nhất 3 ký tự');
                isValid = false;
            } else {
                showSuccess('loginField', 'loginFieldError');
            }

            // Validate Password
            if (password === '') {
                showError('loginPassword', 'loginPasswordError', 'Mật khẩu không được để trống');
                isValid = false;
            } else if (password.length < 6) {
                showError('loginPassword', 'loginPasswordError', 'Mật khẩu phải có ít nhất 6 ký tự');
                isValid = false;
            } else {
                showSuccess('loginPassword', 'loginPasswordError');
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        // =================== FORGOT PASSWORD FUNCTIONALITY ===================
        // Biến toàn cục để lưu timer
        let resendTimer;
        let timeLeft = 60;

        function startResendTimer() {
            const resendBtn = document.getElementById('resendOtpBtn');
            const countdown = document.getElementById('countdown');
            const resendText = document.getElementById('resendText');

            // KIỂM TRA XEM CÁC ELEMENT CÓ TỒN TẠI KHÔNG
            if (!resendBtn || !countdown || !resendText) {
                console.error('Không tìm thấy elements cần thiết cho timer');
                return;
            }

            // Vô hiệu hóa nút
            resendBtn.disabled = true;
            countdown.style.display = 'inline';
            resendText.style.display = 'inline';

            // Cập nhật thời gian mỗi giây
            resendTimer = setInterval(function() {
                timeLeft--;

                // KIỂM TRA LẠI ELEMENT TRƯỚC KHI CẬP NHẬT
                const currentCountdown = document.getElementById('countdown');
                if (currentCountdown) {
                    currentCountdown.textContent = ` (${timeLeft}s)`;
                }

                if (timeLeft <= 0) {
                    clearInterval(resendTimer);

                    // KIỂM TRA CÁC ELEMENT TRƯỚC KHI CẬP NHẬT
                    const currentResendBtn = document.getElementById('resendOtpBtn');
                    const currentCountdownEl = document.getElementById('countdown');

                    if (currentResendBtn) {
                        currentResendBtn.disabled = false;
                    }
                    if (currentCountdownEl) {
                        currentCountdownEl.style.display = 'none';
                    }
                }
            }, 1000);
        }

        // Hàm gửi lại OTP - ĐÃ SỬA LỖI
        async function resendOtp() {
            const identifier = document.getElementById('identifierHidden').value;
            const resendBtn = document.getElementById('resendOtpBtn');

            if (!identifier) {
                alert('Không tìm thấy thông tin email/username');
                return;
            }

            // KIỂM TRA ELEMENT TỒN TẠI
            if (!resendBtn) {
                console.error('Không tìm thấy nút gửi lại OTP');
                return;
            }

            try {
                resendBtn.disabled = true;
                resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang gửi...';

                // LẤY CSRF TOKEN
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                    document.querySelector('input[name="_token"]')?.value;

                const response = await fetch('{{ route('password.send_otp') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        identifier: identifier
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    alert('Mã OTP mới đã được gửi! Vui lòng kiểm tra email của bạn.');

                    // Reset timer và bắt đầu đếm ngược mới
                    clearInterval(resendTimer);
                    timeLeft = 60;

                    // Đặt lại nội dung nút trước khi bắt đầu timer
                    resendBtn.innerHTML =
                        '<span id="resendText">Gửi lại mã</span><span id="countdown"> (60s)</span>';

                    // Bắt đầu timer mới
                    startResendTimer();
                } else {
                    alert(data.message || 'Có lỗi xảy ra khi gửi lại mã OTP');
                    // Nếu có lỗi, reset lại nút về trạng thái ban đầu
                    resendBtn.innerHTML =
                        '<span id="resendText">Gửi lại mã</span><span id="countdown"></span>';
                    resendBtn.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.');

                // Nếu có lỗi, reset lại nút về trạng thái ban đầu
                if (resendBtn) {
                    resendBtn.innerHTML =
                        '<span id="resendText">Gửi lại mã</span><span id="countdown"></span>';
                    resendBtn.disabled = false;
                }
            } finally {
                // KHÔNG GHI ĐÈ INNERHTML Ở ĐÂY VÌ SẼ LÀM MẤT TIMER ELEMENTS
                // Timer sẽ tự động reset innerHTML khi cần thiết
            }
        }

        // Hàm reset timer khi cần
        function resetTimer() {
            if (resendTimer) {
                clearInterval(resendTimer);
                resendTimer = null;
            }
            timeLeft = 60;

            const resendBtn = document.getElementById('resendOtpBtn');
            const countdown = document.getElementById('countdown');
            const resendText = document.getElementById('resendText');

            if (resendBtn) {
                resendBtn.disabled = false;
            }
            if (countdown) {
                countdown.style.display = 'none';
            }
            if (resendText) {
                resendText.style.display = 'inline';
            }
        }

        // Cập nhật function showSendOtpForm
        function showSendOtpForm() {
            document.getElementById('sendOtpForm').style.display = 'block';
            document.getElementById('verifyOtpForm').style.display = 'none';
            document.getElementById('otpIdentifierError').innerText = '';
            document.getElementById('otpCodeError').innerText = '';
            document.getElementById('newPasswordError').innerText = '';
            document.getElementById('confirmNewPasswordError').innerText = '';
            document.getElementById('otpCode').value = '';
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmNewPassword').value = '';
            document.getElementById('emailOrUsername').value = '';

            // Reset timer khi quay lại form gửi OTP
            resetTimer();
        }


        function showVerifyOtpForm(identifier) {
            document.getElementById('sendOtpForm').style.display = 'none';
            document.getElementById('verifyOtpForm').style.display = 'block';
            document.getElementById('identifierHidden').value = identifier;

            // Bắt đầu đếm ngược
            clearInterval(resendTimer); // Xóa timer cũ nếu có
            timeLeft = 60;
            startResendTimer();
        }

        // Handle Send OTP Form submission (using Fetch API for AJAX)
        document.getElementById('sendOtpForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            document.getElementById('otpIdentifierError').innerText = ''; // Clear error

            // CLIENT-SIDE VALIDATION CHO SEND OTP FORM
            const identifier = formData.get('identifier').trim();
            let isValid = true;

            if (identifier === '') {
                document.getElementById('otpIdentifierError').innerText =
                    'Email hoặc Tên đăng nhập không được để trống';
                isValid = false;
            } else if (identifier.length < 3) {
                document.getElementById('otpIdentifierError').innerText =
                    'Email hoặc Tên đăng nhập phải có ít nhất 3 ký tự';
                isValid = false;
            }

            if (!isValid) {
                return;
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ?
                            document.querySelector('meta[name="csrf-token"]').content : '',
                        'Accept': 'application/json' // Expect JSON response
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    alert(data.message); // Show success message
                    showVerifyOtpForm(formData.get('identifier'));
                } else {
                    if (data.errors && data.errors.identifier) {
                        document.getElementById('otpIdentifierError').innerText = data.errors
                            .identifier[0];
                    } else if (data.message) {
                        alert(data.message); // Show general error message
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.');
            }
        });

        // Handle Verify OTP and Reset Password Form submission (using Fetch API for AJAX)
        document.getElementById('verifyOtpForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            document.getElementById('otpCodeError').innerText = ''; // Clear errors
            document.getElementById('newPasswordError').innerText = '';
            document.getElementById('confirmNewPasswordError').innerText = '';

            // CLIENT-SIDE VALIDATION CHO VERIFY OTP FORM
            const otpCode = formData.get('otp_code').trim();
            const newPassword = formData.get('new_password');
            const confirmPassword = formData.get('new_password_confirmation');
            let isValid = true;

            // Validate OTP
            if (otpCode === '') {
                document.getElementById('otpCodeError').innerText = 'Mã OTP không được để trống';
                isValid = false;
            } else if (!validateOTP(otpCode)) {
                document.getElementById('otpCodeError').innerText = 'Mã OTP phải có 6 chữ số';
                isValid = false;
            }

            // Validate New Password
            if (newPassword === '') {
                document.getElementById('newPasswordError').innerText =
                    'Mật khẩu mới không được để trống';
                isValid = false;
            } else if (newPassword.length < 6) {
                document.getElementById('newPasswordError').innerText =
                    'Mật khẩu mới phải có ít nhất 6 ký tự';
                isValid = false;
            } else if (!validatePassword(newPassword)) {
                document.getElementById('newPasswordError').innerText =
                    'Mật khẩu mới phải có chữ hoa, chữ thường và số';
                isValid = false;
            }

            // Validate Confirm Password
            if (confirmPassword === '') {
                document.getElementById('confirmNewPasswordError').innerText =
                    'Xác nhận mật khẩu không được để trống';
                isValid = false;
            } else if (confirmPassword !== newPassword) {
                document.getElementById('confirmNewPasswordError').innerText =
                    'Mật khẩu xác nhận không khớp';
                isValid = false;
            }

            if (!isValid) {
                return;
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ?
                            document.querySelector('meta[name="csrf-token"]').content : '',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    alert(data.message); // Show success message
                    // Close modal and potentially redirect or show login success
                    const forgotPasswordModal = bootstrap.Modal.getInstance(document.getElementById(
                        'forgotPasswordModal'));
                    if (forgotPasswordModal) {
                        forgotPasswordModal.hide();
                    }
                    showSendOtpForm(); // Reset form for next time
                } else {
                    if (data.errors) {
                        if (data.errors.otp_code) {
                            document.getElementById('otpCodeError').innerText = data.errors
                                .otp_code[0];
                        }
                        if (data.errors.new_password) {
                            document.getElementById('newPasswordError').innerText = data.errors
                                .new_password[0];
                        }
                        if (data.errors.new_password_confirmation) {
                            document.getElementById('confirmNewPasswordError').innerText = data
                                .errors.new_password_confirmation[0];
                        }
                    } else if (data.message) {
                        alert(data.message);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xác nhận OTP. Vui lòng thử lại.');
            }
        });

        // Gắn sự kiện click cho nút gửi lại
        document.getElementById('resendOtpBtn').addEventListener('click', resendOtp);

        // Ensure CSRF token is present in meta tag for AJAX requests
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}'; // Or get it from a global JS variable if available
            document.head.appendChild(meta);
        }

        // Đặt các function thành global để có thể gọi từ HTML
        window.showSendOtpForm = showSendOtpForm;
        window.showVerifyOtpForm = showVerifyOtpForm;

        // Reset form khi modal đóng
        document.getElementById('forgotPasswordModal').addEventListener('hidden.bs.modal', function() {
            showSendOtpForm();
        });

        // =================== ORIGINAL CODE ===================
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

        // Kiểm tra trạng thái form khi load trang
        const registerErrors = {{ $errors->any() && old('name') ? 'true' : 'false' }};
        const loginErrors = {{ $errors->any() && old('login') ? 'true' : 'false' }};

        if (registerErrors) {
            container.classList.add("active", "no-transition");
        } else if (loginErrors) {
            container.classList.remove("active", "no-transition");
        } else {
            // Mặc định hiển thị form đăng nhập
            container.classList.remove("active");
        }

        // Xóa class "no-transition" sau khi trang đã load
        setTimeout(() => {
            container.classList.remove("no-transition");
        }, 100);

        // Xử lý khi submit form đăng nhập thất bại
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const login = document.getElementById('loginField').value.trim();
            const password = document.getElementById('loginPassword').value;

            let isValid = true;

            // Validate Login Field
            if (login === '') {
                showError('loginField', 'loginFieldError', 'Email hoặc Username không được để trống');
                isValid = false;
            } else if (!validateLoginField(login)) {
                showError('loginField', 'loginFieldError',
                    'Email hoặc Username phải có ít nhất 3 ký tự');
                isValid = false;
            } else {
                showSuccess('loginField', 'loginFieldError');
            }

            // Validate Password
            if (password === '') {
                showError('loginPassword', 'loginPasswordError', 'Mật khẩu không được để trống');
                isValid = false;
            } else if (password.length < 6) {
                showError('loginPassword', 'loginPasswordError', 'Mật khẩu phải có ít nhất 6 ký tự');
                isValid = false;
            } else {
                showSuccess('loginPassword', 'loginPasswordError');
            }

            if (!isValid) {
                e.preventDefault();
                // Đảm bảo hiển thị form đăng nhập khi có lỗi
                container.classList.remove("active");
                localStorage.setItem("activeForm", "login");
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('forgotPasswordModal');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function() {
                resetTimer();
                showSendOtpForm();
            });
        }
    });
</script>

</html>

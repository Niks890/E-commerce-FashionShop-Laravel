<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.tutorialjinni.com/bootstrap/5.2.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tutorialjinni.com/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" href="{{ asset('assets/img/TSTShop/LogoTSTFashionShop.webp') }}" type="image/x-icon" />
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
                    <a href="javascript:void(0);" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="javascript:void(0);" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="javascript:void(0);" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="javascript:void(0);" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
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
                    <a href="javascript:void(0);" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="javascript:void(0);" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="javascript:void(0);" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
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



            // function showSendOtpForm() {
            //     document.getElementById('sendOtpForm').style.display = 'block';
            //     document.getElementById('verifyOtpForm').style.display = 'none';
            //     document.getElementById('otpIdentifierError').innerText = ''; // Clear previous errors
            //     document.getElementById('otpCodeError').innerText = '';
            //     document.getElementById('newPasswordError').innerText = '';
            //     document.getElementById('confirmNewPasswordError').innerText = '';
            //     document.getElementById('otpCode').value = ''; // Clear OTP field
            //     document.getElementById('newPassword').value = ''; // Clear password field
            //     document.getElementById('confirmNewPassword').value = ''; // Clear confirm password field
            // }

            // function showVerifyOtpForm(identifier) {
            //     document.getElementById('sendOtpForm').style.display = 'none';
            //     document.getElementById('verifyOtpForm').style.display = 'block';
            //     document.getElementById('identifierHidden').value = identifier;
            // }

            // // Handle Send OTP Form submission (using Fetch API for AJAX)
            // document.getElementById('sendOtpForm').addEventListener('submit', async function(event) {
            //     event.preventDefault();
            //     const form = event.target;
            //     const formData = new FormData(form);
            //     document.getElementById('otpIdentifierError').innerText = ''; // Clear error

            //     try {
            //         const response = await fetch(form.action, {
            //             method: 'POST',
            //             headers: {
            //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ?
            //                     document.querySelector('meta[name="csrf-token"]').content : '',
            //                 'Accept': 'application/json' // Expect JSON response
            //             },
            //             body: formData
            //         });

            //         const data = await response.json();

            //         if (response.ok) {
            //             alert(data.message); // Show success message
            //             showVerifyOtpForm(formData.get('identifier'));
            //         } else {
            //             if (data.errors && data.errors.identifier) {
            //                 document.getElementById('otpIdentifierError').innerText = data.errors
            //                     .identifier[0];
            //             } else if (data.message) {
            //                 alert(data.message); // Show general error message
            //             }
            //         }
            //     } catch (error) {
            //         console.error('Error:', error);
            //         alert('Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.');
            //     }
            // });

            // // Handle Verify OTP and Reset Password Form submission (using Fetch API for AJAX)
            // document.getElementById('verifyOtpForm').addEventListener('submit', async function(event) {
            //     event.preventDefault();
            //     const form = event.target;
            //     const formData = new FormData(form);
            //     document.getElementById('otpCodeError').innerText = ''; // Clear errors
            //     document.getElementById('newPasswordError').innerText = '';
            //     document.getElementById('confirmNewPasswordError').innerText = '';

            //     try {
            //         const response = await fetch(form.action, {
            //             method: 'POST',
            //             headers: {
            //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ?
            //                     document.querySelector('meta[name="csrf-token"]').content : '',
            //                 'Accept': 'application/json'
            //             },
            //             body: formData
            //         });

            //         const data = await response.json();

            //         if (response.ok) {
            //             alert(data.message); // Show success message
            //             // Close modal and potentially redirect or show login success
            //             const forgotPasswordModal = bootstrap.Modal.getInstance(document.getElementById(
            //                 'forgotPasswordModal'));
            //             if (forgotPasswordModal) {
            //                 forgotPasswordModal.hide();
            //             }
            //             showSendOtpForm(); // Reset form for next time
            //         } else {
            //             if (data.errors) {
            //                 if (data.errors.otp_code) {
            //                     document.getElementById('otpCodeError').innerText = data.errors
            //                         .otp_code[0];
            //                 }
            //                 if (data.errors.new_password) {
            //                     document.getElementById('newPasswordError').innerText = data.errors
            //                         .new_password[0];
            //                 }
            //                 if (data.errors.new_password_confirmation) {
            //                     document.getElementById('confirmNewPasswordError').innerText = data
            //                         .errors.new_password_confirmation[0];
            //                 }
            //             } else if (data.message) {
            //                 alert(data.message);
            //             }
            //         }
            //     } catch (error) {
            //         console.error('Error:', error);
            //         alert('Có lỗi xảy ra khi xác nhận OTP. Vui lòng thử lại.');
            //     }
            // });

            // // Ensure CSRF token is present in meta tag for AJAX requests
            // if (!document.querySelector('meta[name="csrf-token"]')) {
            //     const meta = document.createElement('meta');
            //     meta.name = 'csrf-token';
            //     meta.content = '{{ csrf_token() }}'; // Or get it from a global JS variable if available
            //     document.head.appendChild(meta);
            // }
        });
    </script>


    {{-- <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">Đặt lại mật khẩu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="sendOtpForm" action="{{ route('password.send_otp') }}" method="POST">
                        @csrf
                        <p>Vui lòng nhập **Email** hoặc **Tên đăng nhập** của bạn để nhận mã OTP xác nhận.</p>
                        <div class="mb-3">
                            <label for="emailOrUsername" class="form-label">Email hoặc Tên đăng nhập</label>
                            <input type="text" class="form-control" id="emailOrUsername" name="identifier"
                                required>
                            <small class="text-danger" id="otpIdentifierError"></small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Gửi mã OTP</button>
                    </form>

                    <form id="verifyOtpForm" action="{{ route('password.verify_otp') }}" method="POST"
                        style="display: none;">
                        @csrf
                        <input type="hidden" name="identifier_hidden" id="identifierHidden">
                        <p class="mt-3">Mã OTP đã được gửi đến địa chỉ Email/Tên đăng nhập của bạn. Vui lòng kiểm tra
                            và nhập mã ở đây.</p>
                        <div class="mb-3">
                            <label for="otpCode" class="form-label">Mã OTP</label>
                            <input type="text" class="form-control" id="otpCode" name="otp_code" required>
                            <small class="text-danger" id="otpCodeError"></small>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password"
                                required>
                            <small class="text-danger" id="newPasswordError"></small>
                        </div>
                        <div class="mb-3">
                            <label for="confirmNewPassword" class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" id="confirmNewPassword"
                                name="new_password_confirmation" required>
                            <small class="text-danger" id="confirmNewPasswordError"></small>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Xác nhận và Đổi mật khẩu</button>
                        <button type="button" class="btn btn-secondary w-100 mt-2" onclick="showSendOtpForm()">Gửi
                            lại OTP</button>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}
</body>

</html>

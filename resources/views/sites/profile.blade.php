@extends('sites.master')
@section('title', 'Hồ sơ cá nhân')



<section class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__text">
                    <h4>Hồ sơ cá nhân</h4>
                    <div class="breadcrumb__links">
                        <a href="{{ route('sites.home') }}">Home</a>
                        <a href="{{ route('user.profile') }}">Hồ sơ cá nhân</a>
                        <span>Thông tin cá nhân của bạn</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@section('content')
    @if (Session::has('updateprofile'))
        <div class="alert alert-success alert-dismissible fade show mt-4 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ Session::get('updateprofile') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container my-5">
        <div class="profile-container">
            <div class="row g-0">
                <!-- Sidebar -->
                <div class="col-lg-3">
                    <div class="profile-sidebar text-center">
                        @php
                            $avatarUrl = asset('client/img/avatar-user.png');
                            if (Auth::guard('customer')->check() && Auth::guard('customer')->user() !== null) {
                                $user = Auth::guard('customer')->user();
                                $avatar = $user->image;
                                if (!empty($avatar)) {
                                    if (filter_var($avatar, FILTER_VALIDATE_URL)) {
                                        $avatarUrl = $avatar;
                                    } elseif (file_exists(public_path('client/img/' . $avatar))) {
                                        $avatarUrl = asset('client/img/' . $avatar);
                                    }
                                }
                            }
                        @endphp

                        <img class="profile-avatar mb-3" src="{{ $avatarUrl }}" alt="Avatar">
                        <h4>{{ Auth::guard('customer')->user()->name }}</h4>
                        <p class="text-white-50 mb-4">{{ Auth::guard('customer')->user()->email }}</p>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-lg-9">
                    <div class="profile-content">
                        <div class="profile-header">
                            <h3 class="fw-bold"><i class="fas fa-user-edit me-2 text-primary"></i> Thông tin cá nhân</h3>
                            <p class="text-muted">Quản lý thông tin cá nhân của bạn</p>
                        </div>

                        <form action="{{ route('user.update_profile', Auth::guard('customer')->user()->id) }}"
                            method="post">
                            @csrf @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Họ và tên</label>
                                    <input type="text" class="form-control" name="name" value="{{ $customer->name }}">
                                    @error('name')
                                        <small class="text-danger validate-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="text" class="form-control" name="email" value="{{ $customer->email }}"
                                        readonly>
                                    @error('email')
                                        <small class="text-danger validate-error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="text" class="form-control" name="phone"
                                        value="{{ $customer->phone }}">
                                    @error('phone')
                                        <small class="text-danger validate-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Địa chỉ</label>
                                    <input type="text" class="form-control" name="address"
                                        value="{{ $customer->address }}">
                                    @error('address')
                                        <small class="text-danger validate-error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row password-section">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mật khẩu</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" value="********" readonly>
                                        <button class="btn btn-outline-secondary password-toggle" type="button"
                                            id="btn-edit-profile">
                                            <i class="fas fa-edit"></i> Thay đổi
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-save text-white">
                                    <i class="fas fa-save me-2"></i> Lưu thông tin
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
    <style>
        .profile-container {
            background-color: #f8f9fa;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-sidebar {
            background: linear-gradient(135deg, #656877 0%, #fbfbfb 100%);
            color: white;
            padding: 30px 20px;
            height: 100%;
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .profile-content {
            padding: 30px;
            background-color: white;
        }

        .profile-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            color: #555;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #b8b8b8;
            box-shadow: 0 0 0 0.25rem rgba(118, 75, 162, 0.25);
        }

        .btn-save {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 10px 30px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(118, 75, 162, 0.3);
        }

        .password-toggle {
            cursor: pointer;
            transition: all 0.3s;
        }

        .password-toggle:hover {
            color: #764ba2;
        }
    </style>
@endsection

@section('js')
    @if (Session::has('updateprofile'))
        <script src="{{ asset('assets/js/message.js') }}"></script>
    @endif

    <script>
        document.getElementById("btn-edit-profile").addEventListener("click", function(e) {
            if (confirm("Bạn muốn thay đổi mật khẩu?")) {
                let container = document.querySelector('.password-section');
                let inputNewPassword = document.createElement('div');
                inputNewPassword.classList.add('col-md-6', 'mb-3');
                inputNewPassword.innerHTML = `
                    <label class="form-label">Mật khẩu mới</label>
                    <input type="password" class="form-control" name="new_password" placeholder="Nhập mật khẩu mới">
                    <small class="text-muted">Để trống nếu không muốn thay đổi</small>`;
                container.appendChild(inputNewPassword);

                // Thêm trường xác nhận mật khẩu
                let inputConfirmPassword = document.createElement('div');
                inputConfirmPassword.classList.add('col-md-6', 'mb-3');
                inputConfirmPassword.innerHTML =
                    `
                    <label class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" class="form-control" name="new_password_confirmation" placeholder="Nhập lại mật khẩu mới">`;
                container.appendChild(inputConfirmPassword);

                // Ẩn nút thay đổi sau khi click
                this.style.display = 'none';
            }
        });
    </script>
@endsection

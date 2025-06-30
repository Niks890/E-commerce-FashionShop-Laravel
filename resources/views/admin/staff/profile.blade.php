@extends('admin.master')
@section('title', 'Hồ sơ cá nhân')
@section('content')
    @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 1050; max-width: 350px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle text-success me-2"></i>
                <span>{{ Session::get('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="container-fluid px-4 py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <!-- Header Section -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-gradient-primary text-white border-0 py-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-cog me-2"></i>
                            <h4 class="mb-0 fw-bold">Hồ Sơ Cá Nhân</h4>
                        </div>
                    </div>
                </div>

                <!-- Profile Content -->
                <div class="card shadow-lg border-0 overflow-hidden">
                    <div class="row g-0">
                        <!-- Profile Sidebar -->
                        <div class="col-md-4 col-lg-3">
                            <div class="bg-light h-100 p-4 text-center">
                                <div class="profile-avatar-container mb-4">
                                    <div class="position-relative d-inline-block">
                                        <img class="rounded-circle shadow-sm border border-3 border-white"
                                             width="120" height="120"
                                             style="object-fit: cover;"
                                             src="{{ $staff->avatar ?? asset('images/avatar.png') }}"
                                             alt="Avatar">
                                        <div class="position-absolute bottom-0 end-0">
                                            <span class="badge bg-success rounded-pill p-2">
                                                <i class="fas fa-check"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="profile-info">
                                    <h5 class="fw-bold text-dark mb-1">{{ $staff->name }}</h5>
                                    <p class="text-muted mb-2">{{ $staff->position }}</p>
                                    <p class="text-muted small mb-3">
                                        <i class="fas fa-envelope me-1"></i>
                                        {{ $staff->email }}
                                    </p>

                                    <div class="d-flex justify-content-center gap-2 mb-3">
                                        <span class="badge bg-primary px-3 py-2">
                                            <i class="fas fa-briefcase me-1"></i>
                                            Nhân viên
                                        </span>
                                    </div>

                                    <div class="profile-stats">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="stat-item">
                                                    <h6 class="fw-bold text-primary mb-0">{{ $staff->status }}</h6>
                                                    <small class="text-muted">Trạng thái</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="stat-item">
                                                    <h6 class="fw-bold text-success mb-0">Hoạt động</h6>
                                                    <small class="text-muted">Tình trạng</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Form -->
                        <div class="col-md-8 col-lg-9">
                            <div class="p-4">
                                <form action="{{ route('staff.update_staff', $staff->id) }}" method="post" class="needs-validation" novalidate>
                                    @csrf @method('PUT')

                                    <div class="mb-4">
                                        <h5 class="fw-bold text-dark mb-3">
                                            <i class="fas fa-edit me-2 text-primary"></i>
                                            Thông Tin Cá Nhân
                                        </h5>
                                        <hr class="border-2 border-primary opacity-25">
                                    </div>

                                    <div class="row g-3">
                                        <!-- Name & Email Row -->
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control border-2" id="name" name="name"
                                                       value="{{ old('name', $staff->name) }}" placeholder="Họ và tên" required>
                                                <label for="name">
                                                    <i class="fas fa-user me-1"></i>Họ và tên
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="email" class="form-control border-2" id="email" name="email"
                                                       value="{{ old('email', $staff->email) }}" placeholder="Email" required>
                                                <label for="email">
                                                    <i class="fas fa-envelope me-1"></i>Email
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Phone & Address Row -->
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="tel" class="form-control border-2" id="phone" name="phone"
                                                       value="{{ old('phone', $staff->phone) }}" placeholder="Số điện thoại">
                                                <label for="phone">
                                                    <i class="fas fa-phone me-1"></i>Số điện thoại
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select border-2" id="sex" name="sex" required>
                                                    <option value="1" {{ $staff->sex == 1 ? 'selected' : '' }}>Nam</option>
                                                    <option value="0" {{ $staff->sex == 0 ? 'selected' : '' }}>Nữ</option>
                                                </select>
                                                <label for="sex">
                                                    <i class="fas fa-venus-mars me-1"></i>Giới tính
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Address Full Width -->
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <input type="text" class="form-control border-2" id="address" name="address"
                                                       value="{{ old('address', $staff->address) }}" placeholder="Địa chỉ">
                                                <label for="address">
                                                    <i class="fas fa-map-marker-alt me-1"></i>Địa chỉ
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Position & Status Row -->
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control border-2 bg-light" id="position" name="position"
                                                       value="{{ old('position', $staff->position) }}" placeholder="Chức vụ" readonly>
                                                <label for="position">
                                                    <i class="fas fa-briefcase me-1"></i>Chức vụ
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control border-2" id="status" name="status"
                                                       value="{{ old('status', $staff->status) }}" placeholder="Trạng thái">
                                                <label for="status">
                                                    <i class="fas fa-info-circle me-1"></i>Trạng thái
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="row mt-5">
                                        <div class="col-12">
                                            <div class="d-flex gap-3 justify-content-center">
                                                <button type="submit" class="btn btn-primary btn-lg px-5 py-3 shadow-sm">
                                                    <i class="fas fa-save me-2"></i>
                                                    Lưu Thay Đổi
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary btn-lg px-5 py-3" onclick="window.history.back()">
                                                    <i class="fas fa-arrow-left me-2"></i>
                                                    Quay Lại
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card {
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .form-floating > .form-control,
        .form-floating > .form-select {
            border-radius: 10px;
            border-width: 2px;
            transition: all 0.3s ease;
        }

        .form-floating > .form-control:focus,
        .form-floating > .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn {
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .profile-avatar-container img {
            transition: all 0.3s ease;
        }

        .profile-avatar-container:hover img {
            transform: scale(1.05);
        }

        .stat-item {
            padding: 10px;
            border-radius: 8px;
            background: rgba(102, 126, 234, 0.1);
            margin-bottom: 10px;
        }

        .badge {
            font-size: 0.85em;
            font-weight: 500;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .form-floating > label {
            font-weight: 500;
            color: #6c757d;
        }

        .text-primary {
            color: #667eea !important;
        }

        .border-primary {
            border-color: #667eea !important;
        }

        .bg-light {
            background-color: #f8f9fc !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container-fluid {
                padding: 15px;
            }

            .card {
                margin-bottom: 20px;
            }

            .btn-lg {
                padding: 12px 30px;
                font-size: 1rem;
            }
        }

        /* Animation for form validation */
        .was-validated .form-control:invalid,
        .was-validated .form-select:invalid {
            border-color: #dc3545;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>
@endsection

@section('js')
    @if (Session::has('success'))
        <script src="{{ asset('assets/js/message.js') }}"></script>
    @endif

    <script>
        // Auto-dismiss success alert after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.querySelector('.alert-dismissible');
            if (alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            }
        });

        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                const forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
@endsection

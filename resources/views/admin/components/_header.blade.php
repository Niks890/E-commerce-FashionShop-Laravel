@php
    $userId = auth()->user()->id - 1;
    $staff = DB::table('staff')->where('id', $userId)->select('avatar')->first();
@endphp

<style>
    /* Modern Header Styles */
    .main-header {
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
    }

    .logo-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        position: relative;
        overflow: hidden;
    }

    .logo-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
    }

    .logo-header .logo {
        position: relative;
        z-index: 2;
    }

    .navbar-brand {
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        transition: transform 0.3s ease;
    }

    .navbar-brand:hover {
        transform: scale(1.05);
    }

    /* Modern Buttons */
    .btn-toggle {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        border-radius: 12px;
        color: white;
        padding: 8px 12px;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .btn-toggle:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .topbar-toggler {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        border-radius: 12px;
        color: white;
        padding: 8px 12px;
        transition: all 0.3s ease;
    }

    /* Modern Navigation */
    .navbar-header {
        backdrop-filter: blur(20px);
        background: rgba(255, 255, 255, 0.95);
    }

    .topbar-nav .nav-item {
        margin: 0 4px;
    }

    .topbar-icon .nav-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #f8f9fc 0%, #e8ecf4 100%);
        color: #5a6c7d;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0, 0, 0, 0.04);
        position: relative;
        overflow: hidden;
    }

    .topbar-icon .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, #667eea, #764ba2);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .topbar-icon .nav-link i {
        position: relative;
        z-index: 2;
        font-size: 18px;
    }

    .topbar-icon .nav-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.25);
        color: white;
    }

    .topbar-icon .nav-link:hover::before {
        opacity: 1;
    }

    /* Notification Badge */
    .notification {
        position: absolute;
        top: -6px;
        right: -6px;
        background: linear-gradient(135deg, #ff6b6b, #ee5a52);
        color: white;
        border-radius: 12px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 600;
        min-width: 20px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
        }

        50% {
            box-shadow: 0 2px 8px rgba(255, 107, 107, 0.6);
        }

        100% {
            box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
        }
    }

    /* Profile Section */
    .topbar-user .profile-pic {
        display: flex;
        align-items: center;
        padding: 8px 16px;
        border-radius: 16px;
        background: linear-gradient(135deg, #f8f9fc 0%, #e8ecf4 100%);
        border: 1px solid rgba(0, 0, 0, 0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
    }

    .topbar-user .profile-pic:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
    }

    .avatar-sm {
        position: relative;
        overflow: hidden;
    }

    .avatar-img {
        width: 36px;
        height: 36px;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .profile-pic:hover .avatar-img {
        transform: scale(1.1);
        border-color: white;
    }

    .profile-username {
        margin-left: 12px;
        line-height: 1.2;
    }

    .profile-username .op-7 {
        font-size: 12px;
        opacity: 0.7;
        font-weight: 400;
    }

    .profile-username .fw-bold {
        font-size: 14px;
        font-weight: 600;
        display: block;
        margin-top: 2px;
    }

    /* Modern Dropdowns */
    .dropdown-menu {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(20px);
        background: rgba(255, 255, 255, 0.95);
        padding: 8px;
        margin-top: 8px;
    }

    .dropdown-item {
        border-radius: 12px;
        padding: 12px 16px;
        margin: 2px 0;
        transition: all 0.2s ease;
        font-weight: 500;
        color: #5a6c7d;
    }

    .dropdown-item:hover {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        transform: translateX(4px);
    }

    /* Messages & Notifications */
    .dropdown-title {
        padding: 16px 20px 12px;
        font-weight: 600;
        color: #2c3e50;
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        margin-bottom: 8px;
    }

    .notif-center a,
    .message-notif-scroll a {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-radius: 12px;
        margin: 4px 8px;
        transition: all 0.2s ease;
        text-decoration: none;
        color: inherit;
    }

    .notif-center a:hover,
    .message-notif-scroll a:hover {
        background: linear-gradient(135deg, #f8f9fc, #e8ecf4);
        transform: translateX(4px);
    }

    .notif-img img {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid rgba(102, 126, 234, 0.1);
    }

    .notif-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
    }

    .notif-primary {
        background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .notif-success {
        background: linear-gradient(135deg, #56ab2f, #a8e6cf);
    }

    .notif-danger {
        background: linear-gradient(135deg, #ff6b6b, #ee5a52);
    }

    .notif-content {
        flex: 1;
        margin-left: 12px;
    }

    .notif-content .block {
        font-weight: 500;
        color: #2c3e50;
        display: block;
        margin-bottom: 2px;
    }

    .notif-content .time,
    .notif-content .subject {
        font-size: 12px;
        color: #7f8c8d;
    }

    .see-all {
        display: block;
        text-align: center;
        padding: 12px;
        margin: 8px;
        border-radius: 12px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        text-decoration: none;
        font-weight: 500;
        transition: transform 0.2s ease;
    }

    .see-all:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    /* Quick Actions */
    .quick-actions-header {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
    }

    .quick-actions-item {
        text-align: center;
        padding: 16px 8px;
        border-radius: 12px;
        transition: all 0.2s ease;
        margin: 4px;
    }

    .quick-actions-item:hover {
        background: linear-gradient(135deg, #f8f9fc, #e8ecf4);
        transform: translateY(-2px);
    }

    .avatar-item {
        width: 48px;
        height: 48px;
        margin: 0 auto 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }

    /* User Dropdown */
    .user-box {
        padding: 20px;
        text-align: center;
        background: linear-gradient(135deg, #f8f9fc, #e8ecf4);
        border-radius: 12px;
        margin: 8px;
    }

    .avatar-lg img {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .u-text h4 {
        margin: 12px 0 4px;
        color: #2c3e50;
        font-weight: 600;
    }

    .u-text p {
        margin-bottom: 12px;
        color: #7f8c8d;
        font-size: 14px;
    }

    .btn-secondary {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border: none;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        padding: 6px 16px;
        transition: transform 0.2s ease;
    }

    .btn-secondary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .profile-username {
            display: none;
        }

        .topbar-icon .nav-link {
            width: 40px;
            height: 40px;
        }
    }

    /* Search Form */
    .navbar-form .input-group {
        border-radius: 12px;
        overflow: hidden;
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.06);
    }

    .navbar-form .form-control {
        border: none;
        padding: 12px 16px;
        background: transparent;
    }

    .navbar-form .form-control:focus {
        box-shadow: none;
        outline: none;
    }

    /* Smooth scrollbars */
    .scrollbar-outer::-webkit-scrollbar {
        width: 6px;
    }

    .scrollbar-outer::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 3px;
    }

    .scrollbar-outer::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 3px;
    }

    .scrollbar-outer::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #5a6fd8, #6a4190);
    }
</style>

<div class="main-header">
    <div class="main-header-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="index.html" class="logo">
                <img src="assets/img/TSTShop/LogoTSTFashionShop.webp" alt="thương hiệu navbar" class="navbar-brand"
                    height="20" />
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>

    <!-- Navbar Header -->
    <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
        <div class="container-fluid">
            <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <!-- Search Mobile -->
                <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);"
                        role="button" aria-expanded="false" aria-haspopup="true">
                        <i class="fa fa-search"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-search animated fadeIn">
                        <form class="navbar-left navbar-form nav-search">
                            <div class="input-group">
                                <input type="text" placeholder="Tìm kiếm ..." class="form-control" />
                            </div>
                        </form>
                    </ul>
                </li>

                <!-- Messages -->
                <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="messageDropdown" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-envelope"></i>
                    </a>
                    <ul class="dropdown-menu messages-notif-box animated fadeIn" aria-labelledby="messageDropdown">
                        <li>
                            <div class="dropdown-title d-flex justify-content-between align-items-center">
                                <span>Tin nhắn</span>
                                <a href="javascript:void(0);" class="small text-primary">Đánh dấu đã đọc</a>
                            </div>
                        </li>
                        <li>
                            <div class="message-notif-scroll scrollbar-outer">
                                <div class="notif-center">
                                    <a href="javascript:void(0);">
                                        <div class="notif-img">
                                            <img src="https://res.cloudinary.com/dc2zvj1u4/image/upload/v1747743499/blog_images/file_xyoqqa.png"
                                                alt="Ảnh đại diện" />
                                        </div>
                                        <div class="notif-content">
                                            <span class="subject fw-bold">Jimmy Denis</span>
                                            <span class="block">Bạn khỏe không?</span>
                                            <span class="time">5 phút trước</span>
                                        </div>
                                    </a>
                                    <a href="javascript:void(0);">
                                        <div class="notif-img">
                                            <img src="https://res.cloudinary.com/dc2zvj1u4/image/upload/v1747743499/blog_images/file_xyoqqa.png"
                                                alt="Ảnh đại diện" />
                                        </div>
                                        <div class="notif-content">
                                            <span class="subject fw-bold">Chad</span>
                                            <span class="block">Ok, Cảm ơn!</span>
                                            <span class="time">12 phút trước</span>
                                        </div>
                                    </a>
                                    <a href="javascript:void(0);">
                                        <div class="notif-img">
                                            <img src="https://res.cloudinary.com/dc2zvj1u4/image/upload/v1747743499/blog_images/file_xyoqqa.png"
                                                alt="Ảnh đại diện" />
                                        </div>
                                        <div class="notif-content">
                                            <span class="subject fw-bold">Jhon Doe</span>
                                            <span class="block">Sẵn sàng cho cuộc họp hôm nay...</span>
                                            <span class="time">12 phút trước</span>
                                        </div>
                                    </a>
                                    <a href="javascript:void(0);">
                                        <div class="notif-img">
                                            <img src="https://res.cloudinary.com/dc2zvj1u4/image/upload/v1747743499/blog_images/file_xyoqqa.png"
                                                alt="Ảnh đại diện" />
                                        </div>
                                        <div class="notif-content">
                                            <span class="subject fw-bold">Talha</span>
                                            <span class="block">Chào bạn, bạn khỏe không?</span>
                                            <span class="time">17 phút trước</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="see-all" href="javascript:void(0);">
                                Xem tất cả tin nhắn <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Notifications -->
                <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="notifDropdown" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bell"></i>
                        <span class="notification">4</span>
                    </a>
                    <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
                        <li>
                            <div class="dropdown-title">
                                Bạn có 4 thông báo mới
                            </div>
                        </li>
                        <li>
                            <div class="notif-scroll scrollbar-outer">
                                <div class="notif-center">
                                    <a href="javascript:void(0);">
                                        <div class="notif-icon notif-primary">
                                            <i class="fa fa-user-plus"></i>
                                        </div>
                                        <div class="notif-content">
                                            <span class="block">Người dùng mới đăng ký</span>
                                            <span class="time">5 phút trước</span>
                                        </div>
                                    </a>
                                    <a href="javascript:void(0);">
                                        <div class="notif-icon notif-success">
                                            <i class="fa fa-comment"></i>
                                        </div>
                                        <div class="notif-content">
                                            <span class="block">Rahmad đã bình luận trên Admin</span>
                                            <span class="time">12 phút trước</span>
                                        </div>
                                    </a>
                                    <a href="javascript:void(0);">
                                        <div class="notif-img">
                                            <img src="https://res.cloudinary.com/dc2zvj1u4/image/upload/v1747743499/blog_images/file_xyoqqa.png"
                                                alt="Ảnh đại diện" />
                                        </div>
                                        <div class="notif-content">
                                            <span class="block">Reza đã gửi tin nhắn cho bạn</span>
                                            <span class="time">12 phút trước</span>
                                        </div>
                                    </a>
                                    <a href="javascript:void(0);">
                                        <div class="notif-icon notif-danger">
                                            <i class="fa fa-heart"></i>
                                        </div>
                                        <div class="notif-content">
                                            <span class="block">Farrah đã thích Admin</span>
                                            <span class="time">17 phút trước</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="see-all" href="javascript:void(0);">
                                Xem tất cả thông báo <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Quick Actions -->
                <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a class="nav-link" data-bs-toggle="dropdown" href="javascript:void(0);" aria-expanded="false">
                        <i class="fas fa-layer-group"></i>
                    </a>
                    <div class="dropdown-menu quick-actions animated fadeIn">
                        <div class="quick-actions-header">
                            <span class="title mb-1 fw-bold">Hành động nhanh</span>
                            <span class="subtitle op-7">Phím tắt</span>
                        </div>
                        <div class="quick-actions-scroll scrollbar-outer">
                            <div class="quick-actions-items">
                                <div class="row m-0">
                                    <a class="col-6 col-md-4 p-0" href="javascript:void(0);">
                                        <div class="quick-actions-item">
                                            <div class="avatar-item bg-danger rounded-circle">
                                                <i class="far fa-calendar-alt"></i>
                                            </div>
                                            <span class="text">Lịch</span>
                                        </div>
                                    </a>
                                    <a class="col-6 col-md-4 p-0" href="javascript:void(0);">
                                        <div class="quick-actions-item">
                                            <div class="avatar-item bg-warning rounded-circle">
                                                <i class="fas fa-map"></i>
                                            </div>
                                            <span class="text">Bản đồ</span>
                                        </div>
                                    </a>
                                    <a class="col-6 col-md-4 p-0" href="javascript:void(0);">
                                        <div class="quick-actions-item">
                                            <div class="avatar-item bg-info rounded-circle">
                                                <i class="fas fa-file-excel"></i>
                                            </div>
                                            <span class="text">Báo cáo</span>
                                        </div>
                                    </a>
                                    <a class="col-6 col-md-4 p-0" href="javascript:void(0);">
                                        <div class="quick-actions-item">
                                            <div class="avatar-item bg-success rounded-circle">
                                                <i class="fas fa-envelope"></i>
                                            </div>
                                            <span class="text">Email</span>
                                        </div>
                                    </a>
                                    <a class="col-6 col-md-4 p-0" href="javascript:void(0);">
                                        <div class="quick-actions-item">
                                            <div class="avatar-item bg-primary rounded-circle">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </div>
                                            <span class="text">Hóa đơn</span>
                                        </div>
                                    </a>
                                    <a class="col-6 col-md-4 p-0" href="javascript:void(0);">
                                        <div class="quick-actions-item">
                                            <div class="avatar-item bg-secondary rounded-circle">
                                                <i class="fas fa-credit-card"></i>
                                            </div>
                                            <span class="text">Thanh toán</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- User Profile -->
                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="javascript:void(0);"
                        aria-expanded="false">
                        <div class="avatar-sm">
                            <img src="{{ $staff->avatar ?? asset('assets/img/profile.jpg') }}" alt="..."
                                class="avatar-img rounded-circle" />
                        </div>
                        <span class="profile-username">
                            <span class="op-7">Xin chào,</span>
                            <span class="fw-bold">{{ auth()->user()->name }}</span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-user animated fadeIn">
                        <div class="dropdown-user-scroll scrollbar-outer">
                            <li>
                                <div class="user-box">
                                    <div class="avatar-lg">
                                        <img src="{{ $staff->avatar ?? asset('assets/img/profile.jpg') }}"
                                            alt="ảnh đại diện" class="avatar-img rounded" />
                                    </div>
                                    <div class="u-text">
                                        <h4>{{ auth()->user()->name }}</h4>
                                        <p class="text-muted">{{ auth()->user()->email }}</p>
                                        <a href="{{ route('staff.profile') }}"
                                            class="btn btn-xs btn-secondary btn-sm">Xem hồ sơ</a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('staff.profile') }}">
                                    <i class="fas fa-user me-2"></i>Hồ sơ của tôi
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('admin.logout') }}">
                                    <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                </a>
                            </li>
                        </div>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <!-- End Navbar -->
</div>

@php
    $userId = auth()->user()->id - 1;
    $staff = DB::table('staff')->where('id', $userId)->select('avatar')->first();
    // dd($staff);
@endphp
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
                <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="messageDropdown" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-envelope"></i>
                    </a>
                    <ul class="dropdown-menu messages-notif-box animated fadeIn" aria-labelledby="messageDropdown">
                        <li>
                            <div class="dropdown-title d-flex justify-content-between align-items-center">
                                Tin nhắn
                                <a href="javascript:void(0);" class="small">Đánh dấu đã đọc tất cả</a>
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
                                            <span class="subject">Jimmy Denis</span>
                                            <span class="block"> Bạn khỏe không? </span>
                                            <span class="time">5 phút trước</span>
                                        </div>
                                    </a>
                                    <a href="javascript:void(0);">
                                        <div class="notif-img">
                                            <img src="https://res.cloudinary.com/dc2zvj1u4/image/upload/v1747743499/blog_images/file_xyoqqa.png"
                                                alt="Ảnh đại diện" />
                                        </div>
                                        <div class="notif-content">
                                            <span class="subject">Chad</span>
                                            <span class="block"> Ok, Cảm ơn! </span>
                                            <span class="time">12 phút trước</span>
                                        </div>
                                    </a>
                                    <a href="javascript:void(0);">
                                        <div class="notif-img">
                                            <img src="https://res.cloudinary.com/dc2zvj1u4/image/upload/v1747743499/blog_images/file_xyoqqa.png"
                                                alt="Ảnh đại diện" />
                                        </div>
                                        <div class="notif-content">
                                            <span class="subject">Jhon Doe</span>
                                            <span class="block">
                                                Sẵn sàng cho cuộc họp hôm nay...
                                            </span>
                                            <span class="time">12 phút trước</span>
                                        </div>
                                    </a>
                                    <a href="javascript:void(0);">
                                        <div class="notif-img">
                                            <img src="https://res.cloudinary.com/dc2zvj1u4/image/upload/v1747743499/blog_images/file_xyoqqa.png"
                                                alt="Ảnh đại diện" />
                                        </div>
                                        <div class="notif-content">
                                            <span class="subject">Talha</span>
                                            <span class="block"> Chào bạn, bạn khỏe không? </span>
                                            <span class="time">17 phút trước</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="see-all" href="javascript:void(0);">Xem tất cả tin nhắn<i
                                    class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>
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
                                            <span class="block"> Người dùng mới đăng ký </span>
                                            <span class="time">5 phút trước</span>
                                        </div>
                                    </a>
                                    <a href="javascript:void(0);">
                                        <div class="notif-icon notif-success">
                                            <i class="fa fa-comment"></i>
                                        </div>
                                        <div class="notif-content">
                                            <span class="block">
                                                Rahmad đã bình luận trên Admin
                                            </span>
                                            <span class="time">12 phút trước</span>
                                        </div>
                                    </a>
                                    <a href="javascript:void(0);">
                                        <div class="notif-img">
                                            <img src="https://res.cloudinary.com/dc2zvj1u4/image/upload/v1747743499/blog_images/file_xyoqqa.png"
                                                alt="Ảnh đại diện" />
                                        </div>
                                        <div class="notif-content">
                                            <span class="block">
                                                Reza đã gửi tin nhắn cho bạn
                                            </span>
                                            <span class="time">12 phút trước</span>
                                        </div>
                                    </a>
                                    <a href="javascript:void(0);">
                                        <div class="notif-icon notif-danger">
                                            <i class="fa fa-heart"></i>
                                        </div>
                                        <div class="notif-content">
                                            <span class="block"> Farrah đã thích Admin </span>
                                            <span class="time">17 phút trước</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="see-all" href="javascript:void(0);">Xem tất cả thông báo<i
                                    class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a class="nav-link" data-bs-toggle="dropdown" href="javascript:void(0);" aria-expanded="false">
                        <i class="fas fa-layer-group"></i>
                    </a>
                    <div class="dropdown-menu quick-actions animated fadeIn">
                        <div class="quick-actions-header">
                            <span class="title mb-1">Hành động nhanh</span>
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

                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="javascript:void(0);"
                        aria-expanded="false">
                        <div class="avatar-sm">
                            <img src="{{ $staff->avatar ?? asset('assets/img/profile.jpg') }} " alt="..."
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
                                        <img src="{{ $staff->avatar ?? asset('assets/img/profile.jpg') }} "
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
                                <a class="dropdown-item" href="{{ route('staff.profile') }}">Hồ sơ của tôi</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('admin.logout') }}">Đăng xuất</a>
                            </li>
                        </div>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <!-- End Navbar -->
</div>

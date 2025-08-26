<!-- Page Preloder -->
<div id="preloder">
    <div class="loader"></div>
</div>
@php
    if (Session::has('cart')) {
        $cartQuantity = count(Session::get('cart'));
    }

    if (Session::has('wishlist')) {
        $wishlistQuantity = count(Session::get('wishlist'));
    }
@endphp

@if (Session::has('success'))
    <div class="shadow-lg p-2 move-from-top login-success-notify"
        style="width: 16rem; position: fixed; text-align: center; border-radius: 8px;
    background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); margin: 0; z-index: 99999;
    top: 0; left: 40%; opacity: 0.9;">
        <i class="fas fa-check p-2 bg-success text-white rounded-circle pe-2 mx-2"></i>{{ Session::get('success') }}
    </div>
@endif


<!--************************* Offcanvas Menu Begin Menu ẩn nhé ***************-->
<div class="offcanvas-menu-overlay"></div>
<div class="offcanvas-menu-wrapper">
    <!-- Thêm nút đóng X -->
    <div class="offcanvas__close">
        <span class="offcanvas__close__btn" onclick="closeOffcanvas()">
            <i class="fa fa-times"></i>
        </span>
    </div>

    <div class="offcanvas__option">
        <div class="offcanvas__links">
            <a href="{{ route('user.login') }}">Đăng nhập</a>
        </div>
        <div class="offcanvas__top__hover">
            <span>Hỗ trợ<i class="arrow_carrot-down"></i></span>
            <ul class="bg-white">
                <li>
                    <a class="text-dark" href="{{ route('sites.contact') }}">Liên Hệ
                </li>
                <li><a class="text-dark" href="javascript:void(0);">Chat</li>
            </ul>
        </div>
    </div>
    <div class="offcanvas__nav__option">
        <a href="{{ route('sites.shop') }}" class="search-switch"><img src="{{ asset('client/img/icon/search.png') }}"
                alt=""></a>
        <a href="{{ route('sites.wishlist') }}" style="position: relative; display: inline-block;">
            <img src="{{ asset('client/img/icon/heart.png') }}" width="20" alt="">
            <span class="wishlist-quantity-header"
                style="position: absolute; top: -5px; left: 12px; background: rgb(0, 0, 0); color: white; font-size: 12px; font-weight: bold; width: 16px; height: 16px; line-height: 16px; text-align: center; border-radius: 50%;">
                {{ $wishlistQuantity ?? 0 }}
            </span>
        </a>
        <a href="{{ route('sites.cart') }}" style="position: relative; display: inline-block;">
            <img src="{{ asset('client/img/icon/cart.png') }}" width="20" alt="">
            <span class="cart-quantity-header"
                style="position: absolute; top: -5px; left: 12px; background: rgb(0, 0, 0); color: white; font-size: 12px; font-weight: bold; width: 16px; height: 16px; line-height: 16px; text-align: center; border-radius: 50%;">
                {{ $cartQuantity ?? 0 }}</span></a>
    </div>
    <div id="mobile-menu-wrap"></div>
    <div class="offcanvas__text">
        <p>Miễn phí vận chuyển cho đơn 500k, hỗ trợ đổi trả trong vòng 7 ngày</p>
    </div>
</div>
<!-- Offcanvas Menu End -->



<!-- Header Section Begin -->
<header class="header">
    <div class="header__top">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-7">
                    <div class="header__top__left">
                        <p>Miễn phí vận chuyển cho đơn 500k, hỗ trợ đổi trả trong vòng 7 ngày</p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-5">
                    <div class="header__top__right">
                        <div class="header__top__hover">
                            @php
                                $avatarUrl = asset('client/img/avatar-user.png'); // Ảnh mặc định

                                if (Auth::guard('customer')->check()) {
                                    $user = Auth::guard('customer')->user();

                                    if ($user && !empty($user->image)) {
                                        if (filter_var($user->image, FILTER_VALIDATE_URL)) {
                                            // Nếu ảnh là URL (Google/Facebook)
                                            $avatarUrl = $user->image;
                                        } else {
                                            // Nếu ảnh được lưu trong thư mục client/img
                                            $avatarUrl = asset('client/img/' . $user->image);
                                        }
                                    }
                                }
                            @endphp


                            @if (Auth::guard('customer')->check())
                                <img src="{{ $avatarUrl }}" alt="User Avatar" width="30" alt=""
                                    class="rounded-circle">
                                <span class="text-dark">Xin chào, {{ Auth::guard('customer')->user()->name }}<i
                                        class="arrow_carrot-down text-dark"></i></span>
                                <ul>
                                    <li><a class="text-dark" href="{{ route('user.profile') }}">Hồ sơ cá nhân</a></li>
                                    <li><a class="text-dark" href="{{ route('sites.getHistoryOrder') }}">Lịch sử giao
                                            dịch</a>
                                    </li>
                                    <li><a class="text-dark" href="{{ route('sites.wishlist') }}">Quản lý địa chỉ</a></li>
                                    <li><a class="text-dark" href="{{ route('sites.coupon') }}">Mã giảm giá &
                                            Voucher</a></li>
                                    <li><a class="text-dark" href="{{ route('user.logout') }}">Đăng Xuất</a></li>
                                </ul>
                            @else
                                <span class=""><a class="text-dark" href="{{ route('user.login') }}">Đăng
                                        nhập</a></span>
                            @endif
                        </div>
                        <div class="ms-3 header__top__hover">
                            <span class="text-dark">Hỗ trợ<i class="arrow_carrot-down"></i></span>
                            <ul>
                                <li>
                                    <a class="text-dark" href="{{ route('sites.contact') }}">Liên Hệ
                                </li>
                                <li><a class="text-dark" href="javascript:void(0);">Chat</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3">
                <div class="header__logo">
                    <a href="{{ route('sites.home') }}" class="text-dark font-weight-bold">
                        <img class="rounded-circle" src="{{ asset('assets/img/TSTShop/LogoTSTFashionShop.webp') }}"
                            alt="Logo" width="35">
                        TFashionShop
                    </a>
                </div>
            </div>

            <div class="col-lg-6 col-md-6">
                <nav class="header__menu mobile-menu">
                    <ul>
                        <li class="{{ request()->routeIs('sites.home') ? 'active' : '' }}">
                            <a href="{{ route('sites.home') }}">Home</a>
                        </li>
                        <li class="{{ request()->routeIs('sites.shop') ? 'active' : '' }}">
                            <a href="{{ route('sites.shop') }}">Shop</a>
                        </li>
                        <li class="{{ request()->routeIs('sites.aboutUs') ? 'active' : '' }}">
                            <a href="{{ route('sites.aboutUs') }}">About Us</a>
                        </li>
                        <li class="{{ request()->routeIs('sites.contact') ? 'active' : '' }}">
                            <a href="{{ route('sites.contact') }}">Contacts</a>
                        </li>
                        <li class="{{ request()->routeIs('sites.blog') ? 'active' : '' }}">
                            <a href="{{ route('sites.blog') }}">Blog</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="col-lg-3 col-md-3">
                <div class="header__nav__option">
                    <a class="search-btn" style="cursor: pointer;">
                        <i class="fa fa-fw fa-search text-dark" style="font-size: 20px"></i>
                    </a>
                    <a href="{{ route('sites.wishlist') }}" style="position: relative; display: inline-block;">
                        <img src="{{ asset('client/img/icon/heart.png') }}" width="20" alt="">
                        <span class="wishlist-quantity-header"
                            style="position: absolute; top: -5px; left: 12px; background: rgb(0, 0, 0); color: white; font-size: 12px; font-weight: bold; width: 16px; height: 16px; line-height: 16px; text-align: center; border-radius: 50%;">
                            {{ $wishlistQuantity ?? 0 }}
                        </span>
                    </a>
                    <a href="{{ route('sites.cart') }}" style="position: relative; display: inline-block;">
                        <img src="{{ asset('client/img/icon/cart.png') }}" width="20" alt="">
                        <span class="cart-quantity-header"
                            style="position: absolute; top: -5px; left: 12px; background: rgb(0, 0, 0); color: white; font-size: 12px; font-weight: bold; width: 16px; height: 16px; line-height: 16px; text-align: center; border-radius: 50%;">
                            {{ $cartQuantity ?? 0 }}</span></a>
                </div>
            </div>
        </div>
        <div class="canvas__open"><i class="fa fa-bars"></i></div>
    </div>
</header>
<!-- Header Section End -->

@section('css')
    <link rel="stylesheet" href="{{ asset('client/css/header.css') }}">
    <style>
        .move-from-top {
            animation: slide-down 0.4s ease-out;
        }

        @keyframes slide-down {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .offcanvas__close {
            position: absolute;
            top: 20px;
            right: -50px;
            /* Đẩy ra ngoài cùng bên phải */
            z-index: 9999;
        }

        .offcanvas__close__btn {
            display: inline-block;
            width: 30px;
            height: 30px;
            background: #000;
            color: #fff;
            text-align: center;
            line-height: 30px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .offcanvas__close__btn:hover {
            background: #666;
            transform: rotate(90deg);
        }

        .offcanvas__close__btn i {
            font-size: 14px;
        }
    </style>
@endsection


<script>
    // JavaScript để đóng offcanvas
    function closeOffcanvas() {
        // Ẩn offcanvas menu
        document.querySelector('.offcanvas-menu-wrapper').classList.remove('active');
        document.querySelector('.offcanvas-menu-overlay').classList.remove('active');
        document.body.classList.remove('offcanvas-menu-open');
    }

    // Cũng có thể đóng khi click vào overlay
    document.querySelector('.offcanvas-menu-overlay').addEventListener('click', function() {
        closeOffcanvas();
    });

    setTimeout(function() {
        var alert = document.querySelector('.login-success-notify');
        if (alert) {
            alert.classList.remove('show'); // Bootstrap ẩn alert
            alert.classList.add('fade'); // Tạo hiệu ứng mờ dần
            setTimeout(() => alert.remove(), 500); // Xóa khỏi DOM sau khi mờ dần
        }
    }, 2000);
</script>

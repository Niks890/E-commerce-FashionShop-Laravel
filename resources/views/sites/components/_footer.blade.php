<!-- Footer Section Begin -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="footer__about">
                    <div class="footer__logo">
                        <a href="{{ route('sites.home') }}" class="text-white font-weight-bold text-uppercase">
                            <img class="rounded-circle" src="{{ asset('assets/img/TSTShop/LogoTSTFashionShop.webp') }}"
                                alt="Logo" width="35">
                            VMT Fashion Shop
                        </a>
                    </div>
                    <p>Khách hàng là trọng tâm trong mô hình kinh doanh độc đáo của chúng tôi, bao gồm cả thiết kế.</p>
                    <a href="{{ route('sites.home') }}"><img src="{{ asset('client/img/payment.png') }}"
                            alt=""></a>
                </div>
            </div>
            <div class="col-lg-2 offset-lg-1 col-md-3 col-sm-6">
                <div class="footer__widget">
                    <h6>Mua Sắm</h6>
                    <ul>
                        <li><a href="{{ route('sites.home') }}">Cửa Hàng Quần Áo</a></li>
                        <li><a href="{{ route('sites.home') }}">Xu Hướng</a></li>
                        <li><a href="{{ route('sites.home') }}">Cộng Tác</a></li>
                        <li><a href="{{ route('sites.home') }}">Khuyến Mãi</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-6">
                <div class="footer__widget">
                    <h6>Chính Sách</h6>
                    <ul>
                        <li><a href="{{ route('sites.home') }}">Liên Hệ</a></li>
                        <li><a href="{{ route('sites.home') }}">Thanh Toán</a></li>
                        <li><a href="{{ route('sites.home') }}">Vận Chuyển</a></li>
                        <li><a href="{{ route('sites.home') }}">Chính Sách Đổi Trả</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 offset-lg-1 col-md-6 col-sm-6">
                <div class="footer__widget">
                    <h6>Tin Tức</h6>
                    <div class="footer__newslatter">
                        <p>Trở thành người đầu tiên nhận thông báo khuyến mãi, thông tin sản phẩm mới nhất!</p>
                        <form action="{{ route('sites.contact') }}#contact-page">
                            <input type="text" placeholder="Your email" class="text-white">
                            <button type="submit"><span class="icon_mail_alt"></span></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="footer__copyright__text">
                    <p>Copyright ©
                        <script>
                            document.write(new Date().getFullYear());
                        </script>
                        Bản quyền thuộc về TFashionShop by <a href={{ route('sites.home') }} target="#">VMT</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- Footer Section End -->

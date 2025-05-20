@extends('sites.master')
@section('title', 'Về chúng tôi')
@section('content')
    <!-- Phần Breadcrumb Bắt đầu -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>About Us</h4>
                        <div class="breadcrumb__links">
                            <a href="{{ route('sites.home') }}">Home</a>
                            <span>About Us</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Phần Breadcrumb Kết thúc -->

    <!-- Phần Giới Thiệu Bắt đầu -->
    <section class="about spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="about__pic">
                        <img src="{{ ('client/img/about/about-us.jpg') }}" alt="">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="about__item">
                        <h4>Chúng tôi là ai?</h4>
                        <p>Các chương trình quảng cáo theo ngữ cảnh đôi khi có những chính sách nghiêm ngặt cần phải tuân thủ.
                            Hãy lấy Google làm ví dụ.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="about__item">
                        <h4>Chúng tôi làm gì?</h4>
                        <p>Trong thời đại số, nơi mà thông tin có thể dễ dàng truy cập trong vài giây, danh thiếp vẫn giữ được tầm quan trọng của nó.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="about__item">
                        <h4>Tại sao chọn chúng tôi?</h4>
                        <p>Một ngôi nhà hai hoặc ba tầng là cách lý tưởng để tối ưu hóa diện tích đất, nhưng đối với người lớn tuổi hoặc người khuyết tật thì có thể gặp khó khăn.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Phần Giới Thiệu Kết thúc -->

    <!-- Phần Đánh Giá Khách Hàng Bắt đầu -->
    <section class="testimonial">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 p-0">
                    <div class="testimonial__text">
                        <span class="icon_quotations"></span>
                        <p>“Ra ngoài sau giờ làm? Mang theo máy uốn tóc butane đến văn phòng, làm nóng nó, tạo kiểu tóc trước khi rời đi và bạn sẽ không cần phải quay về nhà.”</p>
                        <div class="testimonial__author">
                            <div class="testimonial__author__pic">
                                <img src="{{ ('client/img/about/RaidenShogun.png') }}" alt="">
                            </div>
                            <div class="testimonial__author__text">
                                <h5>Raiden Shogun</h5>
                                <p>Thiết kế thời trang</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 p-0">
                    <div class="testimonial__pic set-bg" data-setbg="{{ ('client/img/about/testimonial-pic.jpg') }}"></div>
                </div>
            </div>
        </div>
    </section>
    <!-- Phần Đánh Giá Khách Hàng Kết thúc -->

        <!-- Counter Section Begin -->
    <section class="counter spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="counter__item">
                        <div class="counter__item__number">
                            <h2 class="cn_num">102</h2>
                        </div>
                        <span>Đối tác <br />Khách hàng</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="counter__item">
                        <div class="counter__item__number">
                            <h2 class="cn_num">30</h2>
                        </div>
                        <span>Hơn <br />Danh mục sản phẩm</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="counter__item">
                        <div class="counter__item__number">
                            <h2 class="cn_num">102</h2>
                        </div>
                        <span><br />Quốc gia</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="counter__item">
                        <div class="counter__item__number">
                            <h2 class="cn_num">99</h2>
                            <strong>%</strong>
                        </div>
                        <span> <br />Khách hàng hài lòng</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Counter Section End -->

    <!-- Team Section Begin -->
    <section class="team spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <span>Team Members</span>
                        <h2>Gặp gỡ các thành viên</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="team__item">
                        <img src="{{ ('client/img/about/RaidenShogun.png') }}" class="img-fluid" alt="">
                        <h4>Raiden Shogun</h4>
                        <span>Fashion Design</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="team__item">
                        <img src="{{ ('client/img/about/Mavuika.png') }}" class="img-fluid" alt="">
                        <h4>Mavuika</h4>
                        <span>C.E.O</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="team__item">
                        <img src="{{ ('client/img/about/Furina.png') }}" class="img-fluid" alt="">
                        <h4>Furina</h4>
                        <span>Manager</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="team__item">
                        <img src="{{ ('client/img/about/Kiara.png') }}" class="img-fluid" alt="">
                        <h4>Kiara</h4>
                        <span>Delivery</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Team Section End -->

    <!-- Client Section Begin -->
    <section class="clients spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <span>Đối tác</span>
                        <h2>Đồng hành với chúng tôi</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-4 col-6">
                    <a href="javascript:void(0);" class="client__item"><img src="{{ ('client/img/clients/client-1.png') }}" alt=""></a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-4 col-6">
                    <a href="javascript:void(0);" class="client__item"><img src="{{ ('client/img/clients/client-2.png') }}" alt=""></a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-4 col-6">
                    <a href="javascript:void(0);" class="client__item"><img src="{{ ('client/img/clients/client-3.png') }}" alt=""></a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-4 col-6">
                    <a href="javascript:void(0);" class="client__item"><img src="{{ ('client/img/clients/client-4.png') }}" alt=""></a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-4 col-6">
                    <a href="javascript:void(0);" class="client__item"><img src="{{ ('client/img/clients/client-5.png') }}" alt=""></a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-4 col-6">
                    <a href="javascript:void(0);" class="client__item"><img src="{{ ('client/img/clients/client-6.png') }}" alt=""></a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-4 col-6">
                    <a href="javascript:void(0);" class="client__item"><img src="{{ ('client/img/clients/client-7.png') }}" alt=""></a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-4 col-6">
                    <a href="javascript:void(0);" class="client__item"><img src="{{ ('client/img/clients/client-8.png') }}" alt=""></a>
                </div>
            </div>
        </div>
    </section>
    <!-- Client Section End -->

@endsection


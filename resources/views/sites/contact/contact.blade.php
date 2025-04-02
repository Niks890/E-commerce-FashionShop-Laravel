@extends('sites.master')

@section('title', 'Liên Hệ')

@section('content')
    <!-- Map Begin -->
    <div class="container">
        <div id="map" class="map mt-3"></div>
    </div>


    <!-- Map End -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var mapContainer = document.getElementById('map');
            mapContainer.style.height = "500px"; // Đặt chiều cao cho bản đồ

            // Khởi tạo bản đồ Leaflet
            var map = L.map(mapContainer).setView([10.030687, 105.769079], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Tạo icon tùy chỉnh cho marker
            var customIcon = L.icon({
                iconUrl: "{{ asset('assets/img/TSTShop/LogoTSTFashionShop.webp') }}", // Đường dẫn icon
                iconSize: [50, 50], // Kích thước icon
                iconAnchor: [25, 50], // Điểm neo icon
                popupAnchor: [0, -50] // Điểm neo popup
            });

            // Thêm marker của TST Fashion Shop
            var marker = L.marker([10.030687, 105.769079], {
                icon: customIcon,
                title: "TST Fashion Shop",
                alt: "TST Fashion Shop"
            }).addTo(map);

            // Nội dung popup
            var popupContent = `
                <div style="font-family: Arial, sans-serif; font-size: 14px;">
                    <img class="rounded-circle" src="{{ asset('assets/img/TSTShop/LogoTSTFashionShop.webp') }}"
                        alt="TST Fashion Shop" width="75">
                    <h3 style="color: #2584d8;">Thông tin địa điểm</h3>
                    <p><strong>Tên địa điểm:</strong> TST Fashion Shop</p>
                    <p><strong>Vị trí:</strong> 3/2, Xuân Khánh, Cần Thơ</p>
                    <p><strong>Chi tiết:</strong> Cửa hàng thời trang TST Fashion</p>
                </div>`;

            // Gán popup vào marker
            marker.bindPopup(popupContent).openPopup();
            document.querySelector('.leaflet-interactive').classList.add('rounded-circle');
        });
    </script>


    <!-- Contact Section Begin -->
    <section class="contact spad" id="contact-page">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="contact__text">
                        <div class="section-title">
                            <span>Thông Tin Liên Hệ</span>
                            <h2>Liên Hệ Với Chúng Tôi</h2>
                            <p>Chúng tôi luôn chú trọng đến từng chi tiết để mang đến cho khách hàng trải nghiệm dịch vụ tốt
                                nhất.</p>
                        </div>
                        <ul>
                            <li>
                                <h4>Chi Nhánh Cần Thơ</h4>
                                <p>3/2, Xuân Khánh, Cần Thơ<br />+43 982-314-0958</p>
                            </li>
                            <li>
                                <h4>Chi Nhánh Hồ Chí Minh</h4>
                                <p>Quận Cam, Hồ Chí Minh<br />+12 345-423-9893</p>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="contact__form">
                        <form action="{{ route('contact.send') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <input type="text" name="name" placeholder="Họ và Tên" required>
                                </div>
                                <div class="col-lg-6">
                                    <input type="email" name="email" placeholder="Email" required>
                                </div>
                                <div class="col-lg-12">
                                    <textarea placeholder="Nội dung tin nhắn" name="message" required></textarea>
                                    <input type="submit" name="send-message" class="site-btn" value="Gửi Tin Nhắn">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section End -->
@endsection

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

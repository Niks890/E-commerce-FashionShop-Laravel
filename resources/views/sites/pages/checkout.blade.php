{{-- @php
    dd(Session::get('percent_discount'), Session::get('cart'), Session::get('voucher_id'));
@endphp --}}
@php
    $percentDiscount = Session::get('percent_discount', 0);
    $voucherId = Session::get('voucher_id');
    $discountInfo = null;

    if ($percentDiscount > 0 && $voucherId) {
        $voucher = \App\Models\Voucher::find($voucherId);
        if ($voucher) {
            $discountInfo = [
                'percent' => $percentDiscount * 100,
                'code' => $voucher->vouchers_code,
                'max_discount' => $voucher->vouchers_max_discount,
            ];
        }
    }
@endphp
{{-- @extends('sites.master') --}}
@extends('sites.master', ['hideChatbox' => true])
@section('title', 'Thanh toán')
@section('content')
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Thanh Toán</h4>
                        <div class="breadcrumb__links">
                            <a href="{{ route('sites.home') }}">Home</a>
                            <a href="{{ route('sites.shop') }}">Shop</a>
                            <span>Thanh Toán</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="checkout spad">
        <div class="container">
            <div class="checkout__form">
                <form action="{{ route('payment.checkout') }}" method="POST" id="checkout-form">
                    @csrf
                    <div class="row">
                        <div class="col-lg-7 col-md-6">
                            <h6 class="coupon__code"><span class="icon_tag_alt"></span> Bạn có mã giảm giá? <a
                                    href="{{ route('sites.cart') }}">Click vào đây</a> để áp mã giảm giá cho đơn hàng</h6>
                            <h6 class="checkout__title">Thông tin người nhận</h6>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="checkout__input">
                                        <p>Tên người nhận<span>*</span></p>
                                        <input type="text" class="text-dark" name="receiver_name" required>
                                        @error('receiver_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Dynamic Address Fields --}}
                            <div class="row mb-3">
                                <div class="col-lg-4">
                                    <div class="checkout__input">
                                        <p>Tỉnh/Thành phố<span>*</span></p>
                                        <select name="province" id="province-select" class="form-control" required>
                                            <option value="">-- Chọn Tỉnh/Thành phố --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="checkout__input">
                                        <p>Quận/Huyện<span>*</span></p>
                                        <select name="district" id="district-select" class="form-control" required disabled>
                                            <option value="">-- Chọn Quận/Huyện --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="checkout__input">
                                        <p>Phường/Xã<span>*</span></p>
                                        <select name="ward" id="ward-select" class="form-control" required disabled>
                                            <option value="">-- Chọn Phường/Xã --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="checkout__input">
                                        <p>Số nhà, Tên đường<span>*</span></p>
                                        <input type="text" class="text-dark" placeholder="Ví dụ: 123 Đường 3/2"
                                            class="checkout__input__add" name="street_address" required>
                                    </div>
                                </div>
                                <input type="hidden" name="address" id="full-address-input">
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="checkout__input">
                                        <p>Số điện thoại<span>*</span></p>
                                        <input class="text-dark" type="text" name="phone" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="checkout__input">
                                        <p>Email<span>*</span></p>
                                        <input type="text" class="text-dark" name="email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="checkout__input">
                                <p>Ghi chú<span></span></p>
                                <input type="text" class="text-dark" placeholder="Ghi chú cho đơn hàng (nếu có)"
                                    name="note">
                                {{-- Changed required to optional for notes --}}
                            </div>
                            <div class="checkout__input__checkbox">
                                <a href="{{ route('user.login') }}">Tạo tài khoản mua hàng?</a>
                                <p>Tạo tài khoản ngay để nhận những ưu đãi khi mua hàng tại TFashionShop!</p>
                            </div>
                            <div class="mt-3">
                                <strong>Ưu đãi khi mua hàng tại TFashionShop: </strong>
                                <p>Miễn phí giao hàng áp dụng cho đơn hàng giao tận nơi từ 500K và tất cả các đơn nhận tại
                                    cửa hàng.</p>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-6">
                            <div class="checkout__order">
                                <h4 class="order__title">Đơn hàng của bạn</h4>
                                @if ($discountInfo)
                                    <div class="alert alert-success mb-3">
                                        <strong>Mã giảm giá đã áp dụng:</strong> {{ $discountInfo['code'] }}
                                        <br>
                                        <strong>Đã giảm:</strong> -{{ $discountInfo['percent'] }}% giá trị đơn hàng
                                        @if ($discountInfo['max_discount'])
                                            {{-- (tối đa {{ number_format($discountInfo['max_discount'], 0, ',', '.') }} đ) --}}
                                        @endif
                                    </div>
                                @endif
                                <div class="checkout__order__products">Sản Phẩm<span>Đơn giá</span></div>
                                @php
                                    $index = 1;
                                    $totalPriceCart = 0;
                                    $vat = 0.1;
                                    $ship = 30000;
                                    $percentDiscount = Session::get('percent_discount', 0);
                                    $discountAmount = 0; // Thêm biến để lưu tổng số tiền được giảm

                                    if (Session::has('cart') && count(Session::get('cart')) > 0) {
                                        $cart = array_filter(Session::get('cart'), function ($item) {
                                            return !empty($item->checked) && $item->checked;
                                        });

                                        foreach ($cart as $items) {
                                            $itemTotal = $items->price * $items->quantity;
                                            $itemDiscount = $itemTotal * $percentDiscount;
                                            $discountAmount += $itemDiscount; // Cộng dồn số tiền được giảm
                                            $totalPriceCart += $itemTotal - $itemDiscount;
                                        }

                                        if ($totalPriceCart >= 500000) {
                                            $ship = 0;
                                        }

                                        $vatPrice = $totalPriceCart * $vat;
                                        $total = $totalPriceCart + $vatPrice + $ship;
                                    } else {
                                        $totalPriceCart = 0;
                                        $vatPrice = 0;
                                        $ship = 0;
                                        $total = 0;
                                    }
                                @endphp


                                @if (Session::has('cart') && count(Session::get('cart')) > 0)
                                    @foreach (Session::get('cart') as $items)
                                        @if (!empty($items->checked) && $items->checked)
                                            <ul class="checkout__total__products">
                                                <li>{{ $index++ }}.
                                                    {{ Str::words($items->name, 10) }}<span>{{ number_format($items->price, 0, ',', '.') . ' đ' }}</span>
                                                    <img src="{{ $items->image }}" width="50" alt="">
                                                    <h6>Số lượng: {{ $items->quantity }}</h6>
                                                    <h6>Size: {{ $items->size }}</h6>
                                                    <h6>Màu: {{ $items->color }}</h6>
                                                </li>
                                            </ul>
                                        @endif
                                    @endforeach
                                @endif
                                <ul class="checkout__total__all">
                                    @if ($discountAmount > 0)
                                        <li>Giảm giá:<span>-{{ number_format($discountAmount, 0, ',', '.') }} đ</span></li>
                                    @endif
                                    <li>Tạm tính:<span>{{ number_format($totalPriceCart, 0, ',', '.') }} đ</span></li>
                                    <li>Thuế VAT (10%):<span>{{ number_format($vatPrice, 0, ',', '.') }} đ</span></li>
                                    <li>Phí Ship:<span>{{ number_format($ship, 0, ',', '.') }} đ</span></li>
                                    <li>Thành tiền:<span>{{ number_format($total, 0, ',', '.') }} đ</span></li>
                                </ul>
                                <div class="checkout__input__checkbox">
                                    <label for="COD">
                                        <img src="{{ asset('client/img/checkout/cod.png') }}" alt=""
                                            width="20">
                                        COD: Thanh toán khi nhận hàng
                                        <input type="radio" name="payment" id="COD" value="COD" checked>
                                        <span class="checkmark"></span>
                                    </label>
                                    <label for="vnpay">
                                        <img src="{{ asset('client/img/checkout/vnpay.png') }}" alt=""
                                            width="20">
                                        VNPAY: Thanh toán qua ví VNPAY
                                        <input type="radio" name="payment" id = "vnpay" value="vnpay">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label for="momo">
                                        <img src="{{ asset('client/img/checkout/momo.png') }}" alt=""
                                            width="20">
                                        Momo: Thanh toán qua ví MoMo
                                        <input type="radio" name="payment" id = "momo" value="momo">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label for="zalopay">
                                        <img src="{{ asset('client/img/checkout/image.png') }}" alt=""
                                            width="20">
                                        ZaloPay: Thanh toán qua ví ZaloPay
                                        <input type="radio" name="payment" id = "zalopay" value="zalopay">
                                        <span class="checkmark"></span>
                                    </label>

                                </div>
                                <input type="hidden" name="total" value="{{ $total }}">
                                <input type="hidden" name ="shipping_fee" value="{{ $ship }}">
                                <input type="hidden" name="VAT" value="{{ $vatPrice }}">
                                {{-- test do chưa có khách hàng --}}
                                <input type="hidden" name="customer_id"
                                    value="{{ Auth::guard('customer')->check() ? Auth::guard('customer')->user()->id : '' }}">
                                {{-- danh sách sản phẩm --}}
                                {{-- <input type="hidden" name="selected_items" id="selected-items"> --}}
                                <input type="submit" id="checkout-form-submit-btn" name="redirect" class="site-btn"
                                    value="ĐẶT HÀNG">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection


@section('css')
    <style>
        #province-select,
        #district-select,
        #ward-select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            height: 40px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        /* Đảm bảo không bị ẩn bởi các style khác */
        .checkout__input select {
            display: block !important;
            opacity: 1 !important;
        }
    </style>
@endsection

@section('js')
    <script>
        // Khai báo biến để lưu trữ toàn bộ dữ liệu địa giới hành chính
        let administrativeData = [];

        document.addEventListener('DOMContentLoaded', function() {
            $('select.form-control').niceSelect('destroy');

            const style = document.createElement('style');
            style.innerHTML = `
        #province-select, #district-select, #ward-select {
            display: block !important;
            width: 100%;
            height: 40px;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .nice-select.form-control { display: none !important; }
    `;
            document.head.appendChild(style);
            const totalAmount = {{ $total }};

            const emailInput = document.querySelector('input[name="email"]');
            const phoneInput = document.querySelector('input[name="phone"]');
            const checkoutForm = document.getElementById('checkout-form');
            const fullAddressInput = document.getElementById('full-address-input');
            const streetAddressInput = document.querySelector('input[name="street_address"]');


            // Dynamic address elements
            const provinceSelect = document.getElementById('province-select');
            const districtSelect = document.getElementById('district-select');
            const wardSelect = document.getElementById('ward-select');

            // --- Thay thế API Integration bằng việc đọc file JSON cục bộ ---

            // Hàm tải dữ liệu từ file JSON
            async function loadAdministrativeData() {
                try {
                    // Sử dụng asset() để lấy đường dẫn public của file JSON
                    // Đảm bảo file data.json của bạn nằm trong thư mục public/dataCountry/
                    const response = await fetch("{{ asset('storage/dataCountry/data.json') }}");
                    administrativeData = await response.json();
                    // console.log('Administrative data loaded successfully:', administrativeData);
                    // Sau khi dữ liệu được tải, gọi hàm fetchProvinces để điền dropdown tỉnh/thành phố
                    populateProvinces();
                } catch (error) {
                    console.error('Error loading administrative data:', error);
                    alert('Không thể tải dữ liệu địa giới hành chính. Vui lòng thử lại sau.');
                }
            }

            function populateProvinces() {
                // console.log('Populating provinces...', administrativeData); // Kiểm tra dữ liệu

                // Xóa tất cả options hiện có
                provinceSelect.innerHTML = '';

                // Thêm option mặc định
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = '-- Chọn Tỉnh/Thành phố --';
                provinceSelect.appendChild(defaultOption);

                // Kiểm tra xem administrativeData có phải là mảng không
                if (!Array.isArray(administrativeData)) {
                    console.error('Administrative data is not an array:', administrativeData);
                    return;
                }

                // Thêm các tỉnh thành vào dropdown
                administrativeData.forEach(province => {
                    const option = document.createElement('option');
                    option.value = province.code;
                    option.textContent = province.name;
                    provinceSelect.appendChild(option);
                });

                // console.log('Provinces populated:', provinceSelect.options.length); // Kiểm tra số lượng options

                // Kích hoạt lại nếu có giá trị cũ
                const oldProvinceCode = '{{ old('province') }}';
                if (oldProvinceCode) {
                    provinceSelect.value = oldProvinceCode;
                    populateDistricts(oldProvinceCode, '{{ old('district') }}');
                }
            }

            // Hàm điền dữ liệu cho dropdown Quận/Huyện dựa trên mã tỉnh
            // data.json của bạn chỉ có 'districts' bên trong mỗi tỉnh
            function populateDistricts(provinceCode, oldDistrictCode = null) {
                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                wardSelect.innerHTML =
                    '<option value="">-- Chọn Phường/Xã --</option>'; // Xóa phường/xã khi đổi quận/huyện
                districtSelect.disabled = true; // Ban đầu tắt
                wardSelect.disabled = true; // Ban đầu tắt

                const selectedProvince = administrativeData.find(p => p.code === Number(provinceCode));

                if (selectedProvince && selectedProvince.districts) {
                    selectedProvince.districts.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.code;
                        option.textContent = district.name;
                        districtSelect.appendChild(option);
                    });
                    districtSelect.disabled = false; // Bật nếu có dữ liệu
                }

                if (oldDistrictCode) {
                    districtSelect.value = oldDistrictCode;
                    populateWards(oldDistrictCode, '{{ old('ward') }}');
                }
            }

            // Hàm điền dữ liệu cho dropdown Phường/Xã dựa trên mã quận
            // data.json của bạn có 'wards' bên trong mỗi district
            function populateWards(districtCode, oldWardCode = null) {
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                wardSelect.disabled = true; // Ban đầu tắt

                // Cần tìm tỉnh trước để truy cập vào districts, rồi mới tìm district để truy cập wards
                // Cách tối ưu hơn là duyệt qua toàn bộ administrativeData để tìm district có code tương ứng
                let selectedDistrict = null;
                for (const province of administrativeData) {
                    selectedDistrict = province.districts.find(d => d.code === Number(districtCode));
                    if (selectedDistrict) break;
                }

                if (selectedDistrict && selectedDistrict.wards) {
                    selectedDistrict.wards.forEach(ward => {
                        const option = document.createElement('option');
                        option.value = ward.code;
                        option.textContent = ward.name;
                        wardSelect.appendChild(option);
                    });
                    wardSelect.disabled = false; // Bật nếu có dữ liệu
                }

                if (oldWardCode) {
                    wardSelect.value = oldWardCode;
                }
            }

            // Event listeners cho các dropdown
            provinceSelect.addEventListener('change', function() {
                const selectedProvinceCode = this.value;
                if (selectedProvinceCode) {
                    populateDistricts(selectedProvinceCode);
                } else {
                    districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                    districtSelect.disabled = true;
                    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                    wardSelect.disabled = true;
                }
                updateFullAddress();
            });

            districtSelect.addEventListener('change', function() {
                const selectedDistrictCode = this.value;
                if (selectedDistrictCode) {
                    populateWards(selectedDistrictCode);
                } else {
                    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                    wardSelect.disabled = true;
                }
                updateFullAddress();
            });

            wardSelect.addEventListener('change', updateFullAddress);
            streetAddressInput.addEventListener('input', updateFullAddress);


            // Function to combine address parts into a single string for submission
            function updateFullAddress() {
                const street = streetAddressInput.value.trim();
                const wardName = wardSelect.options[wardSelect.selectedIndex]?.textContent;
                const districtName = districtSelect.options[districtSelect.selectedIndex]?.textContent;
                const provinceName = provinceSelect.options[provinceSelect.selectedIndex]?.textContent;

                let fullAddressParts = [];
                if (street) fullAddressParts.push(street);
                if (wardName && wardName !== '-- Chọn Phường/Xã --') fullAddressParts.push(wardName);
                if (districtName && districtName !== '-- Chọn Quận/Huyện --') fullAddressParts.push(districtName);
                if (provinceName && provinceName !== '-- Chọn Tỉnh/Thành phố --') fullAddressParts.push(
                    provinceName);

                fullAddressInput.value = fullAddressParts.join(', ');
            }


            // Initial fetch for provinces when the page loads
            // Gọi hàm tải dữ liệu khi trang được nạp
            loadAdministrativeData();


            // Tạo các phần tử hiển thị lỗi
            let emailError = document.createElement('small');
            emailError.classList.add('text-danger', 'email-error');
            emailInput.parentNode.appendChild(emailError);

            let phoneError = document.createElement('small');
            phoneError.classList.add('text-danger', 'phone-error');
            phoneInput.parentNode.appendChild(phoneError);

            // Hàm validate email
            function validateEmail() {
                const email = emailInput.value;
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Regex cơ bản cho email
                if (!emailPattern.test(email)) {
                    emailError.textContent = 'Email không hợp lệ. Vui lòng nhập đúng định dạng email.';
                    emailInput.classList.add('is-invalid'); // Thêm class để đổi màu border đỏ
                    return false;
                } else {
                    emailError.textContent = '';
                    emailInput.classList.remove('is-invalid');
                    return true;
                }
            }

            // Hàm validate số điện thoại (existing code)
            function validatePhone() {
                const phone = phoneInput.value;
                // Regex cho số điện thoại Việt Nam (10 hoặc 11 số, bắt đầu bằng 0)
                const phonePattern = /^(0|\+84)[3|5|7|8|9][0-9]{8,9}$/;
                if (!phonePattern.test(phone)) {
                    phoneError.textContent =
                        'Số điện thoại không hợp lệ. Vui lòng nhập số điện thoại Việt Nam (10 hoặc 11 số).';
                    phoneInput.classList.add('is-invalid');
                    return false;
                } else {
                    phoneError.textContent = '';
                    phoneInput.classList.remove('is-invalid');
                    return true;
                }
            }
            emailInput.addEventListener('input', validateEmail);
            phoneInput.addEventListener('input', validatePhone);

            if (totalAmount > 2000000) {
                const codRadio = document.getElementById('COD');
                const vnpayRadio = document.getElementById('vnpay');
                const codLabel = codRadio.closest('label');

                // Disable COD radio button
                codRadio.disabled = true;

                // Nếu COD đang được chọn thì chuyển sang VNPAY
                if (codRadio.checked) {
                    codRadio.checked = false;
                    vnpayRadio.checked = true;
                }

                // Thêm style để hiển thị rõ là disabled
                codLabel.style.opacity = '0.5';
                codLabel.style.cursor = 'not-allowed';

                // Thêm ghi chú cảnh báo
                const warningSpan = document.createElement('span');
                warningSpan.style.color = 'red';
                warningSpan.style.fontSize = '12px';
                warningSpan.style.display = 'block';
                warningSpan.style.marginTop = '5px';
                warningSpan.textContent = 'Không áp dụng COD cho đơn hàng > 2 triệu đồng';
                codLabel.appendChild(warningSpan);

                // Ngăn người dùng click vào COD
                codLabel.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('Đơn hàng trên 2 triệu đồng không thể thanh toán COD!');
                });
            }
        });

        document.getElementById('checkout-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Ngăn chặn form submit mặc định

            const isEmailValid = validateEmail(); // Call validation functions
            const isPhoneValid = validatePhone();

            // Ensure all required fields are filled, including the new address fields
            const provinceSelect = document.getElementById('province-select');
            const districtSelect = document.getElementById('district-select');
            const wardSelect = document.getElementById('ward-select');
            const streetAddressInput = document.querySelector('input[name="street_address"]');
            const receiverNameInput = document.querySelector('input[name="receiver_name"]');

            if (!receiverNameInput.value.trim()) {
                alert('Vui lòng nhập tên người nhận.');
                return;
            }

            if (!provinceSelect.value) {
                alert('Vui lòng chọn Tỉnh/Thành phố.');
                return;
            }
            if (!districtSelect.value) {
                alert('Vui lòng chọn Quận/Huyện.');
                return;
            }
            if (!wardSelect.value) {
                alert('Vui lòng chọn Phường/Xã.');
                return;
            }
            if (!streetAddressInput.value.trim()) {
                alert('Vui lòng nhập số nhà, tên đường.');
                return;
            }

            if (!isEmailValid || !isPhoneValid) {
                alert('Vui lòng kiểm tra lại thông tin email và số điện thoại.');
                return;
            }

            // Combine address parts into a single hidden input for submission
            const fullAddressInput = document.getElementById('full-address-input');
            const street = streetAddressInput.value.trim();
            const wardName = wardSelect.options[wardSelect.selectedIndex]?.textContent;
            const districtName = districtSelect.options[districtSelect.selectedIndex]?.textContent;
            const provinceName = provinceSelect.options[provinceSelect.selectedIndex]?.textContent;
            fullAddressInput.value = `${street}, ${wardName}, ${districtName}, ${provinceName}`;


            // Kiểm tra lại tổng tiền trước khi submit (existing code)
            const totalAmount = {{ $total }};
            let paymentMethod = document.querySelector('input[name="payment"]:checked');

            if (!paymentMethod) {
                alert('Vui lòng chọn phương thức thanh toán!');
                return;
            }

            const paymentValue = paymentMethod.value;

            // Kiểm tra COD với đơn hàng > 2 triệu
            if (paymentValue === 'COD' && totalAmount > 2000000) {
                alert('Đơn hàng trên 2 triệu đồng không thể thanh toán COD!');
                return;
            }

            // Xử lý action theo phương thức thanh toán
            if (paymentValue === 'COD') {
                this.action = "{{ route('order.store') }}"; // Gửi đến OrderController
            } else if (paymentValue === 'momo') {
                let submitBtn = document.getElementById('checkout-form-submit-btn');
                if (submitBtn) {
                    submitBtn.name = "payUrl"; // Ensure the correct name for Momo redirection
                }
            } else if (paymentValue === 'zalopay') {
                let submitBtn = document.getElementById('checkout-form-submit-btn');
                if (submitBtn) {
                    submitBtn.name = "order_url"; // Ensure the correct name for ZaloPay redirection
                }
            } else if (paymentValue === 'vnpay') {
                let submitBtn = document.getElementById('checkout-form-submit-btn');
                if (submitBtn) {
                    submitBtn.name = "redirect"; // Ensure the correct name for VNPAY redirection
                }
            }
            this.submit();
        });

        // Hàm validateEmail và validatePhone cần được định nghĩa ở phạm vi toàn cục
        // để có thể được gọi từ event listener của form submit
        function validateEmail() {
            const emailInput = document.querySelector('input[name="email"]');
            const emailError = document.querySelector('.email-error');
            const email = emailInput.value;
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                emailError.textContent = 'Email không hợp lệ. Vui lòng nhập đúng định dạng email.';
                emailInput.classList.add('is-invalid');
                return false;
            } else {
                emailError.textContent = '';
                emailInput.classList.remove('is-invalid');
                return true;
            }
        }

        function validatePhone() {
            const phoneInput = document.querySelector('input[name="phone"]');
            const phoneError = document.querySelector('.phone-error');
            const phone = phoneInput.value;
            const phonePattern = /^(0|\+84)[3|5|7|8|9][0-9]{8,9}$/;
            if (!phonePattern.test(phone)) {
                phoneError.textContent =
                    'Số điện thoại không hợp lệ. Vui lòng nhập số điện thoại Việt Nam (10 hoặc 11 số).';
                phoneInput.classList.add('is-invalid');
                return false;
            } else {
                phoneError.textContent = '';
                phoneInput.classList.remove('is-invalid');
                return true;
            }
        }
    </script>
@endsection

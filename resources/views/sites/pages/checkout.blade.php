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
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <div class="checkout__input">
                                        <p>Tỉnh/Thành phố<span>*</span></p>
                                        <select name="province" id="province-select" required></select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="checkout__input">
                                        <p>Phường/Xã<span>*</span></p>
                                        <select name="ward" id="ward-select"  required disabled></select>
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
                                    $discountAmount = 0;
                                    if (Session::has('cart') && count(Session::get('cart')) > 0) {
                                        $cart = array_filter(Session::get('cart'), function ($item) {
                                            return !empty($item->checked) && $item->checked;
                                        });
                                        foreach ($cart as $items) {
                                            $itemTotal = $items->price * $items->quantity;
                                            $itemDiscount = $itemTotal * $percentDiscount;
                                            $discountAmount += $itemDiscount;
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
                                <input type="hidden" name="customer_id"
                                    value="{{ Auth::guard('customer')->check() ? Auth::guard('customer')->user()->id : '' }}">
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<style>
    .choices__inner {
        min-height: 44px;
        border-radius: 6px;
        padding: 8px 12px;
        background: #fff;
        font-size: 15px;
        border: 1px solid #ccc;
    }
    .choices__input {
        font-size: 15px;
    }
    .choices__list--dropdown .choices__item {
        padding: 8px 12px;
        font-size: 15px;
    }
    .choices__list--single {
        padding: 8px 12px;
        font-size: 15px;
    }
    .choices[data-type*='select-one'] .choices__inner {
        cursor: pointer;
    }
    .choices__list--dropdown {
        border-radius: 6px;
    }
    /* Icon tìm kiếm cho input search Choices.js */
    .choices__list--dropdown .choices__input {
        padding-left: 32px !important;
        background-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" fill="gray" height="18" width="18" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 14h-.79l-.28-.27c1.19-1.39 1.91-3.17 1.6-5.15C15.57 5.59 12.53 2.66 8.91 3.05A6.996 6.996 0 003.05 8.91c-.39 3.62 2.54 6.66 6.13 6.66 1.98 0 3.76-.72 5.15-1.91l.27.28v.79l5 4.99c.39.39.39 1.03 0 1.42-.39.39-1.03.39-1.42 0l-4.99-5zm-6.5 0C6.01 14 4 11.99 4 9.5S6.01 5 8.5 5 13 7.01 13 9.5 10.99 14 8.5 14z"></path></svg>');
        background-repeat: no-repeat;
        background-position: 8px center;
        background-size: 18px 18px;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
    let administrativeData = [];
    let choicesProvinces;
    let choicesWards;

    document.addEventListener('DOMContentLoaded', function() {
        choicesProvinces = new Choices('#province-select', {
            searchEnabled: true,
            itemSelectText: '',
            shouldSort: false,
            placeholder: true,
            placeholderValue: '-- Chọn Tỉnh/Thành phố --'
        });

        choicesWards = new Choices('#ward-select', {
            searchEnabled: true,
            itemSelectText: '',
            shouldSort: false,
            placeholder: true,
            placeholderValue: '-- Chọn Phường/Xã --'
        });

        // Thêm placeholder cho ô tìm kiếm khi mở dropdown
        choicesProvinces.passedElement.element.addEventListener('showDropdown', function() {
            setTimeout(function() {
                const searchInput = document.querySelector('.choices__input');
                if (searchInput) searchInput.placeholder = 'Tìm kiếm Tỉnh/Thành phố...';
            }, 10);
        });
        choicesWards.passedElement.element.addEventListener('showDropdown', function() {
            setTimeout(function() {
                const searchInput = document.querySelectorAll('.choices__input')[1];
                if (searchInput) searchInput.placeholder = 'Tìm kiếm Phường/Xã...';
            }, 10);
        });

        async function loadAdministrativeData() {
            try {
                const response = await fetch("{{ asset('storage/dataCountry/data.json') }}");
                administrativeData = await response.json();
                populateProvinces();
            } catch (error) {
                alert('Không thể tải dữ liệu địa giới hành chính.');
            }
        }

        function populateProvinces() {
            choicesProvinces.clearChoices();
            choicesProvinces.setChoices(
                administrativeData.map(p => ({
                    value: p.province_code,
                    label: p.name
                })),
                'value', 'label', false
            );
            choicesProvinces.setChoiceByValue('');
        }

        function populateWards(provinceCode, oldWardCode = null) {
            choicesWards.clearChoices();
            choicesWards.setChoices([{
                value: '',
                label: '-- Chọn Phường/Xã --',
                disabled: true,
                selected: true
            }], 'value', 'label', false);

            const selectedProvince = administrativeData.find(p => p.province_code.toString() === provinceCode);
            if (selectedProvince && Array.isArray(selectedProvince.wards)) {
                choicesWards.setChoices(
                    selectedProvince.wards.map(w => ({
                        value: w.ward_code,
                        label: w.name
                    })),
                    'value', 'label', false
                );
                choicesWards.enable();
            } else {
                choicesWards.disable();
            }
            if (oldWardCode) {
                choicesWards.setChoiceByValue(oldWardCode);
            }
        }

        document.getElementById('province-select').addEventListener('change', function() {
            populateWards(this.value);
            updateFullAddress();
        });

        document.getElementById('ward-select').addEventListener('change', updateFullAddress);
        document.querySelector('input[name="street_address"]').addEventListener('input', updateFullAddress);

        function updateFullAddress() {
            const street = document.querySelector('input[name="street_address"]').value.trim();
            const wardText = document.querySelector('#ward-select').selectedOptions[0]?.textContent || '';
            const provinceText = document.querySelector('#province-select').selectedOptions[0]?.textContent || '';
            let fullAddressParts = [];
            if (street) fullAddressParts.push(street);
            if (wardText && wardText !== '-- Chọn Phường/Xã --') fullAddressParts.push(wardText);
            if (provinceText && provinceText !== '-- Chọn Tỉnh/Thành phố --') fullAddressParts.push(provinceText);
            document.getElementById('full-address-input').value = fullAddressParts.join(', ');
        }

        loadAdministrativeData();

        // Validate email & phone (giữ nguyên code validate cũ)
        let emailInput = document.querySelector('input[name="email"]');
        let phoneInput = document.querySelector('input[name="phone"]');
        let emailError = document.createElement('small');
        emailError.classList.add('text-danger', 'email-error');
        emailInput.parentNode.appendChild(emailError);

        let phoneError = document.createElement('small');
        phoneError.classList.add('text-danger', 'phone-error');
        phoneInput.parentNode.appendChild(phoneError);

        function validateEmail() {
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
        emailInput.addEventListener('input', validateEmail);
        phoneInput.addEventListener('input', validatePhone);

        document.getElementById('checkout-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const isEmailValid = validateEmail();
            const isPhoneValid = validatePhone();

            const receiverNameInput = document.querySelector('input[name="receiver_name"]');
            if (!receiverNameInput.value.trim()) {
                alert('Vui lòng nhập tên người nhận.');
                return;
            }
            if (!choicesProvinces.getValue(true)) {
                alert('Vui lòng chọn Tỉnh/Thành phố.');
                return;
            }
            if (!choicesWards.getValue(true)) {
                alert('Vui lòng chọn Phường/Xã.');
                return;
            }
            if (!document.querySelector('input[name="street_address"]').value.trim()) {
                alert('Vui lòng nhập số nhà, tên đường.');
                return;
            }
            if (!isEmailValid || !isPhoneValid) {
                alert('Vui lòng kiểm tra lại thông tin email và số điện thoại.');
                return;
            }
            updateFullAddress();

            // Logic xử lý phương thức thanh toán giữ nguyên như cũ...
            const totalAmount = {{ $total }};
            let paymentMethod = document.querySelector('input[name="payment"]:checked');
            if (!paymentMethod) {
                alert('Vui lòng chọn phương thức thanh toán!');
                return;
            }
            const paymentValue = paymentMethod.value;
            if (paymentValue === 'COD' && totalAmount > 2000000) {
                alert('Đơn hàng trên 2 triệu đồng không thể thanh toán COD!');
                return;
            }
            if (paymentValue === 'COD') {
                this.action = "{{ route('order.store') }}";
            } else if (paymentValue === 'momo') {
                let submitBtn = document.getElementById('checkout-form-submit-btn');
                if (submitBtn) {
                    submitBtn.name = "payUrl";
                }
            } else if (paymentValue === 'zalopay') {
                let submitBtn = document.getElementById('checkout-form-submit-btn');
                if (submitBtn) {
                    submitBtn.name = "order_url";
                }
            } else if (paymentValue === 'vnpay') {
                let submitBtn = document.getElementById('checkout-form-submit-btn');
                if (submitBtn) {
                    submitBtn.name = "redirect";
                }
            }
            this.submit();
        });
    });
</script>
@endsection

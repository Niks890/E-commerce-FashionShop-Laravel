@extends('sites.master')
@section('title', 'Thanh toán')
@section('content')
    <!-- Breadcrumb Section Begin -->
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
    <!-- Breadcrumb Section End -->

    <!-- Checkout Section Begin -->
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
                                        <input type="text" name="receiver_name" required>
                                        @error('receiver_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="checkout__input">
                                <p>Địa chỉ nhận hàng<span>*</span></p>
                                <input type="text" placeholder="Street Address" class="checkout__input__add"
                                    name="address" required>
                            </div>
                            {{-- <div class="checkout__input">
                                <p>Địa chỉ nhận hàng<span class="text-danger">*</span></p>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="use-fixed-address">
                                    <label class="form-check-label" for="use-fixed-address">
                                        Giao tại cửa hàng (sử dụng địa chỉ cố định)
                                    </label>
                                </div>

                                <input type="text" placeholder="Nhập địa chỉ nhận hàng" class="form-control"
                                    name="address" id="address-input" required>

                                <input type="hidden" id="fixed-address" value="123 TFashion Shop, Quận 1, TP.HCM">
                            </div> --}}

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="checkout__input">
                                        <p>Số điện thoại<span>*</span></p>
                                        <input type="text" name="phone" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="checkout__input">
                                        <p>Email<span>*</span></p>
                                        <input type="text" name="email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="checkout__input">
                                <p>Ghi chú<span></span></p>
                                <input type="text" placeholder="Ghi chú cho đơn hàng (nếu có)" name="note" required>
                            </div>
                            <div class="checkout__input__checkbox">
                                <a href="{{ route('admin.login') }}">Tạo tài khoản mua hàng?</a>
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
                                <div class="checkout__order__products">Sản Phẩm<span>Đơn giá</span></div>
                                @php
                                    $index = 1;
                                    $totalPriceCart = 0;
                                    $vat = 0.1;
                                    $ship = 30000;
                                    $percentDiscount = Session::get('percent_discount', 0); // Lấy giá trị mặc định nếu không có

                                    if (Session::has('cart') && count(Session::get('cart')) > 0) {
                                        $cart = array_filter(Session::get('cart'), function ($item) {
                                            return !empty($item->checked) && $item->checked;
                                        }); // Lọc các sản phẩm được chọn (checked = true)

                                        foreach ($cart as $items) {
                                            $discountedPrice =
                                                $items->price * $items->quantity * (1 - $percentDiscount);
                                            $totalPriceCart += $discountedPrice;
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
                                                    <img src="{{$items->image}}" width="50"
                                                        alt="">
                                                    <h6>Số lượng: {{ $items->quantity }}</h6>
                                                    <h6>Size: {{ $items->size }}</h6>
                                                    <h6>Màu: {{ $items->color }}</h6>
                                                </li>
                                            </ul>
                                        @endif
                                    @endforeach
                                @endif
                                <ul class="checkout__total__all">
                                    <li>Tạm tính:<span>{{ number_format($totalPriceCart, 0, ',', '.') . ' đ' }}</span></li>
                                    <li>Thuế VAT (10%):<span>{{ number_format($vatPrice, 0, ',', '.') . ' đ' }}</span></li>
                                    <li>Phí Ship:<span>{{ number_format($ship, 0, ',', '.') . ' đ' }}</span></li>
                                    <li>Thành tiền:<span>{{ number_format($total, 0, ',', '.') . ' đ' }}</span></li>
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
                                <input type="submit" id="checkout-form" name="redirect" class="site-btn"
                                    value="ĐẶT HÀNG">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- Checkout Section End -->
@endsection

{{-- @section('css')
<style>
    #address-input[readonly] {
    background-color: #f8f9fa;
    color: #6c757d;
    font-style: italic;
}
</style>
@endsection --}}
@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const totalAmount = {{ $total }};

            // console.log('Total amount:', totalAmount); // Debug log

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

            // Kiểm tra lại tổng tiền trước khi submit
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
                let inputName = document.querySelector('input[name="redirect"]').value;
                if (inputName) {
                    this.inputName = "payUrl";
                }
            } else if (paymentValue === 'zalopay') {
                let inputName = document.querySelector('input[name="redirect"]');
                if (inputName) {
                    inputName.name = "order_url";
                }
            }

            this.submit(); // Gửi form
        });


    // document.addEventListener('DOMContentLoaded', function () {
    //     const checkbox = document.getElementById('use-fixed-address');
    //     const addressInput = document.getElementById('address-input');
    //     const fixedAddress = document.getElementById('fixed-address').value;

    //     checkbox.addEventListener('change', function () {
    //         if (this.checked) {
    //             addressInput.value = fixedAddress;
    //             addressInput.readOnly = true;
    //         } else {
    //             addressInput.value = '';
    //             addressInput.readOnly = false;
    //         }
    //     });
    // });


    </script>
@endsection

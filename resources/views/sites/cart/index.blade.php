{{-- @php
    dd(Session::get('cart'));
@endphp --}}
@if (Session::has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ Session::get('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
{{-- @extends('sites.master') --}}
@extends('sites.master', ['hideChatbox' => true])
@section('title', 'Giỏ Hàng')
@section('content')
    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Giỏ Hàng</h4>
                        <div class="breadcrumb__links">
                            <a href="{{ route('sites.home') }}">Home</a>
                            <a href="{{ route('sites.shop') }}">Shop</a>
                            <span>Giỏ hàng của bạn</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Shopping Cart Section Begin -->
    <section class="shopping-cart spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="shopping__cart__table">
                        <table>
                            <thead>
                                <tr>
                                    <th class="d-flex align-items-center">
                                        <input type="checkbox" id="check-all" class="mr-2"> All
                                    </th>
                                    <th class="text-center">Sản Phẩm</th>
                                    <th>Hình Ảnh</th>
                                    <th>Số Lượng</th>
                                    <th>Tổng tiền</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (Session::has('cart') && count(Session::get('cart')) > 0)
                                    @foreach (Session::get('cart') as $items)
                                        <tr class="cart-item" data-id="{{ $items->id }}"
                                            data-variant-id="{{ $items->product_variant_id }}">
                                            <td>
                                                <input type="checkbox" data-key="{{ $items->key }}"
                                                    class="product-checkbox" name="selected_products[]"
                                                    value="{{ $items->id }}"
                                                    {{ !empty($items->checked) && $items->checked ? 'checked' : '' }}>
                                            </td>
                                            <td class="product__cart__item">
                                                <a href="{{ route('sites.productDetail', $items->slug) }}">
                                                    <div class="product__cart__item__text">
                                                        <h6>{{ $items->name }}</h6>
                                                        <h5 class="product-price">
                                                            {{ number_format($items->price, 0, ',', '.') . ' đ' }}
                                                        </h5>
                                                        <h6 class="mt-1 color-variant" data-color="{{ $items->color }}">Màu
                                                            sắc: {{ $items->color }}</h6>
                                                        <h6 class="size-variant">Size: {{ $items->size }}</h6>
                                                    </div>
                                                </a>
                                            </td>
                                            <td class="product__cart__item">
                                                <a href="{{ route('sites.productDetail', $items->slug) }}">
                                                    <div class="product__cart__item__pic">
                                                        <img src="{{ $items->image }}" width="80" alt="">
                                                    </div>
                                                </a>
                                            </td>

                                            <td class="quantity__item">
                                                <div class="quantity">
                                                    <div class="input-group mt-3">
                                                        <button class="btn btn-outline-secondary button-decrease"
                                                            type="button">-</button>
                                                        <input type="text" class="text-center product-quantity"
                                                            value="{{ $items->quantity }}" min="1"
                                                            max="{{ $items->available_stock }}" style="width: 30%"
                                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                                        <button class="btn btn-outline-secondary button-increase"
                                                            type="button">+</button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="cart__price">
                                                {{ number_format($items->price * $items->quantity, 0, ',', '.') . ' đ' }}
                                            </td>
                                            <td class="cart__close">
                                                <a href="{{ route('sites.remove', $items->key) }}"
                                                    onclick="return confirm('Bạn có chắc muốn xoá sản phẩm này khỏi giỏ hàng ?')">
                                                    <i class="fa fa-close"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center text-muted" style="font-size: 1.35rem;">Giỏ
                                            hàng đang trống!</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="continue__btn">
                                {{-- đi đến chỗ danh sách sản phẩm bằng id=product-list-home --}}
                                <a href="{{ route('sites.home') }}#product-list-home">Tiếp tục mua hàng</a>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="continue__btn update__btn">
                                <a href="{{ route('sites.clear') }}">Xoá giỏ hàng</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">


                    <div class="cart__discount">
                        <h6>Mã giảm giá</h6>
                        <form action="#">
                            <input type="text" id="code_discount_input" name="code_discount" placeholder="Mã code..."
                                required>
                            <button id="apply-code-discount" type="submit">Áp dụng</button>
                        </form>
                        <button type="button" id="show-vouchers-btn " class="show-vouchers-btn" data-toggle="modal"
                            data-target="#vouchersModal">
                            <i class="fas fa-gift"></i>
                            <span>Xem mã giảm giá của bạn</span>
                            <i class="fas fa-arrow-right btn-arrow"></i>
                        </button>
                        <span id="apply-code-discount-result"></span>
                    </div>

                    @php
                        // $totalPriceCart = collect($cart)->sum(fn($item) => $item->price * $item->quantity);
                        // $ship = $totalPriceCart >= 500000 ? 0 : 30000;
                        if (Session::has('cart') && count(Session::get('cart')) > 0) {
                            $totalPriceCart = 0;
                            $vat = 0.1;
                            $ship = 30000;
                            $massage = '';
                            foreach (Session::get('cart') as $items) {
                                $totalPriceCart += $items->price * $items->quantity;
                            }
                            // Trên 500k free ship
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
                    <div class="cart__total">
                        <h6 class="text-center">Tổng giá trị giỏ hàng</h6>
                        <ul>
                            <li>Tạm tính:
                                <span>{{ number_format($totalPriceCart, 0, ',', '.') . ' đ' }}</span>
                                <p class="percent-discount d-none text-success"></p>
                                <input type="hidden" class="percent-discount-hidden" name="discont" value="0">
                            </li>
                            <li>Thuế VAT(10%):<span>{{ number_format($vatPrice, 0, ',', '.') . ' đ' }}</span></li>
                            <li>Phí Ship:<span>{{ number_format($ship, 0, ',', '.') . ' đ' }}</span></li>
                            <li>Thành tiền:<span>{{ number_format($total, 0, ',', '.') . ' đ' }}</span></li>
                        </ul>

                        <a href="{{ route('sites.checkout') }}" id="checkout-form" class="primary-btn">Thanh Toán</a>
                    </div>
                    <div class="mt-3">
                        <strong>Ưu đãi khi mua hàng tại TFashionShop: </strong>
                        <p>Miễn phí giao hàng áp dụng cho đơn hàng giao tận nơi từ 500K và tất cả các đơn nhận tại cửa hàng.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Shopping Cart Section End -->

    {{-- Cải thiện Modal hiển thị mã giảm giá --}}
    <div class="modal fade enhanced-voucher-modal" id="vouchersModal" tabindex="-1" role="dialog"
        aria-labelledby="vouchersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modern-modal">
                <div class="modal-header gradient-header">
                    <div class="header-content">
                        <i class="fas fa-tags header-icon"></i>
                        <h5 class="modal-title" id="vouchersModalLabel">Mã giảm giá của bạn</h5>
                    </div>
                    <button type="button" class="close modern-close" data-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body modern-body">
                    <ul class="nav nav-tabs modern-tabs" id="vouchersTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active modern-tab-link" id="available-tab" data-toggle="tab"
                                href="#available" role="tab" aria-controls="available" aria-selected="true">
                                <i class="fas fa-check-circle tab-icon"></i>
                                Có thể sử dụng
                                {{-- <span class="tab-badge" id="available-count">0</span> --}}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link modern-tab-link" id="used-tab" data-toggle="tab" href="#used"
                                role="tab" aria-controls="used" aria-selected="false">
                                <i class="fas fa-history tab-icon"></i>
                                Đã sử dụng
                                {{-- <span class="tab-badge" id="used-count">0</span> --}}
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content modern-tab-content" id="vouchersTabContent">
                        <div class="tab-pane fade show active" id="available" role="tabpanel"
                            aria-labelledby="available-tab">
                            <div class="vouchers-container" id="available-vouchers-container">
                                <div class="loading-state">
                                    <div class="modern-spinner">
                                        <div class="spinner-ring"></div>
                                        <div class="spinner-ring"></div>
                                        <div class="spinner-ring"></div>
                                    </div>
                                    <p>Đang tải mã giảm giá...</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="used" role="tabpanel" aria-labelledby="used-tab">
                            <div class="vouchers-container" id="used-vouchers-container">
                                <div class="loading-state">
                                    <div class="modern-spinner">
                                        <div class="spinner-ring"></div>
                                        <div class="spinner-ring"></div>
                                        <div class="spinner-ring"></div>
                                    </div>
                                    <p>Đang tải lịch sử...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modern-footer">
                    <button type="button" class="btn btn-secondary modern-close-btn" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('client/css/voucher-modal.css') }}">
@endsection

@section('js')

    {{-- Xử lý load mã giảm giá --}}
    <script>
        $(document).ready(function() {
            // Khi modal được mở
            $('#vouchersModal').on('show.bs.modal', function() {
                if (!@json(Auth::guard('customer')->check())) {
                    checkLoginForVoucher();
                    $('#vouchersModal').modal('hide');
                    return;
                }

                loadAvailableVouchers();
                loadUsedVouchers();
            });

            // Hàm load voucher có thể sử dụng
            function loadAvailableVouchers() {
                $.ajax({
                    url: '/api/customer/available-vouchers',
                    type: 'GET',
                    success: function(response) {
                        let html = '';
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(voucher => {
                                html += `
                        <div class="col-md-6">
                            <div class="voucher-card" data-code="${voucher.vouchers_code}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="voucher-code">${voucher.vouchers_code}</span>
                                        <span class="voucher-status available">Có thể sử dụng</span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <span class="voucher-discount">Giảm ${Math.round(voucher.vouchers_percent_discount)}%</span>
                                    <span class="voucher-max-discount"> (Tối đa ${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(voucher.vouchers_max_discount)})</span>
                                </div>
                                <div class="voucher-min-order mt-1">Đơn tối thiểu ${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(voucher.vouchers_min_order_amount)}</div>
                                <div class="voucher-expiry mt-1">HSD: ${new Date(voucher.vouchers_end_date).toLocaleDateString('vi-VN')}</div>
                            </div>
                        </div>`;
                            });
                        } else {
                            html =
                                '<div class="col-12 text-center"><p>Bạn không có mã giảm giá nào khả dụng</p></div>';
                        }
                        $('#available-vouchers-container').html(html);

                        // Thêm sự kiện click cho voucher
                        $('.voucher-card[data-code]').click(function() {
                            const code = $(this).data('code');
                            $('#code_discount_input').val(code);
                            $('#apply-code-discount').click();
                            $('#vouchersModal').modal('hide');
                        });
                    },
                    error: function(error) {
                        console.error('Error loading available vouchers:', error);
                        $('#available-vouchers-container').html(
                            '<div class="col-12 text-center"><p>Có lỗi xảy ra khi tải mã giảm giá</p></div>'
                        );
                    }
                });
            }

            // Hàm load voucher đã sử dụng
            function loadUsedVouchers() {
                $.ajax({
                    url: '/api/customer/used-vouchers',
                    type: 'GET',
                    success: function(response) {
                        let html = '';
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(usage => {
                                const voucher = usage.voucher;
                                html += `
                        <div class="col-md-6">
                            <div class="voucher-card used">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="voucher-code">${voucher.vouchers_code}</span>
                                        <span class="voucher-status used">Đã sử dụng</span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <span class="voucher-discount">Giảm ${Math.round(voucher.vouchers_percent_discount)}%</span>
                                </div>
                                <div class="voucher-min-order mt-1">Đã sử dụng ngày ${new Date(usage.used_at).toLocaleDateString('vi-VN')}</div>
                            </div>
                        </div>`;
                            });
                        } else {
                            html =
                                '<div class="col-12 text-center"><p>Bạn chưa sử dụng mã giảm giá nào</p></div>';
                        }
                        $('#used-vouchers-container').html(html);
                    },
                    error: function(error) {
                        console.error('Error loading used vouchers:', error);
                        $('#used-vouchers-container').html(
                            '<div class="col-12 text-center"><p>Có lỗi xảy ra khi tải mã giảm giá đã sử dụng</p></div>'
                        );
                    }
                });
            }
        });
    </script>

    {{-- // Hàm xử lý Cập nhật tổng giá trị giỏ hàng --}}
    <script>
        function updateCartTotal(priceDiscount = 0) {
            let totalPriceCart = 0;
            let vat = 0.1;
            let ship = 30000;

            $(".cart-item").each(function() {
                let productPrice = parseInt($(this).find(".product-price").text().replace(/\D/g, ""));
                let quantity = parseInt($(this).find(".product-quantity").val());
                totalPriceCart += (productPrice * quantity) - (productPrice * quantity * priceDiscount);
            });
            if (totalPriceCart >= 500000) {
                ship = 0;
            }

            let vatPrice = totalPriceCart * vat;
            let total = totalPriceCart + vatPrice + ship;
            $(".cart__total li:nth-child(1) span:nth-child(1)").text(totalPriceCart.toLocaleString('vi-VN') + " đ");
            $(".cart__total li:nth-child(2) span").text(vatPrice.toLocaleString('vi-VN') + " đ");
            $(".cart__total li:nth-child(3) span").text(ship.toLocaleString('vi-VN') + " đ");
            $(".cart__total li:nth-child(4) span").text(total.toLocaleString('vi-VN') + " đ");
        }

        // Xử lý nút tăng giảm số lượng
        $(document).ready(function() {
            $(".button-increase, .button-decrease").click(function() {
                let row = $(this).closest("tr");
                let input = row.find(".product-quantity");
                let productId = row.data("id");
                let productPrice = parseInt(row.find(".product-price").text().replace(/\D/g, ""));

                // let productColor = row.find(".color-variant").text().split(" ")[2];
                let productColor = row.find(".color-variant").data('color').replace(' ', '');
                // console.log(productColor);

                let productSize = row.find(".size-variant").text().split(" ")[1];

                let currentQuantity = parseInt(input.val());
                let minValue = parseInt(input.attr("min")) || 1;
                let maxValue = parseInt(input.attr("max"));

                if ($(this).hasClass("button-increase") && currentQuantity < maxValue) {
                    currentQuantity++;
                } else if ($(this).hasClass("button-decrease") && currentQuantity > minValue) {
                    currentQuantity--;
                }

                input.val(currentQuantity);

                // Cập nhật tổng giá tiền
                let totalPrice = productPrice * currentQuantity;
                row.find(".cart__price").text(totalPrice.toLocaleString() + " đ");
                let pecentDiscount = parseFloat(document.querySelector(".percent-discount-hidden").value);
                // console.log(pecentDiscount);
                // Gọi AJAX để lưu session
                updateCartSession(productId, productColor, productSize, currentQuantity);
                // Cập nhật tổng giá trị giỏ hàng
                updateCartTotal();
            });
        });
        // Hàm xử lý Cập nhật session cart
        function updateCartSession(productId, color, size, quantity) {
            // console.log("Sending update request with:", {
            //     productId: productId,
            //     color: color,
            //     size: size,
            //     quantity: quantity
            // });

            $.ajax({
                url: "/cart/update-cart-session",
                method: "POST",
                data: {
                    product_id: productId,
                    color: color,
                    size: size,
                    quantity: quantity,
                    _token: $('meta[name="csrf-token"]').attr("content")
                },
                success: function(response) {
                    // console.log("Session updated:", response);
                    // console.log(response);
                },
                error: function(xhr) {
                    console.error("Lỗi khi cập nhật session:", xhr.responseText);
                }
            });
        }
    </script>

    {{-- Qty validate --}}
    <script>
        $(document).ready(function() {
            $('.product-quantity').change(function(e) {
                let row = $(this).closest("tr");
                let input = row.find(".product-quantity");
                let productId = row.data("id");
                let productPrice = parseInt(row.find(".product-price").text().replace(/\D/g, ""));
                // let productColor = row.find(".color-variant").text().split(" ")[2];
                let productColor = row.find(".color-variant").data('color').replace(' ', '');
                let productSize = row.find(".size-variant").text().split(" ")[1];

                let currentQuantity = parseInt(input.val());
                let minValue = parseInt(input.attr("min")) || 1;
                let maxValue = parseInt(input.attr("max"));

                if (currentQuantity >
                    maxValue) { // maxValue => số lượng còn lại trong kho
                    input.val(maxValue); // số phông bạt
                    currentQuantity = maxValue;
                    alert("Số lượng không thể vượt quá số lượng trong kho " + maxValue);
                } else if (currentQuantity < minValue) {
                    input.val(1);
                    currentQuantity = 1;
                    alert("Số lượng không thể là số âm!");
                }
                // console.log(currentQuantity);
                let totalPrice = productPrice * currentQuantity;
                row.find(".cart__price").text(totalPrice.toLocaleString() + " đ");
                updateCartSession(productId, productColor, productSize, currentQuantity);
                updateCartTotal();
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.product-quantity').change(function(e) {
                let row = $(this).closest("tr");
                let input = row.find(".product-quantity");
                let productId = row.data("id");
                let productPrice = parseInt(row.find(".product-price").text().replace(/\D/g, ""));
                let currentQuantity = parseInt(input.val());
                let minValue = parseInt(input.attr("min")) || 1;
                let maxValue = parseInt(input.attr("max")) || 10;

                if (currentQuantity > maxValue) { // maxValue => số lượng còn lại trong kho
                    input.val(maxValue); // số phông bạt
                    currentQuantity = maxValue;
                    alert("Số lượng không thể vượt quá số lượng trong kho!" + maxValue);
                } else if (currentQuantity < minValue) {
                    input.val(1);
                    currentQuantity = 1;
                    alert("Số lượng không thể là số âm!");
                }
                // console.log(currentQuantity);
                let totalPrice = productPrice * currentQuantity;
                row.find(".cart__price").text(totalPrice.toLocaleString() + " đ");
                updateCartSession(productId, currentQuantity);
                updateCartTotal();
            });
        });
    </script>
    {{-- Qty validate --}}

    {{-- discount --}}

    <script>
        $(document).ready(function() {
            $('#apply-code-discount').click(function(e) {
                e.preventDefault();

                // Kiểm tra đăng nhập trước khi áp dụng voucher
                if (!@json(Auth::guard('customer')->check())) {
                    checkLoginForVoucher();
                    return;
                }
                var code = $('input[name="code_discount"]').val();
                var customerId = @json(Auth::guard('customer')->id() ?? null);

                $.ajax({
                    url: `/api/vouchers/check/${code}`,
                    type: "GET",
                    dataType: "json",
                    data: {
                        customer_id: customerId
                    },
                    success: function(response) {


                        if (response.status_code === 200) {
                            let voucher = response.data;
                            console.log(voucher);
                            const now = new Date();
                            const endDate = new Date(voucher.end_date);



                            // Check voucher validity
                            if (now > endDate) {
                                showVoucherError('Mã khuyến mãi hết hạn sử dụng.');
                                return;
                            }

                            // Check usage limit
                            if (voucher.usage_count >= voucher.usage_limit) {
                                showVoucherError('Mã khuyến mãi đã hết lượt sử dụng.');
                                return;
                            }

                            // Check if customer has already used this voucher
                            if (voucher.already_used) {
                                showVoucherError('Bạn đã sử dụng mã khuyến mãi này trước đây.');
                                return;
                            }

                            // Check minimum order amount
                            let totalPrice = getCartTotalPrice();
                            if (totalPrice < voucher.min_order_amount) {
                                showVoucherError(
                                    `Đơn hàng tối thiểu ${formatCurrency(voucher.min_order_amount)} để áp dụng mã này.`
                                );
                                return;
                            }

                            // All checks passed - apply voucher
                            applyVoucherSuccess(voucher);
                        } else {
                            showVoucherError('Mã khuyến mãi không hợp lệ!');
                        }
                    },
                    error: function(error) {
                        showVoucherError('Lỗi khi kiểm tra mã khuyến mãi.');
                    }
                });
            });
        });

        // Thêm hàm mới để xử lý đăng nhập cho voucher
        function checkLoginForVoucher() {
            let currentUrl = window.location.href;

            $.ajax({
                url: '/user/check-login',
                type: "POST",
                data: {
                    auth: "false",
                    redirect_url: currentUrl,
                    action: 'apply_voucher', // Thêm action để phân biệt
                    _token: $('meta[name="csrf-token"]').attr("content")
                },
                success: function(response) {
                    window.location.href = '/user/login';
                },
                error: function(error) {
                    console.log("Lỗi khi lưu session", error);
                }
            });
        }

        function showVoucherError(message) {
            $('#apply-code-discount-result').text(message);
            $('#apply-code-discount-result').removeClass('text-success');
            $('#apply-code-discount-result').addClass('text-danger');
        }

        function applyVoucherSuccess(voucher) {
            $('input[name="code_discount"]').attr('disabled', true);
            $('#apply-code-discount-result').text('Mã khuyến mãi hợp lệ.');
            $('#apply-code-discount-result').removeClass('text-danger');
            $('#apply-code-discount-result').addClass('text-success');

            // Parse percent discount từ string sang float và chia 100
            let percentDiscount = parseFloat(voucher.vouchers_percent_discount) / 100;

            // Calculate discount amount
            let totalPrice = getCartTotalPrice();
            let discountAmount = totalPrice * percentDiscount;

            // Áp dụng giới hạn tối đa nếu có
            if (voucher.vouchers_max_discount && discountAmount > parseFloat(voucher.vouchers_max_discount)) {
                discountAmount = parseFloat(voucher.vouchers_max_discount);
            }

            // Update UI
            updateCartTotalWithDiscount(discountAmount);
            $('.percent-discount').removeClass('d-none').addClass('d-inline')
                .text(`(-${percentDiscount * 100}%, tối đa ${formatCurrency(voucher.vouchers_max_discount)})`);
            $('.percent-discount-hidden').val(percentDiscount);

            updateVoucherSession(voucher.id, percentDiscount)
                .then(response => {
                    console.log('Voucher session saved successfully');
                })
                .catch(error => {
                    console.error('Failed to save voucher session:', error);
                    // Có thể hiển thị thông báo lỗi cho user
                });
        }

        function updateVoucherSession(voucherId, percentDiscount) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: "/cart/create-percent-discount-session",
                    method: "POST",
                    data: {
                        percent_discount: percentDiscount,
                        voucher_id: voucherId,
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function(response) {
                        console.log("Voucher session updated:", response);
                        if (response.success) {
                            resolve(response);
                        } else {
                            reject(response.message || 'Lỗi khi lưu voucher session');
                        }
                    },
                    error: function(xhr) {
                        console.error("Lỗi khi cập nhật voucher session:", xhr.responseText);
                        reject(xhr.responseText);
                    }
                });
            });
        }

        function getCartTotalPrice() {
            let total = 0;
            $(".cart-item").each(function() {
                let price = parseInt($(this).find(".product-price").text().replace(/\D/g, ""));
                let quantity = parseInt($(this).find(".product-quantity").val());
                total += price * quantity;
            });
            return total;
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(amount);
        }

        function updateCartTotalWithDiscount(discountAmount) {
            let totalPriceCart = getCartTotalPrice() - discountAmount;
            let vat = 0.1;
            let ship = totalPriceCart >= 500000 ? 0 : 30000;
            let vatPrice = totalPriceCart * vat;
            let total = totalPriceCart + vatPrice + ship;

            $(".cart__total li:nth-child(1) span:nth-child(1)").text(formatCurrency(totalPriceCart));
            $(".cart__total li:nth-child(2) span").text(formatCurrency(vatPrice));
            $(".cart__total li:nth-child(3) span").text(formatCurrency(ship));
            $(".cart__total li:nth-child(4) span").text(formatCurrency(total));
        }
    </script>



    {{-- xử lý nút thanh toán --}}
    <script>
        $(document).ready(function() {
            // Khi bấm nút thanh toán, kiểm tra và lấy danh sách sản phẩm đã chọn
            $("#checkout-form").click(function(event) {
                let selectedItems = [];
                $(".product-checkbox:checked").each(function() {
                    selectedItems.push($(this).val());
                });
                if (selectedItems.length === 0) {
                    alert("Vui lòng chọn ít nhất một sản phẩm để thanh toán.");
                    event.preventDefault();
                    return;
                }
            });
        });
    </script>

    {{-- check mã giảm giá --}}
    <script>
        $(document).ready(function() {
            $('#checkout-form').click(function(e) {
                if (@json(Auth::guard('customer')->check())) {
                    let percentDiscount = $('.percent-discount-hidden').val();
                    let voucherId = @json(Session::get('voucher_id'));
                    console.log('voucher_id', voucherId);
                    updatePercentDiscountSession(percentDiscount, voucherId);
                    // console.log("Giá trị discount:", percentDiscount, voucherId);
                    // updatePercentDiscountSession(percentDiscount);
                } else {
                    e.preventDefault();
                    checkLogin();
                };
            });
        });

        function checkLogin() {
            let currentUrl = window.location.href;

            $.ajax({
                url: '/user/check-login',
                type: "POST",
                data: {
                    auth: "false",
                    redirect_url: currentUrl,
                    _token: $('meta[name="csrf-token"]').attr("content")
                },
                success: function(response) {
                    console.log("Session lưu thành công:", response);
                    window.location.href = '/user/login';
                },
                error: function(error) {
                    console.log("Lỗi khi lưu session", error);
                }
            });
        }


        function updatePercentDiscountSession(percent_discount = 0, voucher_id = null) {
            $.ajax({
                url: "/cart/create-percent-discount-session",
                method: "POST",
                data: {
                    percent_discount: percent_discount,
                    voucher_id: voucher_id,
                    _token: $('meta[name="csrf-token"]').attr("content")
                },
                success: function(response) {
                    console.log("Session updated:", response);
                },
                error: function(xhr) {
                    console.error("Lỗi khi cập nhật session:", xhr.responseText);
                }
            });
        }
    </script>

    {{-- cart total checkbox --}}
    <script>
        $(document).ready(function() {
            //Hàm prop() lấy giá trị element hoặc gán giá trị
            //Xử lý skien change khi chọn "Chọn tất cả" => Tất cả checkbox sản phẩm sẽ được chọn hoặc bỏ chọn
            $("#check-all").change(function() {
                $(".product-checkbox").prop("checked", $(this).prop("checked"));
            });
            //Xử lý nếu bỏ chọn một sản phẩm, bỏ chọn "Chọn tất cả"
            $(".product-checkbox").change(function() {
                if (!$(this).prop("checked")) {
                    $("#check-all").prop("checked", false); // Vô hiệu hoá 1 hoặc tất cả checkbox
                }
                // else {
                //     $("#check-all").prop("checked", true); // chọn tất cả nếu tất cả checkbox đc chọn
                // }
            });
        });
    </script>

    {{-- xử lý cart total --}}
    <script>
        $(document).ready(function() {
            function updateCartTotalForCheckbox() {
                let totalPrice = 0;
                const vat = 0.1;
                let ship = 30000;
                $(".product-checkbox:checked").each(function() {
                    let row = $(this).closest("tr");
                    let price = parseFloat(row.find(".product-price").text().replace(/\D/g, ""));
                    let quantity = parseInt(row.find(".product-quantity").val());
                    totalPrice += price * quantity;
                });
                if (totalPrice >= 500000) {
                    ship = 0;
                }
                let vatPrice = totalPrice * vat;
                let total = totalPrice + vatPrice + ship;
                $(".cart__total li:nth-child(1) span:nth-child(1)").text(totalPrice.toLocaleString('vi-VN') + " đ");
                $(".cart__total li:nth-child(2) span").text(vatPrice.toLocaleString('vi-VN') + " đ");
                $(".cart__total li:nth-child(3) span").text(ship.toLocaleString('vi-VN') + " đ");
                $(".cart__total li:nth-child(4) span").text(total.toLocaleString('vi-VN') + " đ");
            }
            // Xử lý chọn/bỏ chọn tất cả
            $("#check-all").on("change", function() {
                $(".product-checkbox").prop("checked", $(this).prop("checked"));
                updateCartTotalForCheckbox();
            });
            // Nếu bỏ chọn một sản phẩm, bỏ chọn "Chọn tất cả"
            $(".product-checkbox").on("change", function() {
                if (!$(this).prop("checked")) {
                    $("#check-all").prop("checked", false);
                } else if ($(".product-checkbox:checked").length === $(".product-checkbox").length) {
                    $("#check-all").prop("checked", true);
                }
                updateCartTotalForCheckbox();
            });

            updateCartTotalForCheckbox();
        });
    </script>

    {{-- checkbox --}}
    <script>
        $(document).ready(function() {
            $(".product-checkbox, #check-all").change(function() {
                let checkedItems = [];
                $(".product-checkbox:checked").each(function() {
                    let productKey = $(this).data("key");
                    checkedItems.push(productKey);
                });
                // console.log("Danh sách checked:", checkedItems);
                $.ajax({
                    url: "/cart/update-check-status",
                    method: "POST",
                    data: {
                        keys: checkedItems, // Gửi danh sách key
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function(response) {
                        // console.log("Cập nhật session thành công:", response);
                    },
                    error: function(xhr) {
                        console.error("Lỗi khi cập nhật session:", xhr.responseText);
                    }
                });
            });
            // Chọn tất cả
            $("#check-all").change(function() {
                $(".product-checkbox").prop("checked", $(this).prop("checked")).trigger("change");
            });
        });
    </script>


    {{-- xử lý live stock --}}
    <script>
        $(document).ready(function() {
            // Function to check stock before checkout
            function checkStockBeforeCheckout() {
                return new Promise((resolve) => {
                    let itemsToCheck = [];
                    let hasError = false;
                    let errorMessages = [];

                    // Get all cart items
                    $(".cart-item").each(function() {
                        let row = $(this);
                        let variantId = row.data("variant-id");
                        let quantity = parseInt(row.find(".product-quantity").val());
                        let productName = row.find(".product__cart__item__text h6").text();

                        itemsToCheck.push({
                            variantId: variantId,
                            quantity: quantity,
                            productName: productName,
                            row: row
                        });
                    });

                    if (itemsToCheck.length === 0) {
                        resolve({
                            success: false,
                            message: 'Giỏ hàng của bạn đang trống!'
                        });
                        return;
                    }

                    // Prepare variant IDs for API call
                    let variantIds = itemsToCheck.map(item => item.variantId);

                    $.ajax({
                        url: "/cart/check-stock",
                        method: "POST",
                        data: {
                            variant_ids: variantIds,
                            _token: $('meta[name="csrf-token"]').attr("content")
                        },
                        success: function(response) {
                            let stockUpdates = [];
                            let itemsToRemove = [];

                            // Check each item's stock
                            itemsToCheck.forEach(item => {
                                let availableStock = response[item.variantId];

                                if (availableStock === undefined) {
                                    hasError = true;
                                    errorMessages.push(
                                        `Sản phẩm "${item.productName}" không tồn tại trong kho.`
                                    );
                                    itemsToRemove.push(item.row);
                                } else if (availableStock < item.quantity) {
                                    hasError = true;

                                    if (availableStock === 0) {
                                        errorMessages.push(
                                            `Sản phẩm "${item.productName}" đã hết hàng.`
                                        );
                                        itemsToRemove.push(item.row);
                                    } else {
                                        errorMessages.push(
                                            `Sản phẩm "${item.productName}" chỉ còn ${availableStock} sản phẩm. Đã cập nhật số lượng.`
                                        );
                                        stockUpdates.push({
                                            row: item.row,
                                            newQuantity: availableStock,
                                            variantId: item.variantId
                                        });
                                    }
                                }
                            });

                            if (hasError) {
                                // Update quantities for items that still have stock
                                stockUpdates.forEach(update => {
                                    update.row.find(".product-quantity").val(update
                                        .newQuantity);
                                    updateCartItem(update.row, update.newQuantity);
                                });

                                // Remove items that are out of stock
                                itemsToRemove.forEach(row => {
                                    removeCartItem(row);
                                });

                                // Update cart total
                                updateCartTotal();

                                resolve({
                                    success: false,
                                    message: errorMessages.join('<br>') +
                                        '<br>Vui lòng kiểm tra lại giỏ hàng.'
                                });
                            } else {
                                resolve({
                                    success: true
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error("Lỗi khi kiểm tra tồn kho:", xhr.responseText);
                            resolve({
                                success: false,
                                message: 'Có lỗi xảy ra khi kiểm tra tồn kho. Vui lòng thử lại.'
                            });
                        }
                    });
                });
            }

            // Helper function to update cart item in session
            function updateCartItem(row, newQuantity) {
                let productId = row.data("id");
                let color = row.find(".color-variant").data("color").replace(" ", "");
                let size = row.find(".size-variant").text().split(" ")[1];

                $.ajax({
                    url: "/cart/update-cart-session",
                    method: "POST",
                    data: {
                        product_id: productId,
                        color: color,
                        size: size,
                        quantity: newQuantity,
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function(response) {
                        console.log("Đã cập nhật số lượng trong giỏ hàng");
                    },
                    error: function(xhr) {
                        console.error("Lỗi khi cập nhật giỏ hàng:", xhr.responseText);
                    }
                });
            }

            // Helper function to remove cart item
            function removeCartItem(row) {
                let productKey = row.find(".product-checkbox").data("key");

                $.ajax({
                    url: `/cart/remove/${productKey}`,
                    method: "GET",
                    success: function(response) {
                        row.remove();
                        console.log("Đã xóa sản phẩm hết hàng khỏi giỏ hàng");
                    },
                    error: function(xhr) {
                        console.error("Lỗi khi xóa sản phẩm:", xhr.responseText);
                    }
                });
            }

            // Modify checkout click handler
            // $("#checkout-form").click(function(e) {
            //     e.preventDefault();

            //     // First check if user is logged in
            //     if (!@json(Auth::guard('customer')->check())) {
            //         checkLogin();
            //         return;
            //     }

            //     // Check if any items are selected
            //     let selectedItems = [];
            //     $(".product-checkbox:checked").each(function() {
            //         selectedItems.push($(this).val());
            //     });

            //     if (selectedItems.length === 0) {
            //         alert("Vui lòng chọn ít nhất một sản phẩm để thanh toán.");
            //         return;
            //     }

            //     // Show loading state
            //     $(this).addClass("disabled").text("Đang kiểm tra...");

            //     // Check stock before proceeding
            //     checkStockBeforeCheckout().then(result => {
            //         if (result.success) {
            //             // Update discount session if needed
            //             let percentDiscount = $('.percent-discount-hidden').val();

            //             let voucherId = @json(Session::get('voucher_id'));
            //             updatePercentDiscountSession(percentDiscount, voucherId);
            //             // updatePercentDiscountSession(percentDiscount);

            //             // Proceed to checkout
            //             window.location.href = $(this).attr("href");
            //         } else {
            //             // Show error message and reload page to reflect changes
            //             alert(result.message);
            //             location.reload();
            //         }

            //         // Reset button state
            //         $(this).removeClass("disabled").text("Thanh Toán");
            //     });
            // });

            $("#checkout-form").click(function(e) {
                e.preventDefault();

                if (!@json(Auth::guard('customer')->check())) {
                    checkLogin();
                    return;
                }

                let selectedItems = [];
                $(".product-checkbox:checked").each(function() {
                    selectedItems.push($(this).val());
                });

                if (selectedItems.length === 0) {
                    alert("Vui lòng chọn ít nhất một sản phẩm để thanh toán.");
                    return;
                }

                $(this).addClass("disabled").text("Đang kiểm tra...");

                checkStockBeforeCheckout().then(result => {
                    if (result.success) {
                        // Chỉ cập nhật session nếu chưa có voucher_id trong session
                        let voucherId = @json(Session::get('voucher_id'));
                        if (!voucherId) {
                            let percentDiscount = $('.percent-discount-hidden').val();
                            return updatePercentDiscountSession(percentDiscount, null);
                        }
                        return Promise.resolve();
                    } else {
                        throw new Error(result.message);
                    }
                }).then(() => {
                    // Proceed to checkout
                    window.location.href = $(this).attr("href");
                }).catch(error => {
                    alert(error.message || error);
                    if (error.message) {
                        location.reload();
                    }
                }).finally(() => {
                    $(this).removeClass("disabled").text("Thanh Toán");
                });
            });
        });
    </script>
@endsection

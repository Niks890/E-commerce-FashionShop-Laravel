{{--
1. còn vấn đề lỗi khi thêm số lượng từ product_detail qua cart nó cứ cộng số lượng vô tư
2. Chỗ cart icon bị vấn đề là nếu chỉ còn 1 sản phẩm còn hàng vd 5 thì dù click 6 lần vẫn đc :))))
--}}

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
@extends('sites.master')
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
                                        <tr class="cart-item" data-id="{{ $items->id }}">
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
                                                            max="{{ $items->stock }}" style="width: 30%">
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
                            <input type="text" name="code_discount" placeholder="Mã code...">
                            <button id="apply-code-discount" type="submit">Áp dụng</button>
                        </form>
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
@endsection

@section('js')
    <script>
        // Hàm xử lý Cập nhật tổng giá trị giỏ hàng
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

    {{-- discount --}}
    <script>
        $(document).ready(function() {
            $('#apply-code-discount').click(function(e) {
                e.preventDefault();
                var code = $('input[name="code_discount"]').val();
                $.ajax({
                    url: `http://127.0.0.1:8000/api/discount-code/${code}`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.status_code === 200) {
                            let discount = response.data;
                            const now = new Date();
                            const endDate = new Date(discount.end_date);
                            // console.log(endDate);
                            if (now > endDate) {
                                $('#apply-code-discount-result').text(
                                    'Mã khuyến mãi hết hạn sử dụng.');
                                $('#apply-code-discount-result').addClass('text-danger');
                            } else {
                                $('input[name="code_discount"]').attr('disabled', true);
                                $('#apply-code-discount-result').text('Mã khuyến mãi hợp lệ.');
                                $('#apply-code-discount-result').removeClass('text-danger');
                                $('#apply-code-discount-result').addClass('text-success');
                                updateCartTotal(discount.percent_discount);
                                $('.percent-discount').removeClass('d-none');
                                $('.percent-discount').addClass('d-inline');
                                $('.percent-discount-hidden').val(discount.percent_discount);
                                $('.percent-discount').text("(-" + discount.percent_discount *
                                    100 + '%)');
                            }
                        } else {
                            $('#apply-code-discount-result').text(
                                'Mã khuyến mãi không hợp lệ!');
                            $('#apply-code-discount-result').addClass('text-danger');
                            // alert('Mã code không tồn tại!');
                        }
                    },
                    error: function(error) {
                        alert('Lỗi khi xử lý mã KM.');
                    }
                });
            });
        });
    </script>



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


    <script>
        $(document).ready(function() {
            $('#checkout-form').click(function(e) {
                if (@json(Auth::guard('customer')->check())) {
                    let percentDiscount = $('.percent-discount-hidden').val();
                    // console.log("Giá trị discount:", percentDiscount);
                    updatePercentDiscountSession(percentDiscount);
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


        function updatePercentDiscountSession(percent_discount = 0) {
            $.ajax({
                url: "/cart/create-percent-discount-session",
                method: "POST",
                data: {
                    percent_discount: percent_discount, // Đổi key từ discount -> percent_discount
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
@endsection

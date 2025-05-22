@php
    // Session::forget('product_recent');
    // dd(Session::get('product_recent'));

    // Xử lý tính tổng số lượng sản phẩm trong giỏ hàng
    $totalProduct = 0;
    if (Session::has('cart')) {
        foreach (Session::get('cart') as $item) {
            $totalProduct += $item->quantity;
        }
    } else {
        $totalProduct = 0;
    }
@endphp

@extends('sites.master')
@section('title', $productDetail->product_name)
@section('content')
    @if(Session::has('error'))
        <div class="shadow-lg p-3 rounded js-div-dissappear"
            style="width: 100%; max-width: 500px; margin: 1rem auto; display: flex; align-items: center;
                    background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; text-align: left;">
            <i class="fas fa-exclamation-circle bg-danger text-white rounded-circle p-2 me-3"></i>
            <span style="font-size: 1rem;">{{ Session::get('error') }}</span>
        </div>
    @endif

    <!-- Shop Details Section Begin -->
    <section class="shop-details">
        <div class="product__details__pic">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="product__details__breadcrumb">
                            <a href="{{ route('sites.home') }}">Home</a>
                            <a href="{{ route('sites.shop') }}">Shop</a>
                            <span>{{ $productDetail->product_name }}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-3">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                {{-- hình gốc của sản phẩm (list thumnail) --}}
                                <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">
                                    <div class="product__thumb__pic set-bg"
                                        data-setbg="{{ asset('uploads/' . $productDetail->image) }}">
                                    </div>
                                </a>
                            </li>

                            {{-- các hình của varians --}}
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">
                                    <div class="product__thumb__pic set-bg"
                                        data-setbg="{{ asset('uploads/' . $productDetail->image) }}">
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tabs-3" role="tab">
                                    <div class="product__thumb__pic set-bg"
                                        data-setbg="{{ asset('uploads/' . $productDetail->image) }}">
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tabs-4" role="tab">
                                    <div class="product__thumb__pic set-bg"
                                        data-setbg="{{ asset('uploads/' . $productDetail->image) }}">
                                        <i class="fa fa-play"></i>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-6 col-md-9">
                        <div class="tab-content">
                            {{-- hình gốc của sản phẩm (panel) --}}
                            <div class="tab-pane active" id="tabs-1" role="tabpanel">
                                <div class="product__details__pic__item">
                                    <img src="{{ asset('uploads/' . $productDetail->image) }}" alt="">
                                </div>
                            </div>
                            <div class="tab-pane" id="tabs-2" role="tabpanel">
                                <div class="product__details__pic__item">
                                    <img src="{{ 'client/img/shop-details/product-big-3.png' }}" alt="">
                                </div>
                            </div>
                            <div class="tab-pane" id="tabs-3" role="tabpanel">
                                <div class="product__details__pic__item">
                                    <img src="{{ 'client/img/shop-details/product-big.png' }}" alt="">
                                </div>
                            </div>
                            <div class="tab-pane" id="tabs-4" role="tabpanel">
                                <div class="product__details__pic__item">
                                    <img src="{{ 'client/img/shop-details/product-big-4.png' }}" alt="">
                                    <a href="https://www.youtube.com/watch?v=8PJ3_p7VqHw&list=RD8PJ3_p7VqHw&start_radio=1"
                                        class="video-popup"><i class="fa fa-play"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="product__details__content">
            <div class="container">
                <div class="row d-flex justify-content-center">

                    <div class="col-lg-8">
                        <form action="{{ route('sites.addFromDetail', $productDetail->id) }}" method="post">
                            @csrf
                            <div class="product__details__text">
                                <h4>{{ $productDetail->product_name }}</h4>
                                <div class="rating">
                                    @php
                                        $avgStar = $starAvg;
                                        $fullStars = floor($avgStar); // Số sao đầy
                                        $hasHalfStar = $avgStar - $fullStars >= 0.5; // Kiểm tra có nửa sao không
                                        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0); // Số sao rỗng còn lại

                                    @endphp



                                    {{-- Sao đầy --}}
                                    @for ($i = 0; $i < $fullStars; $i++)
                                        <i class="fa fa-star fw-bold" @style('color: #FFD700')></i>
                                    @endfor

                                    {{-- Sao nửa nếu có --}}
                                    @if ($hasHalfStar)
                                        <i class="fa fa-star-half-o fw-bold" @style('color: #FFD700')></i>
                                    @endif

                                    {{-- Sao rỗng --}}
                                    @for ($i = 0; $i < $emptyStars; $i++)
                                        <i class="fa fa-star-o text-dark"></i>
                                    @endfor
                                    <span> - {{ count($commentCustomers) }} Đánh Giá</span>
                                </div>

                                @php
                                    $priceDiscount = $productDetail->price;
                                    $hasDiscount =
                                        $productDetail->discount_id &&
                                        optional($productDetail->Discount)->percent_discount !== null;

                                    if ($hasDiscount) {
                                        $priceDiscount =
                                            $productDetail->price -
                                            $productDetail->price * $productDetail->Discount->percent_discount;
                                    }
                                @endphp

                                <h3>
                                    {{ number_format($priceDiscount) }}đ
                                    @if ($hasDiscount)
                                        <span id="price-discount-detail"
                                            style="text-decoration: line-through; color: gray;">
                                            {{ number_format($productDetail->price) }}đ
                                        </span>
                                    @endif
                                </h3>


                                <p>{{ $productDetail->short_description }}</p>
                                <div class="product__details__option">
                                    <div class="product__details__option__size">
                                        <span>Kích cỡ:</span>
                                        @foreach ($sizes as $size)
                                            <label for="size-{{ $size }}">{{ $size }}
                                                <input type="radio" name="size" id="size-{{ $size }}"
                                                    value="{{ $size }}" required>
                                                <input type="radio" name="size" value="" hidden checked>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="product__details__option__color">
                                        <span>Màu Sắc:</span>
                                        @foreach ($colors as $index => $color)
                                            <label class="color-box" style="background-color: {{ getColorHex($color) }};"
                                                for="color-{{ $index }}" title="{{ $color }}">
                                                <input type="radio" name="color" id="color-{{ $index }}"
                                                    class="color-choice-item" value="{{ $color }}" required>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <span class="stock-extist"></span>
                                </div>
                                <div class="product__details__cart__option">
                                    <div class="quantity">
                                        <div class="pro-qty">
                                            <input class="quantity-input" type="text" name="quantity" value="1"
                                                min="1" max="{{ $productDetail->stock }}">
                                        </div>
                                        @error('quantity')
                                            <script>
                                                alert(@json($message));
                                            </script>
                                        @enderror
                                    </div>
                                    <input type="submit" class="site-btn" name="add_to_cart" value="Thêm vào giỏ hàng">
                                </div>
                                <div class="product__details__btns__option">
                                    <a href="{{ route('sites.addToWishList', $productDetail->id) }}"><i
                                            class="fa fa-heart"></i>Thêm vào yêu thích</a>
                                    <a href="javascript:void(0);"><i class="fa fa-exchange"></i>So sánh giá</a>
                                </div>
                                <div class="product__details__last__option">
                                    <h5><span>Các phương thức thanh toán:</span></h5>
                                    {{-- <img src="{{ 'client/img/shop-details/details-payment.png' }}" alt=""> --}}
                                    <div class="payment-pic">
                                        <img src="{{ asset('client/img/checkout/cod.png') }}" width="50"
                                            alt="">
                                        <img src="{{ asset('client/img/checkout/vnpay.png') }}" width="50"
                                            alt="">
                                        <img src="{{ asset('client/img/checkout/momo.png') }}" width="50"
                                            alt="">
                                        <img src="{{ asset('client/img/checkout/image.png') }}" width="50"
                                            alt="">
                                    </div>
                                    <ul>
                                        <li><span>SKU: </span>{{ $productDetail->sku }}</li>
                                        <li><span>Danh mục: </span>{{ $productDetail->category->category_name }}</li>
                                        <li><span>Tag: </span>{{ str_replace(',', ', ', $productDetail->tags) }}</li>
                                    </ul>
                                </div>
                            </div>
                    </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="product__details__tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tabs-5" role="tab">Mô tả sản
                                        phẩm</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tabs-6" role="tab">Đánh giá của
                                        khách hàng({{ count($commentCustomers ?? []) }})</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tabs-5" role="tabpanel">
                                    <div class="product__details__tab__content">
                                        <p class="note">Mô tả ngắn: {{ $productDetail->short_description }}</p>
                                        <div class="product__details__tab__content__item">
                                            <h5>Mô tả sản phẩm</h5>
                                            <p>{{ $productDetail->description }}</p>
                                        </div>
                                        <div class="product__details__tab__content__item">
                                            <h5>Chất liệu được sử dụng</h5>
                                            <p>{{ $productDetail->material }}</p>
                                        </div>
                                    </div>
                                </div>
                                {{-- phần đánh giá nữa chỉnh giao diện sau --}}
                                <div class="tab-pane" id="tabs-6" role="tabpanel">
                                    <div class="product__details__tab__content">
                                        <!-- Danh sách bình luận -->
                                        <div class="comment-section">
                                            <h5 class="comment-title">Đánh giá sản phẩm
                                                ({{ count($commentCustomers ?? []) }})</h5>
                                            <hr>
                                            <ul id="review-list">
                                                @if ($commentCustomers != null)
                                                    @foreach ($commentCustomers as $commentCustomer)
                                                        <li>
                                                            <div class="comment-header">
                                                                <div class="comment-author-info">
                                                                    <strong
                                                                        class="comment-author">{{ $commentCustomer->customer_name ?? 'Ẩn danh' }}</strong>

                                                                    {{-- Hiển thị sao đánh giá --}}
                                                                    @php
                                                                        $star = $commentCustomer->star ?? 0;
                                                                    @endphp
                                                                    @for ($i = 0; $i < $star; $i++)
                                                                        ★
                                                                    @endfor
                                                                    @for ($i = $star; $i < 5; $i++)
                                                                        ☆
                                                                    @endfor

                                                                </div>
                                                                <span
                                                                    class="comment-date">{{ $commentCustomer->created_at ?? 'Không xác định' }}</span>
                                                            </div>
                                                            <div class="comment-content">
                                                                <p><strong>Sản Phẩm:</strong>
                                                                    {{ $commentCustomer->product_name ?? 'Không rõ' }}</p>
                                                                <p>
                                                                    <strong>Màu sắc:</strong>
                                                                    {{ $commentCustomer->color ?? 'Không xác định' }} |
                                                                    <strong>Size:</strong>
                                                                    {{ $commentCustomer->size ?? 'Không xác định' }}
                                                                </p>
                                                                <p>Nội dung:
                                                                    {{ $commentCustomer->content ?? 'Không có nội dung' }}
                                                                </p>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                @else
                                                    <li>
                                                        <div class="text-center text-muted">Sản phẩm chưa có đánh giá nào!
                                                        </div>
                                                    </li>
                                                @endif


                                            </ul>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.querySelectorAll('.color-choice-item').forEach(item => {
                item.addEventListener('change', async (e) => {
                    // Xóa viền tất cả label
                    document.querySelectorAll('.color-box').forEach(label => label.style.border = 'none');

                    // Thêm viền xanh cho label được chọn
                    let selectedLabel = document.querySelector(`label[for="${e.target.id}"]`);
                    if (selectedLabel) {
                        selectedLabel.style.border = '3px solid blue';
                    }

                    let selectedColor = e.target.value;
                    let productId = @json($productDetail->id);

                    try {
                        let response = await fetch(
                            `http://127.0.0.1:8000/api/product-variant-size/${selectedColor}/${productId}`
                        );
                        let data = await response.json();

                        if (data.status_code === 200) {
                            let availableVariants = data.data;
                            let availableSizes = availableVariants.map(variant => variant.size);

                            document.querySelectorAll('.product__details__option__size label').forEach(
                                label => {
                                    let input = label.querySelector('input[type="radio"]');
                                    input.checked = false;

                                    if (availableSizes.includes(input.value)) {
                                        input.disabled = false;
                                        label.style.textDecoration = "none";
                                        label.style.opacity = "1";
                                    } else {
                                        input.disabled = true;
                                        label.style.textDecoration = "line-through";
                                        label.style.opacity = "0.5";
                                    }
                                });

                            // Chỉ reset số lượng tồn kho nếu không có size hợp lệ
                            if (availableSizes.length === 0) {
                                resetQuantityAndCart(0);
                            } else {
                                document.querySelector(".stock-extist").innerText = "";
                            }
                        }
                    } catch (error) {
                        console.error("Lỗi khi fetch dữ liệu:", error);
                    }
                });
            });

            document.querySelectorAll('.product__details__option__size label').forEach(label => {
                label.addEventListener('click', (e) => {
                    let input = label.querySelector('input[type="radio"]');
                    if (input.disabled) {
                        e.preventDefault();
                        resetQuantityAndCart(0);
                    }
                });
            });

            document.querySelectorAll('.product__details__option__size input').forEach(sizeInput => {
                sizeInput.addEventListener('change', async (e) => {
                    let selectedColor = document.querySelector('.color-choice-item:checked')?.value;
                    let selectedSize = e.target.value;
                    let productId = @json($productDetail->id);

                    if (e.target.disabled) {
                        resetQuantityAndCart(0);
                        return;
                    }

                    if (!selectedColor || !selectedSize) return;

                    try {
                        let response = await fetch(
                            `http://127.0.0.1:8000/api/product-variant-selected/${selectedSize}/${selectedColor}/${productId}`
                        );
                        let data = await response.json();

                        if (data.status_code === 200 && data.data) {
                            let stock = data.data.stock;
                            updateStockUI(stock);
                        }
                    } catch (error) {
                        console.error("Lỗi khi lấy dữ liệu số lượng tồn kho:", error);
                    }
                });
            });

            function resetQuantityAndCart(stock = null) {
                let quantityInput = document.querySelector(".quantity-input");
                let quantityContainer = document.querySelector(".quantity");
                let proqty = document.querySelector(".pro-qty");
                let addToCartBtn = document.querySelector("input[name='add_to_cart']");
                let stockText = document.querySelector(".stock-extist");

                quantityInput.value = 1;
                quantityInput.max = 1;
                quantityInput.disabled = true;
                quantityContainer.style.opacity = "0.5";
                proqty.style.pointerEvents = "none";
                addToCartBtn.disabled = true;
                addToCartBtn.style.backgroundColor = "gray";

                if (stock !== null) {
                    stockText.innerText = `Còn lại: ${stock} sản phẩm`;
                } else {
                    stockText.innerText = "";
                }
            }

            function updateStockUI(stock) {
                let quantityInput = document.querySelector(".quantity-input");
                let quantityContainer = document.querySelector(".quantity");
                let proqty = document.querySelector(".pro-qty");
                let addToCartBtn = document.querySelector("input[name='add_to_cart']");
                let stockText = document.querySelector(".stock-extist");

                stockText.innerText = `Còn lại: ${stock} sản phẩm`;

                if (stock === 0) {
                    resetQuantityAndCart(0);
                    stockText.classList.remove("in-stock");
                    stockText.classList.add("out-of-stock");
                } else {
                    quantityInput.value = 1;
                    quantityInput.max = stock;
                    quantityInput.disabled = false;
                    quantityContainer.style.opacity = "1";
                    quantityContainer.style.backgroundColor = "white";
                    proqty.style.pointerEvents = "auto";
                    addToCartBtn.disabled = false;
                    addToCartBtn.style.backgroundColor = "#000000";
                    stockText.classList.remove("out-of-stock");
                    stockText.classList.add("in-stock");
                }
            }
        </script>
    </section>
    <!-- Shop Details Section End -->

    <!-- Icon giỏ hàng -->
    <div class="cart-icon" id="cartIcon" onclick="toggleCart()">
        <div class="icon-wrapper">
            <i class="fas fa-shopping-cart fa-lg"></i>
            <span class="cart-badge" id="cartCount">{{ $totalProduct }}</span>
        </div>
    </div>

    <!-- Danh sách sản phẩm trong giỏ -->
    <div class="cart-items shadow" id="cartItems">
        <div class="cart-header d-flex justify-content-between align-items-center mb-2">
            <strong class="text-white">🛒 Giỏ hàng của bạn</strong>
            <i class="fas fa-times text-white" style="cursor: pointer;" onclick="toggleCart()"></i>
        </div>
        <div id="cartList" class="cart-body">
            @if (Session::has('cart') && count(Session::get('cart')) > 0)
                @foreach (Session::get('cart') as $items)
                    <div class="cart-item d-flex align-items-center">
                        <img src="uploads/{{ $items->image }}" alt="{{ $items->name }}"
                            class="cart-item-image rounded">
                        <div class="cart-item-info flex-grow-1">
                            <div class="cart-item-name text-truncate">{{ Str::words($items->name, 6) }}</div>
                            <div class="cart-item-price text-muted">{{ number_format($items->price, 0, ',', '.') . ' đ' }}
                            </div>
                        </div>
                        <span class="cart-item-quantity badge bg-danger ms-2">
                            {{ $items->quantity }}
                        </span>
                    </div>
                @endforeach
            @else
                <p class="empty-cart">Chưa có sản phẩm nào.</p>
            @endif
        </div>
        <div class="cart-footer">
            <button class="btn btn-success" onclick="goToCartPage()">Đến trang Giỏ hàng</button>
        </div>
    </div>

    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>



    <!-- Related Section Begin -->
    <section class="related spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="related-title">Sản phẩm liên quan mà có thể bạn sẽ thích</h3>
                </div>
            </div>

            <div class="row" id="suggestion-list-product">
                {{-- ******** danh sách này nằm trong _chatbot_search.blade.php do bỏ đây mất chatbot và se ********** --}}
            </div>
        </div>
    </section>
    <!-- Related Section End -->
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}">
    <link rel="stylesheet" href="{{ asset('client/css/comment.css') }}">
    <link rel="stylesheet" href="{{ asset('client/css/cart-add.css') }}">
    <link rel="stylesheet" href="{{ asset('client/css/stock.css') }}">
@endsection
@section('js')
    @if (Session::has('success'))
        <script src="{{ asset('assets/js/message.js') }}"></script>
    @endif

            {{-- danh sách sản phẩm liên quan --}}
    <script>
        $(document).ready(function() {
              console.log("Loading product suggestions..."); //
            $("#suggestion-list-product").empty(); // Xóa dữ liệu cũ trước khi cập nhật mới

            $.ajax({
                url: "http://127.0.0.1:8000/api/suggest-content-based", // API lấy danh sách sản phẩm
                method: "GET",
                dataType: "json",
                success: function(data) {
                    if (data.length > 0) {
                        console.log(data);
                        data.forEach(function(item) {
                            let price = item.price;
                            let nameDiscount = "";
                            if (item.discount_id != null) {
                                price = item.price - (item.price * item.discount
                                    .percent_discount);
                                nameDiscount = item.discount.name;
                            } else {
                                nameDiscount = "New";
                            }

                            let totalStock = [];
                            let addCartOrNone = [];
                            item.product_variants.map((variant) => {
                                totalStock[variant.product_id] = 0;
                            })
                            item.product_variants.forEach((variant) => {
                                totalStock[variant.product_id] += variant.stock + 0;
                            });
                            item.product_variants.map((variant) => {
                                if (totalStock[variant.product_id] == 0) {
                                    addCartOrNone[variant.product_id] = false;
                                } else {
                                    addCartOrNone[variant.product_id] = true;
                                };
                            })

                            let productHTML = `
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="product__item" id="product-list-shop">
                                <div class="product__item__pic">
                                    <img class="set-bg" width="280" height="250"
                                    src="{{ asset('uploads/${item.image}') }}"
                                    alt="${item.product_name}">
                                    <span class="label name-discount-suggest" >${nameDiscount}</span></li>
                                    <ul class="product__hover">
                                        <li><a href="{{ url('add-to-wishlist') }}/${item.id}"><img src="{{ asset('client/img/icon/heart.png') }}"
                                            alt=""></a></li>
                                            <li><a href="javascript:void(0);"><img src="{{ asset('client/img/icon/compare.png') }}"
                                                alt=""><span>Compare</span></a></li>
                                                <li><a href="{{ url('product') }}/${item.slug}">
                                                    <img src="{{ asset('client/img/icon/search.png') }}"
                                                    alt=""></a>

                                                    </ul>
                                                    </div>

                                                    <div class="product__item__text">
                                                        <h6>${item.product_name}</h6>
                                                        ` +

                                (addCartOrNone[item.id] > 0 ?
                                    `<a href="javascript:void(0);" class="add-cart" data-id="${item.id}">+ Add To Cart</a>` :
                                    `<span class=" badge badge-warning">Hết hàng</span>`)

                                +
                                `
                                            <div class="rating">
                                                <i class="fa fa-star-o"></i>
                                                <i class="fa fa-star-o"></i>
                                                <i class="fa fa-star-o"></i>
                                                <i class="fa fa-star-o"></i>
                                                <i class="fa fa-star-o"></i>
                                                </div>
                                                <h5>${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price)}</h5>
                                                <div class="product__color__select">
                                                    <label for="pc-4">
                                                        <input type="radio" id="pc-4">
                                                        </label>
                                                <label class="active black" for="pc-5">
                                                    <input type="radio" id="pc-5">
                                                    </label>
                                                    <label class="grey" for="pc-6">
                                                        <input type="radio" id="pc-6">
                                                        </label>
                                                        </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            $(document).ready(function() {
                                $('.name-discount-suggest').each(function() {
                                    if ($(this).text().trim() !== "New") {
                                        $(this).addClass(
                                            'bg-danger text-white');
                                    }
                                });
                            });
                            $("#suggestion-list-product").append(productHTML);
                        });
                    } else {
                        $("#suggestion-list-product").html("<p>Không có sản phẩm nào.</p>");
                    }
                },
                error: function() {
                    console.error("Lỗi API khi tải danh sách sản phẩm.");
                    $("#suggestion-list-product").html("<p>Lỗi khi tải sản phẩm.</p>");
                }
            });
        });
    </script>
    <script src="{{ asset('client/js/cart-add.js') }}"></script>
@endsection




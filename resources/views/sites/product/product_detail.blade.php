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

    // Xử lý màu sắc(helper.php)
    // getColorHex($productDetail->color);
@endphp

@extends('sites.master')
@section('title', $productDetail->product_name)
@section('content')
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
        <i class="fas fa-id-card-alt"></i><span class="cart-badge" id="cartCount">{{ $totalProduct }}</span>
    </div>

    <!-- Danh sách sản phẩm trong giỏ -->
    <div class="cart-items" id="cartItems" style="display: none;">
        <strong>Các sản phẩm đã thêm:</strong>
        <div id="cartList">
            @if (Session::has('cart') && count(Session::get('cart')) > 0)
                @foreach (Session::get('cart') as $items)
                    <div class="cart-item p-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <img src="uploads/{{ $items->image }}" alt="{{ $items->name }}" class="cart-item-img"
                                    width="50">
                                <div class="d-inline-block flex-col">
                                    <span>{{ Str::words($items->name, 5) }}</span></br>
                                    <span
                                        class="font-weight-bold">{{ number_format($items->price, 0, ',', '.') . ' đ' }}</span>
                                </div>
                            </div>
                            <span
                                class="cart-item-quantity-{{ $items->id }} quantity-badge">{{ $items->quantity }}</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <div class="cart-footer">
            <button class="btn btn-success w-100" onclick="goToCartPage()">Đến trang Giỏ hàng</button>
        </div>
    </div>

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
    <link rel="stylesheet" href="{{ asset('client/css/comment.css') }}">
    <link rel="stylesheet" href="{{ asset('client/css/cart-add.css') }}">
    <link rel="stylesheet" href="{{ asset('client/css/stock.css') }}">
@endsection

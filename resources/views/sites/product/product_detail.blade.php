@php
    // dd($totalSale);
    // dd($productDetail->discount->name);
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

{{-- @extends('sites.master') --}}
@extends('sites.master', ['hideChatbox' => true])
@section('title', $productDetail->product_name)
@section('content')
    @if (Session::has('error'))
        <div class="alert alert-danger alert-dismissible fade show mx-auto mb-4" role="alert" style="max-width: 600px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3 fs-5"></i>
                <span>{{ Session::get('error') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Breadcrumb Section -->
    <div class="breadcrumb-wrapper bg-light py-3 mb-4">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('sites.home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sites.shop') }}">Cửa hàng</a></li>
                    <li class="breadcrumb-item active">{{ $productDetail->product_name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Product Details Section -->
    <section class="product-details py-5">
        <div class="container">
            <div class="row g-5">
                <!-- Product Images -->
                <div class="col-lg-6">
                    <div class="product-gallery">
                        {{-- Promotion Banner --}}
                        @if ($productDetail->discount_id && $productDetail->discount)
                            <div class="promotion-banner mb-4">
                                <div class="promotion-content">
                                    <div class="promotion-header">
                                        <i class="fas fa-bolt"></i>
                                        <span class="promotion-text">KHUYẾN MÃI ĐẶC BIỆT</span>
                                        <span
                                            class="discount-badge">-{{ $productDetail->discount->percent_discount * 100 }}%</span>
                                    </div>
                                    <div class="promotion-details">
                                        <div class="discount-name">
                                            <i class="fas fa-tag"></i>
                                            {{ $productDetail->discount->name }}
                                        </div>
                                        <div class="countdown-timer">
                                            <i class="fas fa-clock"></i>
                                            <span>Kết thúc sau: <span id="countdown-timer" class="fw-bold"></span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @php
                            $allImages = [];
                            if ($productDetail->image) {
                                $allImages[] = [
                                    'url' => $productDetail->image,
                                    'color' => 'default',
                                    'is_default' => true,
                                ];
                            }
                            foreach ($productDetail->ProductVariants as $variant) {
                                foreach ($variant->ImageVariants as $image) {
                                    $allImages[] = [
                                        'url' => $image->url,
                                        'color' => $variant->color,
                                        'is_default' => false,
                                    ];
                                }
                            }
                            // dd($allImages);
                        @endphp

                        <div class="gallery-container">
                            <!-- Main Image -->
                            <div class="main-image-container mb-3">
                                <div class="tab-content">
                                    @forelse ($allImages as $index => $image)
                                        <div class="tab-pane fade main-image-{{ $image['color'] }} {{ $index == 0 ? 'show active' : '' }}"
                                            id="main-image-{{ $index + 1 }}" role="tabpanel"
                                            data-color="{{ $image['color'] }}">
                                            <div class="main-image-wrapper">
                                                <img src="{{ $image['url'] }}" alt="Product Image {{ $index + 1 }}"
                                                    class="main-image">
                                            </div>
                                        </div>
                                    @empty
                                        <div class="tab-pane fade show active" id="main-image-default" role="tabpanel">
                                            <div class="main-image-wrapper">
                                                <img src="{{ asset('client/img/default-product.png') }}"
                                                    alt="Product Image" class="main-image">
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Thumbnails -->
                            <div class="thumbnails-container">
                                <div class="thumbnails-wrapper">
                                    @forelse ($allImages as $index => $image)
                                        <div class="thumbnail-item thumbnail-{{ $image['color'] }} {{ $index == 0 ? '' : 'd-none' }}"
                                            role="presentation">
                                            <button class="thumbnail-btn {{ $index == 0 ? 'active' : '' }}"
                                                data-bs-toggle="tab" data-bs-target="#main-image-{{ $index + 1 }}"
                                                type="button" role="tab">
                                                <img src="{{ $image['url'] }}" alt="Thumbnail {{ $index + 1 }}"
                                                    class="thumbnail-img">
                                            </button>
                                        </div>
                                    @empty
                                        <div class="thumbnail-item" role="presentation">
                                            <button class="thumbnail-btn active" data-bs-toggle="tab"
                                                data-bs-target="#main-image-default" type="button" role="tab">
                                                <img src="{{ asset('client/img/default-product.png') }}"
                                                    alt="Default Thumbnail" class="thumbnail-img">
                                            </button>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Information -->
                <div class="col-lg-6">
                    <div class="product-info">
                        <form id="add-to-cart-form" action="{{ route('sites.addFromDetail', $productDetail->id) }}"
                            method="post">
                            @csrf

                            <!-- Product Title & Rating -->
                            <div class="product-header mb-4">
                                <h1 class="product-title">{{ $productDetail->product_name }}</h1>

                                <div class="product-meta">
                                    <div class="rating-section">
                                        @php
                                            $avgStar = $starAvg;
                                            $fullStars = floor($avgStar);
                                            $hasHalfStar = $avgStar - $fullStars >= 0.5;
                                            $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                        @endphp
                                        <div class="stars">
                                            @for ($i = 0; $i < $fullStars; $i++)
                                                <i class="fas fa-star"></i>
                                            @endfor
                                            @if ($hasHalfStar)
                                                <i class="fas fa-star-half-alt"></i>
                                            @endif
                                            @for ($i = 0; $i < $emptyStars; $i++)
                                                <i class="far fa-star"></i>
                                            @endfor
                                        </div>
                                        <span class="rating-text">Điểm đánh giá:({{ floor($avgStar) }})</span>
                                        <span class="rating-text">({{ count($commentCustomers) }} đánh giá)</span>
                                    </div>



                                    <div class="sales-info d-flex">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Đã bán {{ $totalSale }} sản phẩm</span>
                                        <i class="fas fa-share text-dark" style="cursor: pointer; margin-left: 12px;" data-bs-toggle="modal"
                                            data-bs-target="#shareModal">Chia sẻ ngay </i>
                                    </div>
                                </div>
                            </div>

                            <!-- Price Section -->
                            <div class="price-section mb-4">
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

                                <div class="price-display">
                                    <span class="current-price">{{ number_format($priceDiscount) }}đ</span>
                                    @if ($hasDiscount)
                                        <span class="original-price">{{ number_format($productDetail->price) }}đ</span>
                                        <span
                                            class="discount-percent">-{{ $productDetail->Discount->percent_discount * 100 }}%</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Product Description -->
                            <div class="product-description mb-4">
                                <p>{{ $productDetail->short_description }}</p>
                            </div>

                            <!-- Product Options -->
                            <div class="product-options mb-4">
                                <!-- Size Selection -->
                                <div class="option-group mb-4">
                                    <label class="option-label">Kích cỡ:</label>
                                    <div class="size-options">
                                        @foreach ($sizes as $size)
                                            <div class="size-option">
                                                <input type="radio" name="size" id="size-{{ $size }}"
                                                    value="{{ $size }}" class="size-input">
                                                <label for="size-{{ $size }}"
                                                    class="size-label">{{ $size }}</label>
                                            </div>
                                        @endforeach
                                        <input type="radio" name="size" value="" hidden checked>
                                    </div>
                                </div>


                                <!-- Color Selection -->
                                <div class="option-group mb-4">
                                    <label class="option-label">Màu sắc:</label>
                                    <div class="color-options">
                                        @foreach ($colors as $index => $color)
                                            <div class="color-option">
                                                <input type="radio" name="color" id="color-{{ $index }}"
                                                    class="color-input" value="{{ $color }}">
                                                <label for="color-{{ $index }}" class="color-label"
                                                    style="background-color: {{ getColorHex($color) }};"
                                                    title="{{ $color }}">
                                                    <i class="fas fa-check"></i>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Stock Info -->
                            <div class="mb-3">
                                <span class="stock-extist"></span>
                            </div>

                            <!-- Quantity & Add to Cart -->
                            <div class="purchase-section mb-4">
                                <div class="quantity">
                                    <div class="pro-qty">
                                        <input class="quantity-input" type="number" name="quantity" value="1"
                                            min="1" max="{{ $productDetail->available_stock }}"
                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                    </div>
                                    @error('quantity')
                                        <script>
                                            alert(@json($message));
                                        </script>
                                    @enderror
                                </div>

                                <button type="submit" class="add-to-cart-btn" name="add_to_cart">
                                    <i class="fas fa-shopping-cart"></i>
                                    Thêm vào giỏ hàng
                                </button>
                            </div>

                            <!-- Action Buttons -->
                            <div class="action-buttons mb-4">
                                <a href="{{ route('sites.addToWishList', $productDetail->id) }}"
                                    class="action-btn wishlist-btn">
                                    <i class="fas fa-heart"></i>
                                    Thêm vào yêu thích
                                </a>
                                <a href="javascript:void(0);" class="action-btn compare-btn">
                                    <i class="fas fa-exchange-alt"></i>
                                    So sánh
                                </a>
                                <a href="https://res.cloudinary.com/dc2zvj1u4/image/upload/v1748404290/ao/file_u0eqqq.jpg"
                                    class="action-btn size-guide-btn">
                                    <i class="fas fa-ruler"></i>
                                    Hướng dẫn size
                                </a>
                            </div>

                            <!-- Payment & Product Info -->
                            <div class="product-details-info">
                                <div class="payment-methods mb-4">
                                    <h6 class="info-title">
                                        <i class="fas fa-credit-card"></i>
                                        Phương thức thanh toán
                                    </h6>
                                    <div class="payment-icons">
                                        <img src="{{ asset('client/img/checkout/cod.png') }}" alt="COD"
                                            class="payment-icon">
                                        <img src="{{ asset('client/img/checkout/vnpay.png') }}" alt="VNPay"
                                            class="payment-icon">
                                        <img src="{{ asset('client/img/checkout/momo.png') }}" alt="Momo"
                                            class="payment-icon">
                                        <img src="{{ asset('client/img/checkout/image.png') }}" alt="Credit Card"
                                            class="payment-icon">
                                    </div>
                                </div>

                                <div class="product-meta-info">
                                    <div class="meta-item">
                                        <span class="meta-label">SKU:</span>
                                        <span class="meta-value">{{ $productDetail->sku }}</span>
                                    </div>
                                    <div class="meta-item">
                                        <span class="meta-label">Danh mục:</span>
                                        <span class="meta-value">{{ $productDetail->category->category_name }}</span>
                                    </div>
                                    <div class="meta-item">
                                        <span class="meta-label">Tag:</span>
                                        <span class="meta-value">{{ str_replace(',', ', ', $productDetail->tags) }}</span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Product Tabs -->
            <div class="product-tabs mt-5">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#description"
                                role="tab">
                                <i class="fas fa-file-text"></i>
                                Mô tả sản phẩm
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews" role="tab">
                                <i class="fas fa-comments"></i>
                                Đánh giá sản phẩm ({{ count($commentCustomers ?? []) }})
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Description Tab -->
                        <div class="tab-pane fade show active" id="description" role="tabpanel">
                            <div class="description-content mt-2">
                                <div class="description-section">
                                    <h5>Mô tả ngắn</h5>
                                    <p>{{ $productDetail->short_description }}</p>
                                </div>
                                <hr class="w-100">
                                <div class="description-section">
                                    <h5>Mô tả chi tiết</h5>
                                    <p>{{ $productDetail->description }}</p>
                                </div>
                                <hr class="w-100">
                                <div class="description-section">
                                    <h5>Chất liệu</h5>
                                    <p>{{ $productDetail->material }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Reviews Tab -->
                        <div class="tab-pane fade" id="reviews" role="tabpanel">
                            <div class="reviews-content mt-2">
                                <div class="reviews-header">
                                    <h5>Đánh giá sản phẩm ({{ count($commentCustomers ?? []) }})</h5>

                                    <!-- Bộ lọc đánh giá -->
                                    <div class="review-filters mb-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="filter-group">
                                                    <label class="filter-label">Lọc theo sao:</label>
                                                    <div class="star-filter">
                                                        @for ($i = 5; $i >= 1; $i--)
                                                            <button class="star-filter-btn"
                                                                data-rating="{{ $i }}">
                                                                {{ $i }} <i class="fas fa-star"></i>
                                                                @if (isset($ratingCounts[$i]))
                                                                    <span class="count">({{ $ratingCounts[$i] }})</span>
                                                                @endif
                                                            </button>
                                                        @endfor
                                                        <button class="star-filter-btn active" data-rating="all">
                                                            Tất cả
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="filter-group">
                                                    <label class="filter-label">Lọc khác:</label>
                                                    <div class="other-filters">
                                                        <button class="filter-btn" data-filter="has_image">
                                                            <i class="fas fa-camera"></i> Có ảnh
                                                        </button>
                                                        <button class="filter-btn active" data-filter="all">
                                                            Tất cả
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if ($commentCustomers && count($commentCustomers) > 0)
                                    <div class="reviews-list">
                                        @foreach ($commentCustomers as $commentCustomer)
                                            <div class="review-item" data-rating="{{ $commentCustomer->star ?? 0 }}"
                                                data-has-image="{{ !empty($commentCustomer->image) ? 'true' : 'false' }}"
                                                data-has-text="{{ !empty($commentCustomer->content) ? 'true' : 'false' }}">
                                                <div class="review-header">
                                                    <div class="reviewer-info">
                                                        <div class="reviewer-name">
                                                            {{ $commentCustomer->customer_name ?? 'Ẩn danh' }}
                                                        </div>
                                                        <div class="review-rating">
                                                            @php $star = $commentCustomer->star ?? 0; @endphp
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if ($i <= $star)
                                                                    <i class="fas fa-star"></i>
                                                                @else
                                                                    <i class="far fa-star"></i>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                    </div>
                                                    <div class="review-date">
                                                        {{ $commentCustomer->created_at ?? 'Không xác định' }}
                                                    </div>
                                                </div>

                                                <div class="review-content">
                                                    <div class="product-variant-info">
                                                        <span class="variant-item">
                                                            <strong>Sản phẩm:</strong>
                                                            {{ $commentCustomer->product_name ?? 'Không rõ' }}
                                                        </span>
                                                        <span class="variant-item">
                                                            <strong>Màu:</strong>
                                                            {{ $commentCustomer->color ?? 'Không xác định' }}
                                                        </span>
                                                        <span class="variant-item">
                                                            <strong>Size:</strong>
                                                            {{ $commentCustomer->size ?? 'Không xác định' }}
                                                        </span>
                                                    </div>





                                                    {{-- <!-- Hiển thị ảnh đánh giá nếu có -->
                                                    @if (!empty($commentCustomer->image))
                                                        <div class="review-images mt-3">
                                                            <div class="row">
                                                                @foreach ($commentCustomer->image as $image)
                                                                    <div class="col-3 col-md-2">
                                                                        <a href="{{ $image }}"
                                                                            data-lightbox="review-images-{{ $commentCustomer->id }}">
                                                                            <img src="{{ $image }}"
                                                                                class="img-thumbnail" alt="Ảnh đánh giá">
                                                                        </a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif --}}

                                                    <p class="review-text mt-3">
                                                        Nội dung: {{ $commentCustomer->content ?? 'Không có nội dung' }}
                                                    </p>

                                                    @if ($commentCustomer->content)
                                                        <div class="seller-response">
                                                            <div class="response-header">
                                                                <i class="fas fa-reply"></i>
                                                                <strong>Phản hồi từ cửa hàng</strong>
                                                            </div>
                                                            <p>Cảm ơn bạn đã mua hàng tại cửa hàng chúng tôi!</p>
                                                            <p>Nếu có bất kỳ vấn đề gì, vui lòng liên hệ:
                                                                <a href="tel:0123456789">0123.456.789</a>
                                                                hoặc email <a
                                                                    href="mailto:TFashionShop@gmail.com">TFashionShop@gmail.com</a>
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="no-reviews">
                                        <i class="fas fa-comments fa-3x"></i>
                                        <p>Sản phẩm chưa có đánh giá nào!</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                        <img src="{{ $items->image }}" alt="{{ $items->name }}" class="cart-item-image rounded">
                        <div class="cart-item-info flex-grow-1">
                            <div class="cart-item-name text-truncate">Tên: {{ Str::words($items->name, 6) }}</div>
                            <div class="cart-item-color">Size-Màu: {{ $items->color }} - {{ $items->size }}</div>
                            <div class="cart-item-price text-muted">Giá:
                                {{ number_format($items->price, 0, ',', '.') . ' đ' }}
                            </div>
                        </div>
                        <span class="cart-item-quantity badge bg-danger ms-2">
                            Qty: {{ $items->quantity }}
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

    <hr class="w-50">
    <!-- Related Section Begin -->
    <section class="related spad p-2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="related-title">Sản phẩm có thể bạn sẽ thích</h3>
                </div>
            </div>

            <div class="row" id="suggestion-list-product">
                {{-- ******** danh sách này nằm trong _chatbot_search.blade.php do bỏ đây mất chatbot và se ********** --}}
            </div>
        </div>
    </section>
    <!-- Related Section End -->


    <hr class="w-50">
    <!-- Product Recently Section Begin -->
    @if (!empty($productRecentInfo) && count($productRecentInfo) > 0)
        <section class="product spad" id="product-recently-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h3 class="related-title font-weight-bold">Sản phẩm bạn đã xem gần đây</h3>
                    </div>
                </div>


                <div class="row product__filter_productRecentInfo" id="product-recently-container">
                    @foreach ($productRecentInfo as $itemRecent)
                        @php
                            // Xử lý khuyến mãi
                            $originalPrice = $itemRecent->price;
                            $discountName = '';
                            if ($itemRecent->discount_id && $itemRecent->discount_id !== null) {
                                $itemRecent->price =
                                    $itemRecent->price - $itemRecent->price * $itemRecent->Discount->percent_discount;
                                $discountName = $itemRecent->Discount->name;
                            } else {
                                $discountName = 'New';
                            }
                            $totalStock = 0;
                            if ($itemRecent->ProductVariants) {
                                // Kiểm tra nếu có productVariants
                                foreach ($itemRecent->ProductVariants as $variant) {
                                    if ($variant) {
                                        $totalStock += $variant->stock;
                                    }
                                }
                            }
                        @endphp
                        <div class="col-lg-3 col-md-6 col-sm-6 mix">
                            <div class="product__item">
                                <div class="product__item__pic">
                                    <img src="{{ $itemRecent->image }}" class="set-bg" width="280" height="280"
                                        alt="{{ $itemRecent->product_name }}">
                                    <span
                                        class="label name-discount bg-danger {{ $discountName == 'New' ? 'text-dark bg-white' : '' }}">
                                        {{ $discountName }}
                                    </span>

                                    <ul class="product__hover">
                                        <li>
                                            <a href="{{ url('add-to-wishlist/' . $itemRecent->id) }}"
                                                class="add-to-wishlist" title="Thêm vào danh sách yêu thích">
                                                <img src="{{ asset('client/img/icon/heart.png') }}" alt="">
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"><img
                                                    src="{{ asset('client/img/icon/compare.png') }}" alt="">
                                                <span>Compare</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ url('product/' . $itemRecent->slug) }}">
                                                <img src="{{ asset('client/img/icon/search.png') }}" alt="">
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="product__item__text">
                                    <h6>{{ $itemRecent->product_name }}</h6>
                                    @php
                                        if ($totalStock == 0) {
                                            echo '<span class=" badge badge-warning">Hết hàng</span>';
                                        } else {
                                            echo '<a href="javascript:void(0);" class="add-cart" data-id="' .
                                                $itemRecent->id .
                                                '">+Add To Cart</a>';
                                        }
                                    @endphp
                                    <div class="rating mt-2">
                                        @php
                                            $avgRating = $itemRecent->comments->avg('star') ?? 0;
                                            $fullStars = floor($avgRating);
                                            $hasHalfStar = $avgRating - $fullStars >= 0.5;
                                        @endphp

                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $fullStars)
                                                <i class="fa fa-star text-warning"></i> <!-- Full star -->
                                            @elseif ($i == $fullStars + 1 && $hasHalfStar)
                                                <i class="fa fa-star-half-o"></i> <!-- Half star -->
                                            @else
                                                <i class="fa fa-star-o"></i> <!-- Empty star -->
                                            @endif
                                        @endfor
                                        <span class="text-muted" style="font-size: 16px;">
                                            ({{ round($itemRecent->comments->count()) ?? 0 }})
                                        </span>
                                    </div>
                                    <h5>{{ number_format($itemRecent->price) }} đ</h5>
                                    @if ($itemRecent->discount_id && $itemRecent->discount_id !== null)
                                        <h6 class="text-muted" style="text-decoration: line-through;">
                                            {{ number_format($originalPrice) }} đ</h6>
                                    @endif
                                    <div class="product__color__select">
                                        <label for="pc-1">
                                            <input type="radio" id="pc-1">
                                        </label>
                                        <label class="active black" for="pc-2">
                                            <input type="radio" id="pc-2">
                                        </label>
                                        <label class="grey" for="pc-3">
                                            <input type="radio" id="pc-3">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                {{-- {{ $productRecentInfo->links() }} --}}
            </div>
        </section>
    @endif
    <!-- Product Section Recently End -->

    <!-- Share Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content mt-auto" style="right: 30%; height: 70%; width: 70%;">
                <div class="modal-header">
                    <h5 class="modal-title" id="shareModalLabel">Chia sẻ sản phẩm</h5>
                    <button type="button" class="btn-close border-0 bg-transparent" data-bs-dismiss="modal"
                        aria-label="Close">X</button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-4">
                        <div id="qrcode" class="mx-auto" style="width: 200px; height: 200px;"></div>
                    </div>
                    <div class="input-group mb-3">

                        @php
                            $shareLink = route('product.share.redirect', ['hash' => $encodedId]);
                        @endphp
                        <input type="text" id="shareLinkInput" class="form-control" value="{{ $shareLink }}"
                            readonly>
                        <button class="btn btn-outline-secondary" type="button" id="copyLinkBtn">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <div class="social-share mt-3">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                            target="_blank" class="btn btn-primary me-2">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($productDetail->product_name) }}"
                            target="_blank" class="btn btn-info">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <link rel="stylesheet" href="{{ asset('client/css/product-detail.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}">
    <link rel="stylesheet" href="{{ asset('client/css/comment.css') }}">
    <link rel="stylesheet" href="{{ asset('client/css/cart-add.css') }}">
    <link rel="stylesheet" href="{{ asset('client/css/stock.css') }}">
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    @if (Session::has('success'))
        <script src="{{ asset('assets/js/message.js') }}"></script>
    @endif

    {{-- Xử lý modal chia sẻ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const shareModal = document.getElementById('shareModal');

            // Khi modal hiển thị, tạo QR code
            shareModal.addEventListener('shown.bs.modal', function() {
                // Xóa QR code cũ nếu có
                document.getElementById('qrcode').innerHTML = '';
                const link_qr = document.getElementById('shareLinkInput').value;
                // console.log(link_qr);

                // Tạo QR code mới
                new QRCode(document.getElementById('qrcode'), {
                    text: link_qr,
                    width: 200,
                    height: 200,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            });

            // Xử lý nút copy link
            document.getElementById('copyLinkBtn').addEventListener('click', function(e) {
                // console.log(e.target);
                const shareLink = document.getElementById('shareLinkInput');
                shareLink.select();
                document.execCommand('copy');

                // Hiển thị thông báo
                showToast('Đã sao chép liên kết vào clipboard!');
            });
        });
    </script>

    {{-- xử lý thông báo và ảnh, stock --}}
    <script>
        // Hàm cập nhật trạng thái các size
        function updateSizeAvailability(availableSizes) {
            document.querySelectorAll('input[name="size"]').forEach(input => {
                if (input.value === "") return;

                const sizeLabel = document.querySelector(`label[for="size-${input.value}"]`);
                if (sizeLabel) {
                    if (availableSizes.includes(input.value)) {
                        input.disabled = false;
                        sizeLabel.style.opacity = "1";
                        sizeLabel.style.cursor = "pointer";
                        sizeLabel.style.textDecoration = "none";
                    } else {
                        input.disabled = true;
                        sizeLabel.style.opacity = "0.5";
                        sizeLabel.style.cursor = "not-allowed";
                    }
                }
            });

            // Hiển thị thông báo nếu không có size nào khả dụng
            const noticeElement = document.querySelector('.size-notice');
            if (noticeElement) {
                if (availableSizes.length === 0) {
                    noticeElement.textContent = "Màu này hiện không có size nào khả dụng";
                    noticeElement.style.color = "#dc3545";
                } else {
                    noticeElement.textContent = "";
                }
            }
        }
        // Hàm hiển thị toast message
        function showToast(message) {
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.style.position = 'fixed';
                toastContainer.style.top = '20px';
                toastContainer.style.right = '20px';
                toastContainer.style.zIndex = '9999';
                document.body.appendChild(toastContainer);
            }

            const toast = document.createElement('div');
            toast.className = 'toast show';
            toast.style.minWidth = '300px';
            toast.style.backgroundColor = '#f8d7da';
            toast.style.color = '#721c24';
            toast.style.border = '1px solid #f5c6cb';
            toast.style.borderRadius = '4px';
            toast.style.boxShadow = '0 0.5rem 1rem rgba(0, 0, 0, 0.15)';
            toast.style.animation = 'slideIn 0.5s, fadeOut 0.5s 2.5s';

            toast.innerHTML = `
            <div class="toast-header" style="background-color: #f8d7da; border-bottom: 1px solid #f5c6cb;">
                <strong class="mr-auto">Thông báo</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close"
                        style="background: none; border: none; font-size: 1.5rem; color: #721c24;">
                    &times;
                </button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        `;

            toastContainer.appendChild(toast);

            setTimeout(() => {
                toast.style.animation = 'fadeOut 0.5s';
                setTimeout(() => {
                    toast.remove();
                }, 500);
            }, 3000);

            const closeBtn = toast.querySelector('.close');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    toast.style.animation = 'fadeOut 0.5s';
                    setTimeout(() => {
                        toast.remove();
                    }, 500);
                });
            }
        }





        // Hàm cập nhật ảnh sản phẩm theo màu được chọn
        function updateProductImages(selectedColor) {
            // Chỉ xử lý các tab-pane liên quan đến ảnh sản phẩm, không ảnh hưởng đến description/reviews
            const imagePanes = document.querySelectorAll('.product-gallery .tab-pane');
            imagePanes.forEach(pane => {
                pane.classList.remove('show', 'active');
            });

            // Ẩn tất cả thumbnails
            const thumbnails = document.querySelectorAll('.thumbnail-item');
            thumbnails.forEach(item => {
                item.classList.add('d-none');
            });

            // Hiển thị thumbnails của màu được chọn
            const colorThumbnails = document.querySelectorAll(`.thumbnail-${selectedColor}`);
            colorThumbnails.forEach(item => {
                item.classList.remove('d-none');
            });

            // Kích hoạt ảnh đầu tiên của màu được chọn
            const firstThumbnailBtn = document.querySelector(`.thumbnail-${selectedColor} .thumbnail-btn`);
            if (firstThumbnailBtn) {
                const targetId = firstThumbnailBtn.getAttribute('data-bs-target');
                const targetPane = document.querySelector(targetId);

                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                    firstThumbnailBtn.classList.add('active');
                }
            }
        }

        // Cập nhật hàm initThumbnailClickEvents
        function initThumbnailClickEvents() {
            document.querySelectorAll('.thumbnail-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Chỉ xử lý các thumbnail trong product gallery, không ảnh hưởng đến description/reviews tabs
                    if (!this.closest('.product-gallery')) return;

                    // Xóa active class từ tất cả các nút thumbnail và pane ảnh
                    document.querySelectorAll('.product-gallery .thumbnail-btn').forEach(b => {
                        b.classList.remove('active');
                    });
                    document.querySelectorAll('.product-gallery .tab-pane').forEach(p => {
                        p.classList.remove('show', 'active');
                    });

                    // Thêm active class cho nút và pane được chọn
                    this.classList.add('active');
                    const targetId = this.getAttribute('data-bs-target');
                    if (targetId) {
                        const targetPane = document.querySelector(targetId);
                        if (targetPane) {
                            targetPane.classList.add('show', 'active');
                        }
                    }
                });
            });

            // Thêm sự kiện cho ảnh chính để ngăn reload
            document.querySelectorAll('.main-image').forEach(img => {
                img.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });
        }

        // Thêm sự kiện cho các tab description/reviews để đảm bảo chúng hoạt động độc lập
        function initDescriptionReviewTabs() {
            const tabLinks = document.querySelectorAll('.product-tabs .nav-link');

            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Đảm bảo chỉ xử lý các tab description/reviews
                    if (!this.closest('.product-tabs')) return;

                    const targetId = this.getAttribute('data-bs-target');
                    const targetPane = document.querySelector(targetId);

                    // Ẩn tất cả các pane
                    document.querySelectorAll('.product-tabs .tab-pane').forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });

                    // Hiển thị pane được chọn
                    if (targetPane) {
                        targetPane.classList.add('show', 'active');
                    }

                    // Cập nhật trạng thái active cho các tab
                    tabLinks.forEach(tab => {
                        tab.classList.remove('active');
                    });
                    this.classList.add('active');
                });
            });
        }



        // Hàm cập nhật trạng thái kho hàng
        function updateStockUI(stock) {
            const quantityInput = document.querySelector(".quantity-input");
            const addToCartBtn = document.querySelector(".add-to-cart-btn");
            const stockText = document.querySelector(".stock-extist");

            if (quantityInput && addToCartBtn && stockText) {
                quantityInput.value = 1;
                quantityInput.max = stock;
                quantityInput.disabled = stock === 0;

                if (stock === 0) {
                    stockText.innerText = "Hết hàng";
                    stockText.className = "stock-extist out-of-stock";
                    addToCartBtn.disabled = true;
                    addToCartBtn.style.backgroundColor = "gray";
                    // addToCartBtn.style.opacity = "0.5";
                } else {
                    stockText.innerText = `Còn lại: ${stock} sản phẩm`;
                    stockText.className = "stock-extist in-stock";
                    addToCartBtn.disabled = false;
                    addToCartBtn.style.backgroundColor = "#2c3e50";
                    // addToCartBtn.style.opacity = "1";
                }
            }
        }

        // Hàm reset số lượng và giỏ hàng
        function resetQuantityAndCart(stock = 0) {
            const quantityInput = document.querySelector(".quantity-input");
            const addToCartBtn = document.querySelector(".add-to-cart-btn");
            const stockText = document.querySelector(".stock-extist");

            if (quantityInput && addToCartBtn && stockText) {
                quantityInput.value = 1;
                quantityInput.max = stock;
                quantityInput.disabled = stock === 0;

                if (stock === 0) {
                    stockText.innerText = "Hết hàng";
                    stockText.className = "stock-extist out-of-stock";
                    addToCartBtn.disabled = true;
                    // addToCartBtn.style.opacity = "0.5";
                } else {
                    stockText.innerText = `Còn lại: ${stock} sản phẩm`;
                    stockText.className = "stock-extist in-stock";
                    addToCartBtn.disabled = false;
                    // addToCartBtn.style.opacity = "1";
                }
            }
        }

        // Xử lý sự kiện khi DOM đã tải xong
        document.addEventListener('DOMContentLoaded', function() {
            initThumbnailClickEvents();

            initDescriptionReviewTabs();

            // Kiểm tra và kích hoạt màu đầu tiên
            const firstColor = document.querySelector('input[name="color"]:checked');
            if (firstColor) {
                updateProductImages(firstColor.value);
            }

            // Kiểm tra và kích hoạt size đầu tiên
            const firstSize = document.querySelector('input[name="size"]:checked');
            if (firstSize) {
                firstSize.dispatchEvent(new Event('change'));
            }



            document.querySelectorAll('input[name="color"]').forEach(item => {
                item.addEventListener('change', async function(e) {
                    const selectedColor = e.target.value;
                    const productId = @json($productDetail->id);

                    // Cập nhật ảnh theo màu
                    updateProductImages(selectedColor);

                    // Reset size selection
                    const defaultSize = document.querySelector('input[name="size"][value=""]');
                    if (defaultSize) defaultSize.checked = true;
                    resetQuantityAndCart(0);

                    try {
                        // Lấy danh sách size có sẵn cho màu này
                        let response = await fetch(
                            `/api/product-variant-size/${selectedColor}/${productId}`
                        );
                        let data = await response.json();

                        if (data.status_code === 200) {
                            let availableVariants = data.data;
                            let availableSizes = availableVariants.map(variant => variant.size);

                            // Cập nhật trạng thái các size
                            updateSizeAvailability(availableSizes);

                            // Nếu chỉ có 1 size khả dụng, tự động chọn
                            if (availableSizes.length === 1) {
                                const singleSizeInput = document.querySelector(
                                    `input[name="size"][value="${availableSizes[0]}"]`);
                                if (singleSizeInput) {
                                    singleSizeInput.checked = true;
                                    singleSizeInput.dispatchEvent(new Event('change'));
                                }
                            }
                        }
                    } catch (error) {
                        console.error("Lỗi khi fetch dữ liệu:", error);
                        showToast("Có lỗi khi tải thông tin size");
                    }
                });
            });


            // Cập nhật phần xử lý sự kiện chọn size
            document.querySelectorAll('input[name="size"]').forEach(sizeInput => {
                sizeInput.addEventListener('change', async (e) => {
                    const selectedColor = document.querySelector('input[name="color"]:checked')
                        ?.value;
                    const selectedSize = e.target.value;
                    const productId = @json($productDetail->id);

                    // Kiểm tra nếu chưa chọn màu
                    if (!selectedColor) {
                        showToast("Vui lòng chọn màu trước khi chọn size");
                        e.target.checked = false; // Bỏ chọn size
                        const defaultSize = document.querySelector(
                            'input[name="size"][value=""]');
                        if (defaultSize) defaultSize.checked = true;
                        return;
                    }

                    // Kiểm tra size hợp lệ
                    if (!selectedSize) {
                        resetQuantityAndCart(0);
                        return;
                    }

                    try {
                        // Hiển thị loading nếu cần
                        const stockText = document.querySelector(".stock-extist");
                        if (stockText) stockText.innerText = "Đang kiểm tra...";

                        const response = await fetch(
                            `/api/product-variant-selected/${selectedSize}/${selectedColor}/${productId}`
                        );
                        const data = await response.json();

                        if (data.status_code === 200 && data.data) {
                            const stock = data.data.available_stock;
                            updateStockUI(stock);
                        } else {
                            showToast("Size này hiện không có hàng");
                            resetQuantityAndCart(0);
                        }
                    } catch (error) {
                        console.error("Lỗi khi lấy dữ liệu số lượng tồn kho:", error);
                        showToast("Có lỗi xảy ra khi kiểm tra tồn kho");
                        resetQuantityAndCart(0);
                    }
                });
            });

            // Xử lý submit form thêm vào giỏ hàng
            const addToCartForm = document.getElementById('add-to-cart-form');
            if (addToCartForm) {
                addToCartForm.addEventListener('submit', function(event) {
                    const selectedSizeInput = document.querySelector('input[name="size"]:checked');
                    const selectedColorInput = document.querySelector('input[name="color"]:checked');

                    const selectedSize = selectedSizeInput ? selectedSizeInput.value : null;
                    const selectedColor = selectedColorInput ? selectedColorInput.value : null;

                    let errorMessage = '';
                    const isSizeSelected = selectedSize !== null && selectedSize !== '';
                    const isColorSelected = selectedColor !== null;

                    if (!isSizeSelected && !isColorSelected) {
                        errorMessage = 'Vui lòng chọn size và màu sắc để thêm vào giỏ hàng.';
                    } else if (!isSizeSelected) {
                        errorMessage = 'Vui lòng chọn size bạn cần.';
                    } else if (!isColorSelected) {
                        errorMessage = 'Vui lòng chọn màu sắc bạn thích.';
                    }

                    if (errorMessage) {
                        showToast(errorMessage);
                        event.preventDefault();
                    }
                });
            }

            // Xử lý số lượng sản phẩm (nếu có quantity buttons)
            const decBtn = document.querySelector('.pro-qty .qty-btn.dec');
            const incBtn = document.querySelector('.pro-qty .qty-btn.inc');
            const quantityInput = document.querySelector('.quantity-input');

            if (decBtn && incBtn && quantityInput) {
                decBtn.addEventListener('click', function() {
                    if (parseInt(quantityInput.value) > 1) {
                        quantityInput.value = parseInt(quantityInput.value) - 1;
                    }
                });

                incBtn.addEventListener('click', function() {
                    const max = parseInt(quantityInput.max);
                    if (parseInt(quantityInput.value) < max) {
                        quantityInput.value = parseInt(quantityInput.value) + 1;
                    } else {
                        showToast(`Số lượng tối đa là ${max}`);
                    }
                });
            }
        });
    </script>


    {{-- xử lý ngôi sao --}}
    <script>
        function generateStarRating(rating) {
            rating = parseFloat(rating) || 0;
            let stars = '';
            const fullStars = Math.floor(rating);
            const hasHalfStar = rating % 1 >= 0.5;

            // Full stars
            for (let i = 0; i < fullStars; i++) {
                stars += '<i class="fa fa-star text-warning" style="margin-right: 1px;"></i>';
            }

            // Half star
            if (hasHalfStar) {
                stars += '<i class="fa fa-star-half-o" style="color: #ffc107; margin-right: 1px;"></i>';
            }

            // Empty stars
            const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
            for (let i = 0; i < emptyStars; i++) {
                stars += '<i class="fa fa-star-o" style="margin-right: 1px; color: #ccc;"></i>';
            }

            return stars;
        }
    </script>

    {{-- xử lý giờ khuyến mãi --}}
    <script>
        // Hàm đếm ngược
        function updateCountdown(startDate, endDate) {
            const now = new Date().getTime();
            const start = new Date(startDate).getTime();
            const end = new Date(endDate).getTime();

            // Kiểm tra nếu chương trình chưa bắt đầu
            if (now < start) {
                document.getElementById('countdown-timer').innerHTML = "Chưa bắt đầu";
                return;
            }

            // Kiểm tra nếu chương trình đã kết thúc
            if (now > end) {
                document.getElementById('countdown-timer').innerHTML = "Đã kết thúc";
                return;
            }

            // Tính thời gian còn lại
            const distance = end - now;

            // Tính toán ngày, giờ, phút, giây
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Hiển thị kết quả
            let countdownText = "";
            if (days > 0) {
                countdownText += `${days} ngày `;
            }
            countdownText += `${hours} giờ ${minutes} phút ${seconds} giây`;

            document.getElementById('countdown-timer').innerHTML = countdownText;

            // Cập nhật mỗi giây
            setTimeout(() => updateCountdown(startDate, endDate), 1000);
        }

        // Gọi hàm khi trang tải xong
        document.addEventListener('DOMContentLoaded', function() {
            @if ($productDetail->discount_id && $productDetail->discount)
                const startDate = '{{ $productDetail->discount->start_date }}';
                const endDate = '{{ $productDetail->discount->end_date }}';
                updateCountdown(startDate, endDate);
            @endif
        });
    </script>

    {{-- danh sách sản phẩm liên quan --}}
    <script>
        $(document).ready(function() {
            // console.log("Loading product suggestions...");
            $("#suggestion-list-product").empty(); // Xóa dữ liệu cũ trước khi cập nhật mới

            $.ajax({
                url: "http://127.0.0.1:8000/api/suggest-content-based", // API lấy danh sách sản phẩm
                method: "GET",
                dataType: "json",
                success: function(data) {
                    if (data.length > 0) {
                        // console.log(data);
                        data.forEach(function(item) {
                            let price = item.price;
                            const showOriginalPrice = item.discount_id !== null && item
                                .discount;

                            let formattedPrice = new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(item.price);
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
                                            src="${item.image}"
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
                                                        <div class="rating" >
                                                            ${generateStarRating(item.star)}
                                                            <span class="text-muted" style="font-size: 16px;"> (${item.comments_count})</span>
                                                        </div>

                                                        <h5>${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price)}</h5>
                                                        <h6 class="text-muted original-price-begin" style="text-decoration: line-through; display: ${showOriginalPrice ? 'block' : 'none'};">${formattedPrice}</h6>
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

    {{-- Xử lý bộ lọc đánh giá --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const starFilterButtons = document.querySelectorAll('.star-filter-btn');
            const otherFilterButtons = document.querySelectorAll('.filter-btn');
            const reviewItems = document.querySelectorAll('.review-item');
            const reviewsList = document.querySelector('.reviews-list');
            const noReviewsMessage = document.querySelector('.no-reviews') || createNoReviewsMessage();

            // Tạo thông báo khi không có đánh giá nếu chưa tồn tại
            function createNoReviewsMessage() {
                const message = document.createElement('div');
                message.className = 'no-reviews';
                message.innerHTML = `
            <i class="fas fa-comments fa-3x"></i>
            <p>Không có đánh giá nào phù hợp với bộ lọc!</p>
        `;
                return message;
            }

            // Reset tất cả đánh giá về trạng thái hiển thị
            function resetAllReviews() {
                reviewItems.forEach(item => {
                    item.style.display = 'block';
                });
            }

            // Lọc đánh giá theo tiêu chí
            function filterReviews() {
                const activeStarFilter = document.querySelector('.star-filter-btn.active');
                const activeOtherFilter = document.querySelector('.filter-btn.active');

                const starValue = activeStarFilter ? activeStarFilter.dataset.rating : 'all';
                const otherValue = activeOtherFilter ? activeOtherFilter.dataset.filter : 'all';

                let hasVisibleItems = false;

                reviewItems.forEach(item => {
                    const itemRating = item.dataset.rating;
                    const hasImage = item.dataset.hasImage === 'true';

                    // Điều kiện lọc
                    const starMatch = starValue === 'all' || parseInt(itemRating) === parseInt(starValue);
                    const otherMatch = otherValue === 'all' ||
                        (otherValue === 'has_image' && hasImage);

                    if (starMatch && otherMatch) {
                        item.style.display = 'block';
                        hasVisibleItems = true;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Xử lý thông báo khi không có đánh giá
                if (!hasVisibleItems) {
                    if (!document.querySelector('.no-reviews')) {
                        reviewsList.appendChild(noReviewsMessage);
                    }
                    noReviewsMessage.style.display = 'block';
                } else {
                    noReviewsMessage.style.display = 'none';
                }
            }

            // Sự kiện click cho bộ lọc sao
            starFilterButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    starFilterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    filterReviews();
                });
            });

            // Sự kiện click cho bộ lọc khác
            otherFilterButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    otherFilterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    filterReviews();
                });
            });

            // Kích hoạt lọc ban đầu
            filterReviews();
        });
    </script>


    {{-- xử lý click thanh tìm kiếm --}}
    <script>
        $(document).ready(function() {
            // gắn sự kiện click vao nút tìm kiếm do bên đây ko ăn js
            $('.search-btn').click(function(e) {
                console.log("click");
                $('.js-modal').addClass("open");
            });

            $('.js-modal-close').click(function() {
                $('.js-modal').removeClass("open");
            });

            // Tìm kiếm sản phẩm bằng AJAX
            $("#search-box").on("input", function(e) {
                let query = $("#search-box").val();
                // console.log(query);
                if (query.length > 1) {
                    $.ajax({
                        url: "http://127.0.0.1:8000/api/search",
                        type: "GET",
                        data: {
                            q: query
                        },
                        success: function(data) {
                            let results = $("#search-results");
                            // console.log(results);
                            results.empty();

                            if (data.results.length > 0) {
                                data.results.forEach(function(item) {
                                    // console.log(item);
                                    let price = Intl.NumberFormat('vi-VN').format(item
                                        .price);
                                    if (item.discount_id != null) {
                                        price = Intl.NumberFormat('vi-VN').format(item
                                            .price - (item
                                                .price * item.discount
                                                .percent_discount));
                                    }
                                    results.append(`
                                        <li class="list-group-item d-flex align-items-center p-3 border-bottom"
                                                style="cursor: pointer;"
                                                onmouseover="this.style.backgroundColor='#ccc'; this.style.textDecoration='underline';"
                                                onmouseout="this.style.backgroundColor='#fff'; this.style.textDecoration='none';">
                                            <a class="fw-medium text-decoration-none text-dark" href="{{ url('product') }}/${item.slug}">
                                            <img src="${item.image}" width="50" height="50" alt="">
                                            ${item.product_name} | <p class="d-inline">Giá:</p> ${price} đ
                                            </a>
                                        </li>

                                `);
                                });
                            } else {
                                results.append("<li>Không tìm thấy kết quả</li>");
                            }
                        }
                    });
                }
            });

        });
    </script>
    <script src="{{ asset('client/js/cart-add.js') }}"></script>
@endsection

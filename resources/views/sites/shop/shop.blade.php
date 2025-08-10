{{-- @include('sites.components._chatbox_and_search') --}}
{{-- @php
    dd($materials);
@endphp --}}
@extends('sites.master', ['hideChatbox' => true])
@section('title', 'Cửa Hàng')
@section('content')
    @php
        $totalProduct = 0;
        if (Session::has('cart')) {
            foreach (Session::get('cart') as $item) {
                $totalProduct += $item->quantity;
            }
        } else {
            $totalProduct = 0;
        }
    @endphp
    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Shop</h4>
                        <div class="breadcrumb__links mb-3">
                            <a href="{{ route('sites.home') }}">Home</a>
                            <span>Shop</span>
                        </div>
                        <div class="fw-bold" style="font-size: 2rem">
                            @if (!empty(request('q')))
                                Kết quả tìm kiếm của từ khoá "{{ request('q') }}"
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->
    <!-- Shop Section Begin -->
    <section class="shop spad">
        <div class="container">
            <!-- Phần bộ lọc cải tiến -->
            <div class="filter-bar mb-5">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="active-filters">
                            @if (request()->hasAny(['q', 'category', 'brand', 'price', 'tag', 'color', 'promotion', 'size', 'material']))
                                <span class="filter-title">Bộ lọc hiện tại:</span>
                                @if (request('q'))
                                    <span class="filter-badge">
                                        Tìm kiếm: "{{ request('q') }}"
                                        <a href="{{ request()->fullUrlWithQuery(['q' => null, 'page' => 1]) }}"
                                            class="remove-filter">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                                @if (request('category'))
                                    <span class="filter-badge">
                                        Danh mục: {{ request('category') }}
                                        <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => 1]) }}"
                                            class="remove-filter">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                                @if (request('brand'))
                                    <span class="filter-badge">
                                        Thương hiệu: {{ request('brand') }}
                                        <a href="{{ request()->fullUrlWithQuery(['brand' => null, 'page' => 1]) }}"
                                            class="remove-filter">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                                @if (request('price'))
                                    <span class="filter-badge">
                                        Giá: {{ $priceRanges[request('price')] ?? request('price') }}
                                        <a href="{{ request()->fullUrlWithQuery(['price' => null, 'page' => 1]) }}"
                                            class="remove-filter">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                                @if (request('tag'))
                                    <span class="filter-badge">
                                        Tag: {{ request('tag') }}
                                        <a href="{{ request()->fullUrlWithQuery(['tag' => null, 'page' => 1]) }}"
                                            class="remove-filter">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                                @if (request('color'))
                                    <span class="filter-badge">
                                        Màu: {{ request('color') }}
                                        <a href="{{ request()->fullUrlWithQuery(['color' => null, 'page' => 1]) }}"
                                            class="remove-filter">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                                @if (request('size'))
                                    <span class="filter-badge">
                                        Size: {{ request('size') }}
                                        <a href="{{ request()->fullUrlWithQuery(['size' => null, 'page' => 1]) }}"
                                            class="remove-filter">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                                @if (request('promotion'))
                                    <span class="filter-badge">
                                        Đang khuyến mãi
                                        <a href="{{ request()->fullUrlWithQuery(['promotion' => null, 'page' => 1]) }}"
                                            class="remove-filter">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                                @if (request('material'))
                                    <span class="filter-badge">
                                        Chất liệu: {{ request('material') }}
                                        <a href="{{ request()->fullUrlWithQuery(['material' => null, 'page' => 1]) }}"
                                            class="remove-filter">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                                <a href="{{ url()->current() }}" class="clear-all-filters">
                                    <i class="fas fa-times-circle"></i> Xóa tất cả
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="sort-options">
                            <form method="GET" action="{{ url()->current() }}">
                                @foreach (request()->except('sort_by', 'page') as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <div class="input-group">
                                    <label class="input-group-text" for="sortSelect">
                                        <i class="fas fa-sort-amount-down"></i>
                                    </label>
                                    <select name="sort_by" id="sortSelect" class="form-select"
                                        onchange="this.form.submit()">
                                        <option value="newest" {{ $sortBy == 'newest' ? 'selected' : '' }}>Mới nhất
                                        </option>
                                        <option value="price_asc" {{ $sortBy == 'price_asc' ? 'selected' : '' }}>Giá tăng
                                            dần</option>
                                        <option value="price_desc" {{ $sortBy == 'price_desc' ? 'selected' : '' }}>Giá giảm
                                            dần</option>
                                        <option value="best_selling" {{ $sortBy == 'best_selling' ? 'selected' : '' }}>Bán
                                            chạy nhất</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3">
                    <div class="shop__sidebar">
                        <div class="shop__sidebar__search">
                            <form action="/shop" method="GET">
                                <input style="border: 1px solid #000000;" class="form-control" type="text" name="q"
                                    placeholder="Tìm kiếm..." value="{{ request('q') }}">
                                <button type="submit"><span class="icon_search text-dark"></span></button>
                            </form>
                        </div>
                        <div class="shop__sidebar__accordion">
                            <div class="accordion" id="accordionExample">
                                <div class="card">
                                    <div class="card-heading">
                                        <a data-toggle="collapse" data-target="#collapseOne">Danh Mục</a>
                                    </div>
                                    <div id="collapseOne" class="collapse show" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <div class="shop__sidebar__categories">
                                                <ul class="nice-scroll" id="category-list"></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-heading">
                                        <a data-toggle="collapse" data-target="#collapseTwo">Thương Hiệu</a>
                                    </div>
                                    <div id="collapseTwo" class="collapse show" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <div class="shop__sidebar__brand">
                                                <ul id="brand-list"></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-heading">
                                        <a data-toggle="collapse" data-target="#collapseThree">Lọc Giá (THEO VND)</a>
                                    </div>
                                    <div id="collapseThree" class="collapse show" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <div class="shop__sidebar__price">
                                                <ul>
                                                    <li><a class="price__item text-dark"
                                                            href="javascript:void(0)">{{ number_format(0, 0, ',', '.') }}
                                                            -
                                                            {{ number_format(100000, 0, ',', '.') }}</a></li>
                                                    <li><a class="price__item text-dark"
                                                            href="javascript:void(0)">{{ number_format(100000, 0, ',', '.') }}
                                                            - {{ number_format(300000, 0, ',', '.') }}</a></li>
                                                    <li><a class="price__item text-dark"
                                                            href="javascript:void(0)">{{ number_format(300000, 0, ',', '.') }}
                                                            - {{ number_format(500000, 0, ',', '.') }}</a></li>
                                                    <li><a class="price__item text-dark"
                                                            href="javascript:void(0)">{{ number_format(500000, 0, ',', '.') }}
                                                            - {{ number_format(1000000, 0, ',', '.') }}</a></li>
                                                    <li><a class="price__item text-dark" href="javascript:void(0)">Trên
                                                            {{ number_format(1000000, 0, ',', '.') }}</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-heading">
                                        <a data-toggle="collapse" data-target="#collapseFour">Màu sắc</a>
                                    </div>
                                    <div id="collapseFour" class="collapse show" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <div class="shop__sidebar__colors">
                                                <ul>
                                                    @foreach ($colors as $color)
                                                        <li>
                                                            <a href="{{ request()->fullUrlWithQuery(['color' => $color, 'page' => 1]) }}"
                                                                class="color-item"
                                                                style="background-color: {{ getColorHex($color) }};
                                                                      display: inline-block;
                                                                      width: 20px;
                                                                      height: 20px;
                                                                      border-radius: 50%;
                                                                      margin-right: 5px;"
                                                                title="{{ $color }}"></a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-heading">
                                        <a data-toggle="collapse" data-target="#collapseFive">Size</a>
                                    </div>
                                    <div id="collapseFive" class="collapse show" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <div class="shop__sidebar__sizes">
                                                <ul>
                                                    @foreach ($sizes as $size)
                                                        <li>
                                                            <a class="text-dark"
                                                                href="{{ request()->fullUrlWithQuery(['size' => $size, 'page' => 1]) }}"
                                                                class="{{ request('size') == $size ? 'active' : '' }}">
                                                                {{ $size }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-heading">
                                        <a data-toggle="collapse" data-target="#collapseMaterial">Chất liệu</a>
                                    </div>
                                    <div id="collapseMaterial" class="collapse show" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <div class="shop__sidebar__materials">
                                                <ul>
                                                    @foreach ($materials as $material)
                                                        <li>
                                                            <a class="text-dark"
                                                                href="{{ request()->fullUrlWithQuery(['material' => $material, 'page' => 1]) }}"
                                                                class="{{ request('material') == $material ? 'active' : '' }}">
                                                                {{ $material }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-heading">
                                        <a data-toggle="collapse" data-target="#collapseSix">Khuyến mãi</a>
                                    </div>
                                    <div id="collapseSix" class="collapse show" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <div class="shop__sidebar__promotion">
                                                <ul>
                                                    <li>
                                                        <a href="{{ request()->fullUrlWithQuery(['promotion' => 1, 'page' => 1]) }}"
                                                            class="promotion-item">
                                                            Đang khuyến mãi
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Layout sẽ xấu nếu để --}}
                                {{-- <div class="card">
                                    <div class="card-heading">
                                        <a data-toggle="collapse" data-target="#collapseSeven">Tags</a>
                                    </div>
                                    <div id="collapseSeven" class="collapse show" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <div class="shop__sidebar__tags">
                                                <a class="tag-item" href="javascript:void(0)">Sơ Mi</a>
                                                <a class="tag-item" href="javascript:void(0)">Áo</a>
                                                <a class="tag-item" href="javascript:void(0)">Kẻ Sọc</a>
                                                <a class="tag-item" href="javascript:void(0)">Linen</a>
                                                <a class="tag-item" href="javascript:void(0)">Cotton</a>
                                                <a class="tag-item" href="javascript:void(0)">utme!</a>
                                                <a class="tag-item" href="javascript:void(0)">smart</a>
                                                <a class="tag-item" href="javascript:void(0)">thun</a>
                                                <a class="tag-item" href="javascript:void(0)">dài</a>
                                                <a class="tag-item" href="javascript:void(0)">dry-ex</a>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="shop__product__option">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="shop__product__option__left">
                                    <p>Danh sách của trang {{ $products->currentPage() }} gồm {{ count($products) }} sản
                                        phẩm</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @foreach ($products as $items)
                            @php
                                $discountName = '';
                                $originalPrice = $items->price;
                                if ($items->discount_id && $items->discount_id !== null) {
                                    $items->price = $items->price - $items->price * $items->Discount->percent_discount;
                                    $discountName = $items->Discount->name;
                                } else {
                                    $discountName = 'New';
                                }
                                $totalStock = 0;
                                if ($items->ProductVariants) {
                                    foreach ($items->ProductVariants as $variant) {
                                        if ($variant) {
                                            $totalStock += $variant->available_stock;
                                        }
                                    }
                                }
                            @endphp

                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="product__item" id="product-list-shop">
                                    <div class="product__item__pic ">
                                        <img class="product__item__pic set-bg" width="280" height="250"
                                            src="{{ $items->image }}" alt="{{ $items->product_name }}">
                                        <span class="label name-discount-shop">{{ $discountName }}</span>
                                        <ul class="product__hover">
                                            <li><a href="{{ route('sites.addToWishList', $items->id) }}"><img
                                                        src="{{ asset('client/img/icon/heart.png') }}"
                                                        alt=""></a></li>
                                            <li><a href="javascript:void(0);"><img
                                                        src="{{ asset('client/img/icon/compare.png') }}"
                                                        alt=""><span>Compare</span></a></li>
                                            <li><a href="{{ url('product') }}/{{ $items->slug }}"><img
                                                        src="{{ asset('client/img/icon/search.png') }}"
                                                        alt=""></a></li>
                                        </ul>
                                    </div>

                                    <div class="product__item__text">
                                        <h6>{{ $items->product_name }}</h6>
                                        @php
                                            if ($totalStock == 0) {
                                                echo '<span class=" badge badge-warning">Hết hàng</span>';
                                            } else {
                                                echo '<a href="javascript:void(0);" class="add-cart" data-id="' .
                                                    $items->id .
                                                    '">+Add To Cart</a>';
                                            }
                                        @endphp
                                        <div class="rating mt-2">
                                            @php
                                                $avgRating = $items->comments->avg('star') ?? 0;
                                                $fullStars = floor($avgRating);
                                                $hasHalfStar = $avgRating - $fullStars >= 0.5;
                                            @endphp

                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $fullStars)
                                                    <i class="fa fa-star text-warning"></i>
                                                @elseif ($i == $fullStars + 1 && $hasHalfStar)
                                                    <i class="fa fa-star-half-o"></i>
                                                @else
                                                    <i class="fa fa-star-o"></i>
                                                @endif
                                            @endfor
                                            <span class="text-muted"> ({{ round($items->comments->count()) ?? 0 }})</span>
                                        </div>
                                        <h5>{{ number_format($items->price) }} VND</h5>
                                        @if ($items->discount_id && $items->discount_id !== null)
                                            <h6 class="text-muted" style="text-decoration: line-through; display: block;">
                                                {{ number_format($originalPrice) }} VND</h6>
                                        @endif
                                        <div class="product__color__select">
                                            @if ($items->ProductVariants && $items->ProductVariants->count() > 0)
                                                @foreach ($items->ProductVariants->unique('color')->take(3) as $variant)
                                                    <label for="pc-{{ $variant->id }}"
                                                        style="background-color: {{ $variant->color }};"></label>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="product__pagination">
                                @if ($products->lastPage() > 1)
                                    @if ($products->onFirstPage())
                                        <a class="disabled">&laquo;</a>
                                    @else
                                        <a href="{{ $products->previousPageUrl() }}#product-list-shop">&laquo;</a>
                                    @endif

                                    @if ($products->currentPage() > 3)
                                        <a href="{{ $products->url(1) }}#product-list-shop">1</a>
                                        @if ($products->currentPage() > 4)
                                            <span class="dots">...</span>
                                        @endif
                                    @endif

                                    @foreach (range(1, $products->lastPage()) as $i)
                                        @if ($i >= $products->currentPage() - 2 && $i <= $products->currentPage() + 2)
                                            @if ($i == $products->currentPage())
                                                <a class="active">{{ $i }}</a>
                                            @else
                                                <a
                                                    href="{{ $products->url($i) }}#product-list-shop">{{ $i }}</a>
                                            @endif
                                        @endif
                                    @endforeach

                                    @if ($products->currentPage() < $products->lastPage() - 2)
                                        @if ($products->currentPage() < $products->lastPage() - 3)
                                            <span class="dots">...</span>
                                        @endif
                                        <a
                                            href="{{ $products->url($products->lastPage()) }}#product-list-shop">{{ $products->lastPage() }}</a>
                                    @endif

                                    @if ($products->hasMorePages())
                                        <a href="{{ $products->nextPageUrl() }}#product-list-shop">&raquo;</a>
                                    @else
                                        <a class="disabled">&raquo;</a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Shop Section End -->
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
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('client/css/cart-add.css') }}">
    <link rel="stylesheet" href="{{ asset('client/css/shop.css') }}">
@endsection

@section('js')
    <script src="{{ asset('client/js/cart-add.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // if(@json($products->count()) == 0){
            //     console.log("Khoong co san pham nao");
            // }
            // Initialize all async functions
            fetchCategories();
            fetchBrands();
            setupPriceFilters();
            setupTagFilters();
            setupSearch();

            // Highlight discount labels
            document.querySelectorAll('.name-discount-shop').forEach(element => {
                if (element.textContent.trim() !== "New") {
                    element.classList.add('bg-danger', 'text-white');
                }
            });

            // Handle sort select change
            document.getElementById('sortSelect').addEventListener('change', function() {
                const sortBy = this.value;
                const url = new URL(window.location.href);
                url.searchParams.set('sort_by', sortBy);
                url.searchParams.delete('page');
                window.location.href = url.toString() + '#product-list-shop';
            });
        });

        // Async function to fetch categories
        async function fetchCategories() {
            try {
                const response = await fetch('http://127.0.0.1:8000/api/category');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const data = await response.json();
                const categories = data.data;

                const categoryList = document.getElementById('category-list');
                categoryList.innerHTML = "";

                categories.forEach(category => {
                    const listItem = document.createElement('li');
                    const currentParams = new URLSearchParams(window.location.search);
                    currentParams.set('category', category.category_name);
                    currentParams.set('page', 1);
                    const newUrl = '/shop?' + currentParams.toString();

                    listItem.innerHTML =
                        `<a class="category__item text-dark" href="${newUrl}" data-category="${category.category_name}">${category.category_name} </a>`;
                    categoryList.appendChild(listItem);
                });
            } catch (error) {
                console.error("Error fetching categories:", error);
                // Display error to user if needed
            }
        }

        // Async function to fetch brands
        async function fetchBrands() {
            try {
                const response = await fetch('http://127.0.0.1:8000/api/brand');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const data = await response.json();
                const brands = data.data;

                const brandList = document.getElementById('brand-list');
                brandList.innerHTML = "";

                brands.forEach(brand => {
                    const listItem = document.createElement('li');
                    const currentParams = new URLSearchParams(window.location.search);
                    currentParams.set('brand', brand.brand);
                    currentParams.set('page', 1);
                    const newUrl = '/shop?' + currentParams.toString();

                    listItem.innerHTML =
                        `<a class="brand__item text-dark" href="${newUrl}" data-brand="${brand.brand}">${brand.brand}</a>`;
                    brandList.appendChild(listItem);
                });
            } catch (error) {
                console.error("Error fetching brands:", error);
                // Display error to user if needed
            }
        }

        // Setup price filters
        function setupPriceFilters() {
            const priceItems = document.querySelectorAll('.price__item');
            priceItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const priceText = this.textContent;
                    let priceParam = priceText.replaceAll(' ', '').replace('Trên', '').replace(/\./g, '');
                    if (priceText.includes('Dưới')) {
                        priceParam = '0-' + priceParam.replace('Dưới', '');
                    }

                    const currentParams = new URLSearchParams(window.location.search);
                    currentParams.set('price', priceParam);
                    currentParams.set('page', 1);
                    const newUrl = '/shop?' + currentParams.toString();

                    window.location.href = newUrl;
                });
            });
        }

        // Setup tag filters
        function setupTagFilters() {
            const tagItems = document.querySelectorAll('.tag-item');
            tagItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const tag = this.textContent.trim().replace(' ', '-');
                    const currentParams = new URLSearchParams(window.location.search);
                    currentParams.set('tag', tag);
                    currentParams.set('page', 1);
                    const newUrl = '/shop?' + currentParams.toString();
                    window.location.href = newUrl;
                });
            });
        }

        // Setup search functionality
        function setupSearch() {
            const searchBox = document.getElementById('search-box');
            if (searchBox) {
                searchBox.addEventListener('input', async function(e) {
                    const query = this.value;
                    if (query.length > 1) {
                        try {
                            const response = await fetch(
                                `http://127.0.0.1:8000/api/search?q=${encodeURIComponent(query)}`);
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            const data = await response.json();
                            const results = document.getElementById('search-results');
                            results.innerHTML = '';

                            if (data.results.length > 0) {
                                data.results.forEach(function(item) {
                                    let price = new Intl.NumberFormat('vi-VN').format(item.price);
                                    if (item.discount_id != null) {
                                        price = new Intl.NumberFormat('vi-VN').format(item.price - (item
                                            .price * item.discount.percent_discount));
                                    }
                                    const resultItem = document.createElement('li');
                                    resultItem.className =
                                        'list-group-item d-flex align-items-center p-3 border-bottom';
                                    resultItem.style.cssText = 'cursor: pointer;';
                                    resultItem.innerHTML = `
                                        <a class="fw-medium text-decoration-none text-dark" href="{{ url('product') }}/${item.slug}">
                                            <img src="${item.image}" width="50" height="50" alt="">
                                            ${item.product_name} | <p class="d-inline">Giá:</p> ${price} đ
                                        </a>
                                    `;
                                    results.appendChild(resultItem);
                                });
                            } else {
                                results.innerHTML = "<li class='list-group-item'>Không tìm thấy kết quả</li>";
                            }
                        } catch (error) {
                            console.error("Error searching:", error);
                        }
                    }
                });
            }
        }
    </script>
@endsection

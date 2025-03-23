@extends('sites.master')
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
            <div class="row">
                <div class="col-lg-3">
                    <div class="shop__sidebar">
                        <div class="shop__sidebar__search">
                            <form action="/shop" method="GET">
                                <input type="text" name="q" placeholder="Search..."
                                    value="{{ request('q') }}">
                                <button type="submit"><span class="icon_search"></span></button>
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
                                                <ul class="nice-scroll">
                                                    <div class="shop__sidebar__categories">
                                                        <ul class="nice-scroll" id="category-list"></ul>
                                                    </div>
                                                    <script>
                                                        async function fetchCategories() {
                                                            try {
                                                                let response = await fetch('http://127.0.0.1:8000/api/category');
                                                                let data = await response.json();
                                                                let categories = data.data;

                                                                let categoryList = document.getElementById('category-list');
                                                                categoryList.innerHTML = ""; // Xóa danh mục cũ

                                                                categories.forEach(category => {
                                                                    let listItem = document.createElement('li');
                                                                    listItem.innerHTML =
                                                                        `<a class="category__item" href="#" data-category="${category.category_name}">${category.category_name} (${category.products_count})</a>`;
                                                                    categoryList.appendChild(listItem);
                                                                });
                                                                let categoryItems = document.querySelectorAll('.category__item');
                                                                // console.log(categoryItems);
                                                                categoryItems.forEach(item => {
                                                                    item.addEventListener('click', function(e) {
                                                                        e.preventDefault();
                                                                        let category = this.getAttribute("data-category");
                                                                        window.location.href = '/shop?category=' + encodeURIComponent(category);
                                                                    });
                                                                });

                                                            } catch (error) {
                                                                console.error("Lỗi API:", error);
                                                            }
                                                        }
                                                        fetchCategories();
                                                    </script>
                                                </ul>
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
                                                <ul id="brand-list">
                                                    <script>
                                                        async function fetchBrand() {
                                                            try {
                                                                let response = await fetch('http://127.0.0.1:8000/api/brand');
                                                                let data = await response.json();
                                                                let brands = data.data;

                                                                let brandList = document.getElementById('brand-list');
                                                                brandList.innerHTML = ""; // Xóa danh sách cũ

                                                                brands.forEach(brand => {
                                                                    let listItem = document.createElement('li');
                                                                    listItem.innerHTML =
                                                                        `<a class="brand__item" href="#" data-brand="${brand.brand}">${brand.brand}</a>`;
                                                                    brandList.appendChild(listItem);
                                                                });

                                                                let brandItems = document.querySelectorAll('.brand__item');
                                                                brandItems.forEach(item => {
                                                                    item.addEventListener('click', function(e) {
                                                                        e.preventDefault();
                                                                        let brand = this.getAttribute("data-brand");
                                                                        window.location.href = '/shop?brand=' + encodeURIComponent(brand);
                                                                    });
                                                                });

                                                            } catch (error) {
                                                                console.error("Lỗi API:", error);
                                                            }
                                                        }
                                                        fetchBrand();
                                                    </script>
                                                </ul>
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
                                                    <li><a class="price__item"
                                                            href="javascript:void(0)">{{ number_format(0, 0, ',', '.') }} -
                                                            {{ number_format(100000, 0, ',', '.') }}</a></li>
                                                    <li><a class="price__item"
                                                            href="javascript:void(0)">{{ number_format(100000, 0, ',', '.') }}
                                                            - {{ number_format(300000, 0, ',', '.') }}</a></li>
                                                    <li><a class="price__item"
                                                            href="javascript:void(0)">{{ number_format(300000, 0, ',', '.') }}
                                                            - {{ number_format(500000, 0, ',', '.') }}</a></li>
                                                    <li><a class="price__item"
                                                            href="javascript:void(0)">{{ number_format(500000, 0, ',', '.') }}
                                                            - {{ number_format(1000000, 0, ',', '.') }}</a></li>
                                                    <li><a class="price__item" href="javascript:void(0)">Trên
                                                            {{ number_format(1000000, 0, ',', '.') }}</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <script>
                                            let priceItems = document.querySelectorAll('.price__item');
                                            priceItems.forEach(item => {
                                                item.addEventListener('click', function(e) {
                                                    e.preventDefault();
                                                    let price = this.textContent;
                                                    window.location.href = '/shop?price=' + encodeURIComponent(price.replaceAll(' ', '')
                                                        .replace('Trên', ''));
                                                });
                                            });
                                        </script>
                                    </div>
                                </div>
                                {{-- <div class="card">
                                    <div class="card-heading">
                                        <a data-toggle="collapse" data-target="#collapseFour">Size</a>
                                    </div>
                                    <div id="collapseFour" class="collapse show" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <div class="shop__sidebar__size">
                                                <label for="xs">xs
                                                    <input type="radio" id="xs">
                                                </label>
                                                <label for="sm">s
                                                    <input type="radio" id="sm">
                                                </label>
                                                <label for="md">m
                                                    <input type="radio" id="md">
                                                </label>
                                                <label for="xl">xl
                                                    <input type="radio" id="xl">
                                                </label>
                                                <label for="2xl">2xl
                                                    <input type="radio" id="2xl">
                                                </label>
                                                <label for="xxl">xxl
                                                    <input type="radio" id="xxl">
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                {{-- <div class="card">
                                    <div class="card-heading">
                                        <a data-toggle="collapse" data-target="#collapseFive">Màu Sắc</a>
                                    </div>
                                    <div id="collapseFive" class="collapse show" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <div class="shop__sidebar__color">
                                                <label class="c-1" for="sp-1">
                                                    <input type="radio" id="sp-1">
                                                </label>
                                                <label class="c-2" for="sp-2">
                                                    <input type="radio" id="sp-2">
                                                </label>
                                                <label class="c-3" for="sp-3">
                                                    <input type="radio" id="sp-3">
                                                </label>
                                                <label class="c-4" for="sp-4">
                                                    <input type="radio" id="sp-4">
                                                </label>
                                                <label class="c-5" for="sp-5">
                                                    <input type="radio" id="sp-5">
                                                </label>
                                                <label class="c-6" for="sp-6">
                                                    <input type="radio" id="sp-6">
                                                </label>
                                                <label class="c-7" for="sp-7">
                                                    <input type="radio" id="sp-7">
                                                </label>
                                                <label class="c-8" for="sp-8">
                                                    <input type="radio" id="sp-8">
                                                </label>
                                                <label class="c-9" for="sp-9">
                                                    <input type="radio" id="sp-9">
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="card">
                                    <div class="card-heading">
                                        <a data-toggle="collapse" data-target="#collapseSix">Tags</a>
                                    </div>
                                    <div id="collapseSix" class="collapse show" data-parent="#accordionExample">
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
                                            <script>
                                                let tagItems = document.querySelectorAll('.tag-item');
                                                tagItems.forEach(item => {
                                                    item.addEventListener('click', function(e) {
                                                        e.preventDefault();
                                                        let tag = this.textContent;
                                                        window.location.href = '/shop?tag=' + encodeURIComponent(tag.replace(' ', '-'));
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </div>
                                </div>
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
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="shop__product__option__right">
                                    <p>Sắp xếp theo</p>
                                    {{-- <ul class="list-unstyled">
                                        <li><a class="sort" href="">abc</a></li>
                                        <li><a href=""></a></li>
                                        <li><a href=""></a></li>
                                    </ul> --}}
                                    <select>
                                        <option class="sort" value="">Hàng mới về</option>
                                        <option class="sort" value="">Thấp tới cao</option>
                                        <option class="sort" value="">Cao tới thấp</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @foreach ($products as $items)
                            @php
                                $discountName = '';
                                if ($items->discount_id && $items->discount_id !== null) {
                                    $items->price = $items->price - $items->price * $items->Discount->percent_discount;
                                    $discountName = $items->Discount->name;
                                } else {
                                    $discountName = 'New';
                                }
                                $totalStock = 0;
                                if ($items->ProductVariants) {
                                    // Kiểm tra nếu có productVariants
                                    foreach ($items->ProductVariants as $variant) {
                                        if ($variant) {
                                            $totalStock += $variant->stock;
                                        }
                                    }
                                }
                            @endphp

                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="product__item" id="product-list-shop">
                                    <div class="product__item__pic">
                                        <img class="product__item__pic set-bg" width="280" height="250"
                                            src="{{ asset('uploads/' . $items->image) }}"
                                            alt="{{ $items->product_name }}">
                                        <span class="label name-discount-shop">{{ $discountName }}</span>
                                        <ul class="product__hover">
                                            <li><a href="{{ route('sites.addToWishList', $items->id) }}"><img
                                                        src="{{ asset('client/img/icon/heart.png') }}"
                                                        alt=""></a></li>
                                            <li><a href="#"><img src="{{ asset('client/img/icon/compare.png') }}"
                                                        alt=""><span>Compare</span></a></li>
                                            <li><a href="{{ url('product') }}/{{ $items->slug }}"><img
                                                        src="{{ asset('client/img/icon/search.png') }}"
                                                        alt=""></a></li>
                                        </ul>
                                    </div>

                                    <div class="product__item__text">
                                        <h6>{{ $items->product_name }}</h6>
                                        {{-- <a href="#" class="add-cart">+ Add To Cart</a> --}}
                                        @php
                                            if($totalStock == 0 ) {
                                                echo '<span class=" badge badge-warning">Hết hàng</span>';
                                            } else {
                                                echo '<a href="javascript:void(0);" class="add-cart" data-id="'. $items->id .'">+Add To Cart</a>';
                                            }
                                        @endphp
                                        <div class="rating">
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                        </div>
                                        <h5>{{ number_format($items->price) }} VND</h5>
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
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="product__pagination">
                                @if ($products->lastPage() > 1)
                                    @if ($products->onFirstPage())
                                        <a href="{{ url('shop') }}#product-list-shop" class="disabled">&laquo;</a>
                                    @else
                                        <a href="{{ $products->previousPageUrl() }}#product-list-shop">&laquo;</a>
                                    @endif

                                    @foreach ($products->links()->elements as $element)
                                        @if (is_array($element))
                                            @foreach ($element as $page => $url)
                                                <a href="{{ $url }}"
                                                    class="{{ $products->currentPage() == $page ? 'active' : '' }}">{{ $page }}</a>
                                            @endforeach
                                        @endif
                                    @endforeach

                                    @if ($products->hasMorePages())
                                        <a href="{{ $products->nextPageUrl() }}#product-list-shop">&raquo;</a>
                                    @else
                                        <a href="{{ url('shop') }}#product-list-shop" class="disabled">&raquo;</a>
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
    <script>
        document.querySelectorAll('.name-discount-shop').forEach(element => {
            if (element.textContent.trim() !== "New") {
                element.classList.add('bg-danger', 'text-white');
            }
        });
    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('client/css/cart-add.css') }}">
@endsection

{{-- @php
   dd($productRecentInfo);
@endphp --}}
@extends('sites.master')
@section('title', 'Trang chủ')
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

    <!-- Hero Section Begin -->
    <section class="hero">
        <div class="hero__slider owl-carousel">
            <div class="hero__items set-bg" data-setbg="{{ asset('client/img/hero/hero-2.jpg') }}">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-5 col-lg-7 col-md-8">
                            <div class="hero__text">
                                <h6>Bộ sưu tập mùa hè</h6>
                                <h2>Fall - Winter Collections 2025</h2>
                                <p>Một thương hiệu chuyên biệt tạo ra các sản phẩm thiết yếu sang trọng. Được chế tác một
                                    cách có đạo đức với cam kết không lay chuyển đối với chất lượng vượt trội.</p>
                                <a href="{{ route('sites.shop') }}" class="primary-btn">Mua ngay<span
                                        class="arrow_right"></span></a>
                                <div class="hero__social">
                                    <a href="https://www.facebook.com/?locale=vi_VN"><i class="fa fa-facebook"></i></a>
                                    <a href="https://x.com/?lang=vi"><i class="fa fa-twitter"></i></a>
                                    <a href="https://www.pinterest.com/"><i class="fa fa-pinterest"></i></a>
                                    <a href="https://www.instagram.com/"><i class="fa fa-instagram"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero__items set-bg" data-setbg="{{ asset('client/img/hero/hero-1.jpg') }}">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-5 col-lg-7 col-md-8">
                            <div class="hero__text">
                                <h6>Bộ sưu tập Thu Đông</h6>
                                <h2>Fall - Winter Collections 2025</h2>
                                <p>Một thương hiệu chuyên biệt tạo ra các sản phẩm thiết yếu sang trọng. Được chế tác một
                                    cách có đạo đức với cam kết không lay chuyển đối với chất lượng vượt trội.</p>
                                <a href="{{ route('sites.shop') }}" class="primary-btn">Mua ngay<span
                                        class="arrow_right"></span></a>
                                <div class="hero__social">
                                    <a href="https://www.facebook.com/?locale=vi_VN"><i class="fa fa-facebook"></i></a>
                                    <a href="https://x.com/?lang=vi"><i class="fa fa-twitter"></i></a>
                                    <a href="https://www.pinterest.com/"><i class="fa fa-pinterest"></i></a>
                                    <a href="https://www.instagram.com/"><i class="fa fa-instagram"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero Section End -->

    <!-- Banner Section Begin -->
    <section class="banner spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 offset-lg-4">
                    <div class="banner__item">
                        <div class="banner__item__pic">
                            <img src="{{ asset('client/img/banner/banner-3.jpg') }}" alt="">
                        </div>
                        <div class="banner__item__text">
                            <h2>Clothing Collections 2025</h2>
                            <a href="javascript:void(0);">Shop now</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="banner__item banner__item--middle">
                        <div class="banner__item__pic">
                            <img src="{{ asset('client/img/banner/banner-2.jpg') }}" alt="">
                        </div>
                        <div class="banner__item__text">
                            <h2>Accessories</h2>
                            <a href="javascript:void(0);">Shop now</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="banner__item banner__item--last">
                        <div class="banner__item__pic">
                            <img src="{{ asset('client/img/banner/banner-1.jpg') }}" alt="">
                        </div>
                        <div class="banner__item__text">
                            <h2>Shoes Spring 2025</h2>
                            <a href="javascript:void(0);">Shop now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Banner Section End -->

    <!-- Product Recently Section Begin -->
    @if (!empty($productRecentInfo) && count($productRecentInfo) > 0)
        <section class="product spad" id="product-recently-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <ul class="filter__controls">
                            <li>Sản Phẩm Bạn Đã Xem Gần Đây</li>
                        </ul>
                    </div>
                </div>


                <div class="row product__filter" id="product-recently-container">
                    @foreach ($productRecentInfo as $itemRecent)
                        @php
                            // Xử lý khuyến mãi
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
                                    <img src="{{ asset('uploads/' . $itemRecent->image) }}" class="set-bg" width="280"
                                        height="280" alt="{{ $itemRecent->product_name }}">
                                    <span class="label name-discount bg-danger text-white">{{ $discountName }}</span>
                                    <ul class="product__hover">
                                        <li>
                                            <a href="{{ url('add-to-wishlist/' . $itemRecent->id) }}"
                                                class="add-to-wishlist" title="Thêm vào danh sách yêu thích">
                                                <img src="{{ asset('client/img/icon/heart.png') }}" alt="">
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"><img src="{{ asset('client/img/icon/compare.png') }}"
                                                    alt="">
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
                                    {{-- <a href="javascript:void(0);" class="add-cart" data-id="{{ $itemRecent->id }}">
                                        + Add To Cart
                                    </a> --}}
                                    <div class="rating mt-2">
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                    </div>
                                    <h5>{{ number_format($itemRecent->price) }} đ</h5>
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
            </div>
        </section>
    @endif


    <!-- Product Section Recently End -->






    <!-- Product Discount Section Begin -->
    <section class="product spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="filter__controls">
                        <li>Hot Sales</li>
                    </ul>
                </div>
            </div>
            <div class="row product__filter" id="product-discount-container">
                <script>
                    async function fetchProductDiscount() {
                        try {
                            let response = await fetch("http://127.0.0.1:8000/api/product-discount");
                            let data = await response.json();
                            let products = data.data;
                            // console.log(products);

                            let container = document.querySelector('#product-discount-container');
                            container.innerHTML = "";

                            products.forEach((product, index) => {
                                let finalPrice;
                                // console.log(product.discount.percent_discount, product.discount.id);

                                if (product.discount.id != null) {
                                    finalPrice = product.price - (product.price * product.discount.percent_discount);

                                } else {

                                    finalPrice = product.price ?? 0;
                                    // console.log(finalPrice);
                                }

                                let formattedPrice = new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND'
                                }).format(finalPrice);

                                // console.log(product);
                                let totalStock = [];
                                let addCartOrNone = [];
                                product['product-variant'].map((variant) => {
                                    totalStock[variant.product_id] = 0;
                                })
                                product['product-variant'].forEach((variant) => {
                                    totalStock[variant.product_id] += variant.stock + 0;
                                });
                                product['product-variant'].map((variant) => {
                                    if (totalStock[variant.product_id] == 0) {
                                        addCartOrNone[variant.product_id] = false;
                                    } else {
                                        addCartOrNone[variant.product_id] = true;
                                    };
                                })



                                let productItem = document.createElement('div');
                                productItem.classList.add("col-lg-3", "col-md-6", "col-sm-6", "mix");
                                productItem.innerHTML = `
                                            <div class="product__item" id="product-list-home">
                                                  <div class="product__item__pic">
                                                        <img src="{{ asset('uploads/${product.image}') }}" class="set-bg" width="280" height="280" alt="${product.name}">
                                                        <span class="label name-discount" >${product.discount.name}</span>
                                                        <ul class="product__hover">
                                                          <li>
                                                                <a href="{{ url('add-to-wishlist') }}/${product.id}" class="add-to-wishlist" title="Thêm vào danh sách yêu thích">
                                                                    <img src="{{ asset('client/img/icon/heart.png') }}" alt="">
                                                                </a>
                                                        </li>

                                                            <li><a href="javascript:void(0);"><img src="{{ asset('client/img/icon/compare.png') }}" alt=""><span>Compare</span></a></li>
                                                            <li><a href="{{ url('product') }}/${product.slug}"><img src="{{ asset('client/img/icon/search.png') }}" alt=""></a></li>
                                                        </ul>
                                                 </div>
                                                <div class="product__item__text">
                                                    <h6>${product.name}</h6>` +

                                    (addCartOrNone[product.id] > 0 ?
                                        `<a href="javascript:void(0);" class="add-cart" data-id="${product.id}">+ Add To Cart</a>` :
                                        `<span class=" badge badge-warning">Hết hàng</span>`)

                                    +
                                    `<div class="rating">
                                                        <i class="fa fa-star-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                    </div>
                                                    <h5>${formattedPrice}</h5>
                                                    <div class="product__color__select">
                                                                                <label for="pc-${index * 3 + 1}">
                                                                                    <input type="radio" id="pc-${index * 3 + 1}">
                                                                                </label>
                                                                                <label class="active black" for="pc-${index * 3 + 2}">
                                                                                    <input type="radio" id="pc-${index * 3 + 2}">
                                                                                </label>
                                                                                <label class="grey" for="pc-${index * 3 + 3}">
                                                                                    <input type="radio" id="pc-${index * 3 + 3}">
                                                                                </label>
                                                                            </div>
                                                </div>
                                            </div>
                                        `;
                                container.appendChild(productItem);
                                if (document.querySelector('.name-discount')) {
                                    document.querySelectorAll('.name-discount').forEach(element => {
                                        element.classList.add('bg-danger', 'text-white');
                                    });

                                }
                            });
                        } catch (error) {
                            console.error("Lỗi API:", error);
                        }
                    }
                    fetchProductDiscount();
                </script>
            </div>
        </div>
    </section>
    <!-- Product Section End -->


    <!-- Product Section Begin -->
    <section class="product spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="filter__controls">
                        <li class="active" data-filter="*">Sản Phẩm Bán Chạy</li>
                        <li data-filter=".new-arrivals">Mới ra mắt</li>
                        <li data-filter=".hot-sales">Hot Sales</li>
                    </ul>
                </div>
            </div>
            <div class="row product__filter" id="product-client-container">
                <script>
                    async function fetchProduct() {
                        try {
                            let response = await fetch('http://127.0.0.1:8000/api/product-client');
                            let data = await response.json();
                            let products = data.data;

                            let container = document.querySelector('#product-client-container');
                            container.innerHTML = "";

                            products.forEach((product, index) => {
                                let finalPrice;
                                let nameDiscount = "";

                                if (product.discount_id != null) {
                                    finalPrice = product.price - (product.price * product.discount.percent_discount);
                                    nameDiscount = product.discount.name;
                                } else {
                                    finalPrice = product.price ?? 0;
                                    nameDiscount = "New";
                                }

                                let formattedPrice = new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND'
                                }).format(finalPrice);

                                let totalStock = [];
                                let addCartOrNone = [];
                                product['product_variants'].map((variant) => {
                                    totalStock[variant.product_id] = 0;
                                })
                                product['product_variants'].forEach((variant) => {
                                    totalStock[variant.product_id] += variant.stock + 0;
                                });
                                product['product_variants'].map((variant) => {
                                    if (totalStock[variant.product_id] == 0) {
                                        addCartOrNone[variant.product_id] = false;
                                    } else {
                                        addCartOrNone[variant.product_id] = true;
                                    };
                                })


                                let productItem = document.createElement('div');
                                productItem.classList.add("col-lg-3", "col-md-6", "col-sm-6", "mix", index % 2 === 0 ?
                                    "new-arrivals" : "hot-sales");
                                productItem.innerHTML = `
                                        <div class="product__item" id="product-list-home">
                                              <div class="product__item__pic">
                                                    <img src="{{ asset('uploads/${product.image}') }}" class="set-bg" width="280" height="280" alt="${product.product_name}">
                                                    <span class="label name-discount-section" >${nameDiscount}</span>
                                                    <ul class="product__hover">
                                                      <li>
                                                            <a href="{{ url('add-to-wishlist') }}/${product.id}" class="add-to-wishlist" title="Thêm vào danh sách yêu thích">
                                                                <img src="{{ asset('client/img/icon/heart.png') }}" alt="">
                                                            </a>
                                                    </li>

                                                        <li><a href="javascript:void(0);"><img src="{{ asset('client/img/icon/compare.png') }}" alt=""><span>Compare</span></a></li>
                                                        <li><a href="{{ url('product') }}/${product.slug}"><img src="{{ asset('client/img/icon/search.png') }}" alt=""></a></li>
                                                    </ul>
                                             </div>
                                            <div class="product__item__text">
                                                <h6>${product.product_name}</h6>
                                                ` +

                                    (addCartOrNone[product.id] > 0 ?
                                        `<a href="javascript:void(0);" class="add-cart" data-id="${product.id}">+ Add To Cart</a>` :
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
                                                <h5>${formattedPrice}</h5>
                                                <div class="product__color__select">
                                                                            <label for="pc-${index * 3 + 1}">
                                                                                <input type="radio" id="pc-${index * 3 + 1}">
                                                                            </label>
                                                                            <label class="active black" for="pc-${index * 3 + 2}">
                                                                                <input type="radio" id="pc-${index * 3 + 2}">
                                                                            </label>
                                                                            <label class="grey" for="pc-${index * 3 + 3}">
                                                                                <input type="radio" id="pc-${index * 3 + 3}">
                                                                            </label>
                                                                        </div>
                                            </div>
                                        </div>
                                    `;
                                container.appendChild(productItem);
                                document.querySelectorAll('.name-discount-section').forEach(element => {
                                    if (element.textContent.trim() !== "New") {
                                        element.classList.add('bg-danger', 'text-white');
                                    }
                                });

                            });
                        } catch (error) {
                            console.error("Lỗi API:", error);
                        }
                    }
                    fetchProduct();
                </script>
            </div>
        </div>
    </section>
    <!-- Product Section End -->

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



    <!-- Categories Section Begin -->
    <section class="categories spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="categories__text">
                        <h2>Quần Áo Chính Hàng<br /> <span>Bộ sưu tập của chúng tôi</span> <br /> Liên Kết</h2>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="categories__hot__deal">
                        <img src="{{ asset('client/img/product-sale.png') }}" alt="">
                        <div class="hot__deal__sticker">
                            <span>Sale Of</span>
                            <h5>$29.99</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 offset-lg-1">
                    <div class="categories__deal__countdown">
                        <span>Deal Of The Week</span>
                        <h2>Multi-pocket Chest Bag Black</h2>
                        <div class="categories__deal__countdown__timer" id="countdown">
                            <div class="cd-item">
                                <span>3</span>
                                <p>Days</p>
                            </div>
                            <div class="cd-item">
                                <span>1</span>
                                <p>Hours</p>
                            </div>
                            <div class="cd-item">
                                <span>50</span>
                                <p>Minutes</p>
                            </div>
                            <div class="cd-item">
                                <span>18</span>
                                <p>Seconds</p>
                            </div>
                        </div>
                        <a href="javascript:void(0);" class="primary-btn">Shop now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Categories Section End -->

    <!-- Instagram Section Begin -->
    <section class="instagram spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="instagram__pic">
                        <div class="instagram__pic__item set-bg"
                            data-setbg="{{ asset('client/img/instagram/instagram-1.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg"
                            data-setbg="{{ asset('client/img/instagram/instagram-2.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg"
                            data-setbg="{{ asset('client/img/instagram/instagram-3.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg"
                            data-setbg="{{ asset('client/img/instagram/instagram-4.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg"
                            data-setbg="{{ asset('client/img/instagram/instagram-5.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg"
                            data-setbg="{{ asset('client/img/instagram/instagram-6.jpg') }}"></div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="instagram__text">
                        <h2>Instagram</h2>
                        <p>Tìm hiểu thêm về chúng tôi qua instagram, cập nhật những thông tin và xu hướng thời trang mới
                            nhất !</p>
                        <h3>#TST_Fashion</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Instagram Section End -->

    <!-- Latest Blog Section Begin -->
    <section class="latest spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <span>Tin mới nhất</span>
                        <h2>Xu hướng thời trang</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach ($data as $model)
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="blog__item">
                            <div class="blog__item__pic set-bg" data-setbg="{{$model->image}}">
                            </div>
                            <div class="blog__item__text">
                                <span><img src="{{ asset('client/img/icon/calendar.png') }}"
                                        alt="">{{ $model->created_at }}</span>
                                <h5>{{ $model->title }}</h5>
                                <a href="{{ route('sites.blogDetail', $model->slug) }}">Đọc thêm</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Latest Blog Section End -->
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('client/css/cart-add.css') }}">
@endsection

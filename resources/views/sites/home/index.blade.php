@php
    // dd($productRecentInfo);
    $userId = auth()->guard('customer')->user()->id ?? 0;
    // dd($userId);
@endphp
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
            <div class="hero__items set-bg" data-setbg="{{ asset('client/img/hero/banner-home-2.jpg') }}">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-5 col-lg-7 col-md-8">
                            <div class="hero__text">
                                <h6>Bộ sưu tập mùa hè</h6>
                                <h2 class="text-white">Bộ sưu tập Thu Đông 2025</h2>
                                <p class="text-white">Một thương hiệu chuyên biệt tạo ra các sản phẩm thiết yếu sang trọng.
                                    Được chế tác một
                                    cách có đạo đức với cam kết không lay chuyển đối với chất lượng vượt trội.</p>
                                <a href="{{ route('sites.shop') }}" class="primary-btn">Mua ngay<span
                                        class="arrow_right"></span></a>
                                <div class="hero__social">
                                    <a href="https://www.facebook.com/?locale=vi_VN"><i
                                            class="fa fa-facebook text-white"></i></a>
                                    <a href="https://x.com/?lang=vi"><i class="fa fa-twitter text-white"></i></a>
                                    <a href="https://www.pinterest.com/"><i class="fa fa-pinterest text-white"></i></a>
                                    <a href="https://www.instagram.com/"><i class="fa fa-instagram text-white"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero__items set-bg" data-setbg="{{ asset('client/img/hero/banner-home-3.jpg') }}">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-5 col-lg-7 col-md-8">
                            <div class="hero__text">
                                <h6>Bộ sưu tập Thu Đông</h6>
                                <h2 class="text-white">Thu Đông Collections 2025</h2>
                                <p class="text-white">Một thương hiệu chuyên biệt tạo ra các sản phẩm thiết yếu sang trọng.
                                    Được chế tác một
                                    cách có đạo đức với cam kết không lay chuyển đối với chất lượng vượt trội.</p>
                                <a href="{{ route('sites.shop') }}" class="primary-btn">Mua ngay<span
                                        class="arrow_right"></span></a>
                                <div class="hero__social">
                                    <a href="https://www.facebook.com/?locale=vi_VN"><i
                                            class="fa fa-facebook text-white"></i></a>
                                    <a href="https://x.com/?lang=vi"><i class="fa fa-twitter text-white"></i></a>
                                    <a href="https://www.pinterest.com/"><i class="fa fa-pinterest text-white"></i></a>
                                    <a href="https://www.instagram.com/"><i class="fa fa-instagram text-white"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero__items set-bg" data-setbg="{{ asset('client/img/hero/banner-home-4.jpg') }}">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-5 col-lg-7 col-md-8">
                            <div class="hero__text">
                                <h6 class="text-white">Bộ sưu tập Xuân Hè</h6>
                                <h2 class="text-white">Xuân - Hè Collections 2025</h2>
                                <p class="text-white">Một thương hiệu chuyên biệt tạo ra các sản phẩm thiết yếu sang trọng.
                                    Được chế tác một
                                    cách có đạo đức với cam kết không lay chuyển đối với chất lượng vượt trội.</p>
                                <a href="{{ route('sites.shop') }}" class="primary-btn">Mua ngay<span
                                        class="arrow_right"></span></a>
                                <div class="hero__social">
                                    <a href="https://www.facebook.com/?locale=vi_VN"><i
                                            class="fa fa-facebook text-white"></i></a>
                                    <a href="https://x.com/?lang=vi"><i class="fa fa-twitter text-white"></i></a>
                                    <a href="https://www.pinterest.com/"><i class="fa fa-pinterest text-white"></i></a>
                                    <a href="https://www.instagram.com/"><i class="fa fa-instagram text-white"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero Section End -->



    <!-- Categories Section Begin -->
    <section class="categories spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="categories__text">
                        <h2>{{ $highestDiscountProduct->category->category_name ?? 'Danh mục' }}<br />
                            <span>{{ $highestDiscountProduct->product_name ?? 'Sản Phẩm' }}</span> <br /> Liên Kết
                        </h2>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="categories__hot__deal">
                        @if ($highestDiscountProduct)
                            <img src="{{ asset($highestDiscountProduct->image) }}"
                                alt="{{ $highestDiscountProduct->product_name }}">
                            <div class="hot__deal__sticker">
                                <span>Giảm {{ $highestDiscountProduct->discount->percent_discount * 100 }}%</span>
                                <h5>{{ number_format($highestDiscountProduct->price * (1 - $highestDiscountProduct->discount->percent_discount), 0, ',', '.') }}đ
                                </h5>
                            </div>
                        @else
                            <img src="{{ asset('client/img/product-sale.png') }}" alt="">
                            <div class="hot__deal__sticker">
                                <span>Sale Of</span>
                                <h5>$29.99</h5>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4 offset-lg-1">
                    <div class="categories__deal__countdown">
                        <span>Sản phẩm có deal tốt!</span>
                        @if ($highestDiscountProduct)
                            <h2>{{ $highestDiscountProduct->name }}</h2>
                        @else
                            <h2>Multi-pocket Chest Bag Black</h2>
                        @endif
                        <div class="categories__deal__countdown__timer" id="countdown"
                            @if ($highestDiscountProduct && $highestDiscountProduct->discount) data-enddate="{{ $highestDiscountProduct->discount->formatted_end_date }}" @endif>
                            <div class="cd-item">
                                <span>3</span>
                                <p>Ngày</p>
                            </div>
                            <div class="cd-item">
                                <span>1</span>
                                <p>Giờ</p>
                            </div>
                            <div class="cd-item">
                                <span>50</span>
                                <p>Phút</p>
                            </div>
                            <div class="cd-item">
                                <span>18</span>
                                <p>Giây</p>
                            </div>
                        </div>
                        @if ($highestDiscountProduct)
                            <a href="{{ route('sites.productDetail', $highestDiscountProduct->slug) }}"
                                class="primary-btn">Mua ngay</a>
                        @else
                            <a href="javascript:void(0);" class="primary-btn">Shop now</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Categories Section End -->




    <!-- Product Recently Section Begin -->
    @if (!empty($productRecentInfo) && count($productRecentInfo) > 0)
        <hr class="w-50">
        <section class="product spad " id="product-recently-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <ul class="filter__controls">
                            <li class="text-dark">Sản Phẩm Bạn Đã Xem Gần Đây</li>
                        </ul>
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
                                        $totalStock += $variant->available_stock;
                                    }
                                }
                            }
                        @endphp
                        <div class="col-lg-3 col-md-6 col-sm-6 mix">
                            <div class="product__item">
                                <div class="product__item__pic pulse">
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
                                    {{-- <h6>{{ $itemRecent->comments->avg('star') ?? '' }}</h6>
                                    <div class="rating mt-2">
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                    </div> --}}
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
                                        <span class="text-muted">
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
        <hr class="w-50">
    @endif
    <!-- Product Section Recently End -->




    <!-- Product RecommendationProduct For UserBase Content Filtering Section Begin -->
    {{-- @if ($userId != 0)
        <section class="product spad">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <ul class="filter__controls">
                            <li class="text-dark">Sản phẩm có thể bạn sẽ thích</li>
                        </ul>
                    </div>
                </div>

                <div class="row product__filter-ubcf" id="product-ubcf-container">
                    <script>
                        let userIdCurrent = @json($userId);
                        async function fetchProductUBCF() {
                            try {

                                let response = await fetch(`http://127.0.0.1:8000/api/recommend/user/${userIdCurrent}`);
                                let data = await response.json();
                                let products = data.data;
                                // console.log(products);

                                let container = document.querySelector('#product-ubcf-container');
                                container.innerHTML = "";
                                let nameDiscount = "";
                                products.forEach((product, index) => {
                                    let finalPrice;
                                    // console.log(product.discount.percent_discount, product.discount.id);
                                    if (product.discount && product.discount.id != null) {
                                        finalPrice = product.price - (product.price * product.discount.percent_discount);
                                        nameDiscount = product.discount.name;
                                    } else {
                                        finalPrice = product.price ?? 0;
                                        nameDiscount = "New";
                                        // console.log(finalPrice);
                                    }

                                    let formattedPrice = new Intl.NumberFormat('vi-VN', {
                                        style: 'currency',
                                        currency: 'VND'
                                    }).format(finalPrice);

                                    // console.log(product);

                                    const showOriginalPrice = product.discount_id !== null && product.discount;
                                    let formattedOriginalPrice = new Intl.NumberFormat('vi-VN', {
                                        style: 'currency',
                                        currency: 'VND'
                                    }).format(product.price);

                                    // console.log(product);
                                    let totalStock = [];
                                    let addCartOrNone = [];
                                    product['product-variant'].map((variant) => {
                                        totalStock[variant.product_id] = 0;
                                    })
                                    product['product-variant'].forEach((variant) => {
                                        totalStock[variant.product_id] += variant.available_stock + 0;
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
                                                  <div class="product__item__pic pulse">
                                                        <img src="${product.image}" class="set-bg" width="280" height="280" alt="${product.name}">
                                                        <span class="label name-ubcf" >${nameDiscount}</span>
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
                                        `
                                            <div class="rating" >
                                                ${generateStarRating(product.star)}
                                                <span class="text-muted"> (${product.comments_count})</span>
                                            </div>

                                                    <h5>${formattedPrice}</h5>
                                                    <h6 class="text-muted original-price-begin" style="text-decoration: line-through; display: ${showOriginalPrice ? 'block' : 'none'};">${formattedOriginalPrice}</h6>
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
                                    document.querySelectorAll('.name-ubcf').forEach(element => {
                                        if (element.textContent.trim() !== "New") {
                                            element.classList.add('bg-danger', 'text-white');
                                        }
                                    });

                                    // if(userIdCurrent!==null && ){
                                    //     document.querySelector('.product spad').classList.remove('d-none');
                                    // }
                                });
                            } catch (error) {
                                console.error("Lỗi API:", error);
                            }
                        }
                        fetchProductUBCF();
                    </script>
                </div>
            </div>
        </section>
        <hr class="w-50">
    @endif --}}
    <!-- RecommendationProduct For UserBase Content Filtering Section End -->


    <!-- Product Discount Section Begin -->
    <section class="product spad mt-2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="filter__controls">
                        <li class="text-dark">Sản phẩm đang khuyến mãi</li>
                    </ul>
                </div>
            </div>
            <div class="row product__filter-discount" id="product-discount-container">
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

                                let formattedOriginalPrice = new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND'
                                }).format(product.price);

                                // console.log(product);
                                let totalStock = [];
                                let addCartOrNone = [];
                                product['product-variant'].map((variant) => {
                                    totalStock[variant.product_id] = 0;
                                })
                                product['product-variant'].forEach((variant) => {
                                    totalStock[variant.product_id] += variant.available_stock + 0;
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
                                            <div class="product__item  pulse" id="product-list-home">
                                                  <div class="product__item__pic">
                                                        <img src="${product.image}" class="set-bg" width="280" height="280" alt="${product.name}">
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
                                    `
                                                <div class="rating" >
                                                    ${generateStarRating(product.star)}
                                                    <span class="text-muted"> (${product.comments_count})</span>
                                                </div>


                                                    <h5>${formattedPrice}</h5>
                                                    <h6 class="text-muted original-price" style="text-decoration: line-through; display: none;">${formattedOriginalPrice}</h6>
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
                                if (document.querySelector('.original-price')) {
                                    document.querySelectorAll('.original-price').forEach(element => {
                                        if (product.discount && product.discount.id != null) {
                                            element.style.display = 'block';
                                        }
                                    })
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
    <hr class="w-50">



    <!-- Product Section Begin -->
    <section class="product spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="filter__controls">
                        <li class="active" data-filter="*">Tất cả sản phẩm</li>
                        {{-- <li data-filter=".new-arrivals">Mới ra mắt</li> --}}
                        <li data-filter=".hot-sales">Mới ra mắt</li>
                    </ul>
                </div>
            </div>
            <div class="row product__filter" id="product-client-container">
            </div>
            <!-- Pagination -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="product__pagination" id="pagination-container">
                        <!-- Pagination links will be inserted here -->
                    </div>
                </div>
            </div>
            <script>
                let currentPage = 1;
                const itemsPerPage = 8;

                async function fetchProduct(page = 1) {
                    try {
                        let response = await fetch(`http://127.0.0.1:8000/api/product-client?page=${page}`);
                        let data = await response.json();
                        let products = data.data;

                        let container = document.querySelector('#product-client-container');
                        container.innerHTML = "";
                        products.forEach((product, index) => {
                            let finalPrice;
                            let nameDiscount = "";
                            // console.log(product);

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

                            let formattedPriceOld = new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(product.price);

                            // Logic xử lý stock (giữ nguyên)
                            let totalStock = [];
                            let addCartOrNone = [];
                            product['product_variants'].map((variant) => {
                                totalStock[variant.product_id] = 0;
                            })
                            product['product_variants'].forEach((variant) => {
                                totalStock[variant.product_id] += variant.available_stock + 0;
                            });
                            product['product_variants'].map((variant) => {
                                if (totalStock[variant.product_id] == 0) {
                                    addCartOrNone[variant.product_id] = false;
                                } else {
                                    addCartOrNone[variant.product_id] = true;
                                };
                            })

                            let productItem = document.createElement('div');
                            productItem.classList.add("col-lg-3", "col-md-6", "col-sm-6");
                            productItem.setAttribute("data-category", index % 2 === 0 ? "new-arrivals" : "hot-sales");

                            // Kiểm tra xem có khuyến mãi không để quyết định hiển thị giá gốc
                            const showOriginalPrice = product.discount_id !== null && product.discount;

                            productItem.innerHTML = `
                                <div class="product__item" id="product-list-home">
                                    <div class="product__item__pic pulse">
                                        <img src="${product.image}" class="set-bg" width="280" height="280" alt="${product.product_name}">
                                        <span class="label name-discount-section">${nameDiscount}</span>
                                        <ul class="product__hover">
                                            <li>
                                                <a href="{{ url('add-to-wishlist') }}/${product.id}" class="add-to-wishlist" title="Thêm vào danh sách yêu thích">
                                                    <img src="{{ asset('client/img/icon/heart.png') }}" alt="">
                                                </a>
                                            </li>
                                            <li><a href="javascript:void(0);"><img src="{{ asset('client/img/icon/compare.png') }}" alt=""><span>Compare</span></a></li>
                                            <li><a href="{{ url('product') }}/${product.slug}"><img src="{{ asset('client/img/icon/search.png') }}" alt=""></a></li>
                                        </ul>
                                    </div>
                                    <div class="product__item__text">
                                        <h6>${product.product_name}</h6>
                                        ` + (addCartOrNone[product.id] ?
                                `<a href="javascript:void(0);" class="add-cart" data-id="${product.id}">+ Add To Cart</a>` :
                                `<span class="badge badge-warning">Hết hàng</span>`) + `
                                         <div class="rating" >
                                                    ${generateStarRating(product.star)}
                                                    <span class="text-muted"> (${product.comments_count})</span>
                                                </div>

                                        <h5>${formattedPrice}</h5>
                                        <h6 class="text-muted original-price-begin" style="text-decoration: line-through; display: ${showOriginalPrice ? 'block' : 'none'};">${formattedPriceOld}</h6>
                                        <div class="product__color__select">
                                            <label for="pc-${index * 3 + 1}"><input type="radio" id="pc-${index * 3 + 1}"></label>
                                            <label class="active black" for="pc-${index * 3 + 2}"><input type="radio" id="pc-${index * 3 + 2}"></label>
                                            <label class="grey" for="pc-${index * 3 + 3}"><input type="radio" id="pc-${index * 3 + 3}"></label>
                                        </div>
                                    </div>
                                </div>
                            `;
                            container.appendChild(productItem);
                        });

                        // Gắn class màu cho nhãn discount
                        document.querySelectorAll('.name-discount-section').forEach(el => {
                            if (el.textContent.trim() !== "New") {
                                el.classList.add('bg-danger', 'text-white');
                            }
                        });


                        // Cập nhật phân trang
                        updatePagination(data.total, data.per_page, data.current_page);

                    } catch (error) {
                        console.error("Lỗi API:", error);
                    }
                }

                function updatePagination(totalItems, perPage, currentPage) {
                    const totalPages = Math.ceil(totalItems / perPage);
                    const paginationContainer = document.getElementById('pagination-container');
                    paginationContainer.innerHTML = '';

                    if (totalPages <= 1) return;

                    function createPageLink(page, label = null, isActive = false, isEllipsis = false) {
                        const link = document.createElement('a');
                        link.href = 'javascript:void(0);';
                        link.textContent = label ?? page;

                        if (isActive) link.classList.add('active');
                        if (isEllipsis) link.classList.add('disabled');

                        if (!isEllipsis && !isActive) {
                            link.addEventListener('click', () => fetchProduct(page));
                        }

                        return link;
                    }

                    if (currentPage > 1) {
                        paginationContainer.appendChild(createPageLink(currentPage - 1, '<'));
                    }

                    const pages = [];
                    if (totalPages <= 5) {
                        for (let i = 1; i <= totalPages; i++) pages.push(i);
                    } else {
                        pages.push(1);
                        if (currentPage > 3) pages.push('...');
                        const start = Math.max(2, currentPage - 1);
                        const end = Math.min(totalPages - 1, currentPage + 1);
                        for (let i = start; i <= end; i++) pages.push(i);
                        if (currentPage < totalPages - 2) pages.push('...');
                        pages.push(totalPages);
                    }

                    pages.forEach(page => {
                        if (page === '...') {
                            const ellipsis = document.createElement('span');
                            ellipsis.textContent = '...';
                            ellipsis.classList.add('disabled');
                            paginationContainer.appendChild(ellipsis);
                        } else {
                            paginationContainer.appendChild(createPageLink(page, null, page === currentPage));
                        }
                    });

                    if (currentPage < totalPages) {
                        paginationContainer.appendChild(createPageLink(currentPage + 1, '>'));
                    }
                }


                // Xử lý lọc sản phẩm
                document.querySelectorAll('.filter__controls li').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        document.querySelectorAll('.filter__controls li').forEach(el => el.classList.remove(
                            'active'));
                        btn.classList.add('active');

                        const filter = btn.getAttribute('data-filter'); // '*', '.new-arrivals', '.hot-sales'
                        const products = document.querySelectorAll('#product-client-container > div');

                        products.forEach((el) => {
                            const category = el.getAttribute('data-category');
                            if (filter === '*' || category === filter.replace('.', '')) {
                                el.style.display = 'block';
                            } else {
                                el.style.display = 'none';
                            }
                        });
                    });
                });

                fetchProduct();
            </script>

        </div>

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
    </section>
    <!-- Product Section End -->


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

    <!-- Toast container -->
    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>


    <hr class="w-50">
    <!-- Banner Section Begin -->
    <section class="banner spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 offset-lg-4">
                    <div class="banner__item">
                        <div class="banner__item__pic">
                            <img src="{{ asset('client/img/banner/banner-3-new.png') }}" alt="">
                        </div>
                        <div class="banner__item__text">
                            <h2 class="text-secondary">Bộ sưu tập mùa hè 2025</h2>
                            <a href="javascript:void(0);">Mua Ngay</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="banner__item banner__item--middle">
                        <div class="banner__item__pic">
                            <img src="{{ asset('client/img/banner/banner-1-new.jpg') }}" alt="">
                        </div>
                        <div class="banner__item__text">
                            <h2>Liên Kết</h2>
                            <a href="javascript:void(0);">Mua Ngay</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="banner__item banner__item--last">
                        <div class="banner__item__pic">
                            <img src="{{ asset('client/img/banner/banner-4-new.jpg') }}" alt="">
                        </div>
                        <div class="banner__item__text">
                            <h2 class="text-secondary">Bộ sưu tập Thu Đông</h2>
                            <a href="javascript:void(0);">Mua Ngay</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Banner Section End -->

    <hr class="w-50">


    <!-- Instagram Section Begin -->
    <section class="instagram spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="instagram__text">
                        <h2>Instagram</h2>
                        <p>Tìm hiểu thêm về chúng tôi qua instagram, cập nhật những thông tin và xu hướng thời trang mới
                            nhất để nhanh chóng !</p>
                        <h3>#TFashionShop</h3>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="instagram__pic">

                        <div class="instagram__pic__item set-bg"
                            data-setbg="{{ asset('client/img/instagram/banner-home-ig.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg"
                            data-setbg="{{ asset('client/img/instagram/banner-home-ig-3.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg"
                            data-setbg="{{ asset('client/img/instagram/banner-home-ig-2.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg"
                            data-setbg="{{ asset('client/img/instagram/banner-home-ig-6.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg"
                            data-setbg="{{ asset('client/img/instagram/banner-home-ig-4.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg"
                            data-setbg="{{ asset('client/img/instagram/banner-home-ig-5.jpg') }}"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Instagram Section End -->

    <hr class="w-50">

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
                            <div class="blog__item__pic set-bg" data-setbg="{{ $model->image }}">
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
            <div class="d-flex justify-content-center">{{ $data->links() }}</div>
        </div>
    </section>
    <!-- Latest Blog Section End -->
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('client/css/cart-add.css') }}">

    <style>
        .product__item:hover .product__item__pic {
            transform: scale(1.01);
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 rgba(249, 0, 0, 0.1);
            }

            100% {
                transform: scale(1.03);
                box-shadow: 0 5px 15px rgba(255, 0, 0, 0.2);
            }
        }

        .pulse:hover {
            animation: pulse 2s infinite;
        }
    </style>
@endsection

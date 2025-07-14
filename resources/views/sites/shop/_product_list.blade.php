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
                    <a href="{{ $products->previousPageUrl() }}#product-list-shop" class="page-link-ajax">&laquo;</a>
                @endif

                @if ($products->currentPage() > 3)
                    <a href="{{ $products->url(1) }}#product-list-shop" class="page-link-ajax">1</a>
                    @if ($products->currentPage() > 4)
                        <span class="dots">...</span>
                    @endif
                @endif

                @foreach (range(1, $products->lastPage()) as $i)
                    @if ($i >= $products->currentPage() - 2 && $i <= $products->currentPage() + 2)
                        @if ($i == $products->currentPage())
                            <a class="active">{{ $i }}</a>
                        @else
                            <a href="{{ $products->url($i) }}#product-list-shop" class="page-link-ajax">{{ $i }}</a>
                        @endif
                    @endif
                @endforeach

                @if ($products->currentPage() < $products->lastPage() - 2)
                    @if ($products->currentPage() < $products->lastPage() - 3)
                        <span class="dots">...</span>
                    @endif
                    <a href="{{ $products->url($products->lastPage()) }}#product-list-shop" class="page-link-ajax">{{ $products->lastPage() }}</a>
                @endif

                @if ($products->hasMorePages())
                    <a href="{{ $products->nextPageUrl() }}#product-list-shop" class="page-link-ajax">&raquo;</a>
                @else
                    <a class="disabled">&raquo;</a>
                @endif
            @endif
        </div>
    </div>
</div>

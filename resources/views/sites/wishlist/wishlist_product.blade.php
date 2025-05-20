@extends('sites.master')

@section('title', 'Danh sách yêu thích')

@section('content')

<section class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__text">
                    <h4>Wishlist</h4>
                    <div class="breadcrumb__links">
                        <a href="{{ route('sites.home') }}">Home</a>
                        <span>Danh sách yêu thích</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h5 mb-0">Sản phẩm yêu thích</h2>
            <span class="badge bg-primary rounded-pill">
                {{ Session::has('wishlist') ? count(Session::get('wishlist')) : 0 }} sản phẩm
            </span>
        </div>

        @if (Session::has('wishlist') && count(Session::get('wishlist')) > 0)
            <div class="row g-4">
                @foreach (Session::get('wishlist') as $item)
                <div class="col-12">
                    <div class="card card-product card-hover shadow-sm">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col-md-2 col-4">
                                    <a href="{{ url('product/'.$item->slug) }}">
                                        <img src="{{ asset('uploads/' . $item->image) }}"
                                             alt="{{ $item->name }}"
                                             class="img-fluid rounded-3 object-fit-cover"
                                             style="width: 120px; height: 120px;">
                                    </a>
                                </div>
                                <div class="col-md-6 col-8">
                                    <h3 class="h6 mb-1">
                                        <a href="{{ url('product/'.$item->slug) }}" class="text-dark">{{ $item->name }}</a>
                                    </h3>
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        <span class="badge bg-light text-dark border">
                                            <small>Mã: {{ $item->id }}</small>
                                        </span>
                                        @if($item->color)
                                        <span class="badge bg-light text-dark border">
                                            <small>Màu: {{ $item->color }}</small>
                                        </span>
                                        @endif
                                        @if($item->size)
                                        <span class="badge bg-light text-dark border">
                                            <small>Size: {{ $item->size }}</small>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-12 mt-3 mt-md-0">
                                    <div class="d-flex justify-content-md-end gap-2">
                                        <a href="{{ route('sites.removefromWishList', $item->id) }}"
                                           class="btn btn-outline-danger btn-sm px-3"
                                           data-bs-toggle="tooltip"
                                           title="Xóa khỏi wishlist">
                                           <i class="bi bi-heartbreak-fill"></i> Xóa
                                        </a>
                                        <a href="{{ url('product/'.$item->slug) }}"
                                           class="btn btn-primary btn-sm px-3">
                                           <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-4 text-center">
                <a href="{{ url('/') }}#product-list-home" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
                </a>
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#ddd" class="bi bi-heart" viewBox="0 0 16 16">
                        <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                    </svg>
                </div>
                <h5 class="mb-3">Wishlist của bạn đang trống</h5>
                <p class="text-muted mb-4">Nhấn vào ♥ để thêm sản phẩm vào danh sách yêu thích</p>
                <a href="{{ url('/') }}#product-list-home" class="btn btn-primary px-4">
                    <i class="bi bi-bag me-2"></i> Khám phá sản phẩm
                </a>
            </div>
        @endif
    </div>
</section>

@endsection

@section('css')
<style>
    .card-product {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-product:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    .object-fit-cover {
        object-fit: cover;
    }
</style>
@endsection

@section('scripts')
<script>
    // Kích hoạt tooltip
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    })
</script>
@endsection

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

<div class="container mt-5">
    <h2 class="text-center mb-4">YÊU THÍCH</h2>

    @if (Session::has('wishlist') && count(Session::get('wishlist')) > 0)
        <div class="list-group">
            @foreach (Session::get('wishlist') as $item)
            <div class="list-group-item d-flex align-items-center border rounded shadow-sm p-3 mb-3">
                <!-- Hình ảnh sản phẩm -->
                <div class="flex-shrink-0">
                    <a href="{{ url('product/'.$item->slug) }}">
                        <img src="{{ asset('uploads/' . $item->image) }}" 
                            alt="{{ $item->name }}" 
                            class="rounded shadow-sm" 
                            style="width: 280px; height: 280px;">
                    </a>
                </div>

                <!-- Thông tin sản phẩm -->
                <div class="flex-grow-1 ms-4">
                    <h5 class="mb-2">{{ $item->name }}</h5>
                    <p class="mb-1"><strong>Mã sản phẩm:</strong> {{ $item->id }}</p>
                    <p class="mb-1"><strong>Màu sắc:</strong> {{ $item->color }}</p>
                    <p class="mb-1"><strong>Kích cỡ:</strong> {{ $item->size }}</p>
                </div>

                <!-- Nút xóa -->
                <div class="text-end">
                    <a href="{{route('sites.removefromWishList', $item->id)}}" class="btn btn-outline-danger remove-wishlist px-3 py-2"">❌ Xóa</a>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center mb-5">
            <p class="text-muted">Không có sản phẩm nào trong danh sách mong muốn của bạn.</p>
            <p>Nhấn vào ❤️ để thêm sản phẩm vào wishlist!</p>
            <a href="{{ url('/') }}#product-list-home" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
    @endif
</div>
@endsection


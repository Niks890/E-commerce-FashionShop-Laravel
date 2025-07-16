@extends('sites.master')
@section('title', 'Chi tiết đơn hàng')

@section('content')
@if (Session::has('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-lg p-3 mt-3 mx-auto text-center" style="max-width: 500px;">
        <i class="fas fa-check-circle me-2"></i> {{ Session::get('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="container py-4">
    <h3 class="text-center mb-4 text-primary fw-bold">Chi Tiết Đơn Hàng #{{$orderDetail[0]->id}}</h3>

    {{-- <div class="text-end mb-3">
        <a href="{{ route('order.invoice', $orderDetail[0]->id) }}" class="btn btn-outline-danger">
            <i class="fa fa-file-pdf me-1"></i> Xuất hóa đơn PDF
        </a>
    </div> --}}

    <div class="row g-4">
        <!-- Thông tin khách hàng -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="text-success text-center mb-3">Thông Tin Khách Hàng</h5>
                    <p><strong>Khách Hàng:</strong> {{$orderDetail[0]->customer_name}}</p>
                    <p><strong>Điện Thoại:</strong> {{$orderDetail[0]->phone}}</p>
                    <p><strong>Email:</strong> {{$orderDetail[0]->email}}</p>
                    <p><strong>Địa Chỉ:</strong> {{$orderDetail[0]->address}}</p>
                </div>
            </div>
        </div>

        <!-- Thông tin đơn hàng -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="text-success text-center mb-3">Thông Tin Đơn Hàng</h5>
                    <p><strong>Mã Đơn Hàng:</strong> {{$orderDetail[0]->id}}</p>
                    <p><strong>Ngày Đặt:</strong> {{\Carbon\Carbon::parse($orderDetail[0]->created_at)->format('d/m/Y H:i')}}</p>
                    <p><strong>Thanh Toán:</strong> {{$orderDetail[0]->payment}}</p>
                    <p><strong>Ghi Chú:</strong> {{$orderDetail[0]->note}}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách sản phẩm -->
    <div class="table-responsive mt-5">
        <h5 class="text-center text-success mb-3">Sản Phẩm Trong Đơn Hàng</h5>
        <table class="table table-bordered align-middle text-center shadow-sm">
            <thead class="table-success">
                <tr>
                    <th>#</th>
                    <th>Sản Phẩm</th>
                    <th>SKU</th>
                    <th>Ảnh</th>
                    <th>Số Lượng</th>
                    <th>Size / Màu</th>
                    <th>Đơn Giá</th>
                    <th>Trạng Thái</th>
                    <th>Thành Tiền</th>
                </tr>
            </thead>
            <tbody>
            @php
                $i = 1;
                $total = 0;
                $vat = 0.1;
                $ship = 30000;
                $totalPriceCart = 0;
                $discount = $orderDetail[0]->code ?? 0;
            @endphp
            @foreach ($orderDetail as $items)
                @php
                    $totalPriceCart += ($items->quantity * $items->price) * (1 - $discount);
                    if($totalPriceCart >= 500000){
                        $ship = 0;
                    }
                    $vatPrice = $totalPriceCart * $vat;
                    $total = $totalPriceCart + $vatPrice + $ship;
                @endphp
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$items->product_name}}</td>
                    <td>{{$items->sku}}</td>
                    <td><img src="{{$items->image}}" width="50" class="rounded"></td>
                    <td>{{$items->quantity}}</td>
                    <td>{{$items->size}} - {{$items->color}}</td>
                    <td>{{number_format($items->price, 0, ',', '.')}} đ</td>
                    <td><span class="badge bg-light text-danger fw-bold">{{$items->status}}</span></td>
                    <td>{{number_format($items->quantity * $items->price, 0, ',', '.')}} đ</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Tổng kết đơn hàng -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="border p-4 rounded shadow-sm bg-light d-flex flex-wrap justify-content-between align-items-center gap-3">
                <p class="mb-0"><strong>Tạm Tính:</strong> {{number_format($totalPriceCart, 0, ',', '.')}} đ</p>
                <p class="mb-0"><strong>VAT (10%):</strong> {{number_format($vatPrice, 0, ',', '.')}} đ</p>
                <p class="mb-0"><strong>Phí Ship:</strong> {{number_format($ship, 0, ',', '.')}} đ</p>
                <p class="mb-0"><strong>Chiết Khấu:</strong> {{$discount * 100}}%</p>
                <p class="mb-0 fs-5"><strong class="text-danger">Tổng Cộng:</strong> {{number_format($total, 0, ',', '.')}} đ</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/message.css') }}">
@endsection

@section('js')
@if (Session::has('success'))
<script src="{{ asset('assets/js/message.js') }}"></script>
@endif
@endsection

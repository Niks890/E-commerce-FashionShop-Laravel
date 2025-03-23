@extends('sites.master')
@section('title', 'Chi tiết đơn hàng')
@section('content')
    @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-lg p-3 mt-3 mx-auto text-center" style="max-width: 500px;">
            <i class="fas fa-check-circle me-2"></i> {{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="container">
        <h3 class="text-center">Đơn Hàng: #{{$orderDetail[0]->id}}</h3>
        <a href="{{ route('order.invoice', $orderDetail[0]->id) }}" class="btn btn-primary mb-3">
            <i class="fa fa-file-pdf"></i> Xuất hóa đơn PDF
        </a>        
        <div class="row">
            <!-- Thông tin khách hàng -->
            <div class="col-md-6">
                <div class="card shadow-sm p-3">
                    <h4 class="text-success text-center">Thông Tin Khách Hàng</h4>
                    <p><strong>Khách Hàng: </strong>{{$orderDetail[0]->customer_name}} </p>
                    <p><strong>Số Điện Thoại: </strong>{{$orderDetail[0]->phone}} </p>
                    <p><strong>Địa Chỉ Email:</strong>{{$orderDetail[0]->email}} </p>
                    <p><strong>Địa Chỉ Nhận Hàng: </strong>{{$orderDetail[0]->address}} </p>
                </div>
            </div>

            <!-- Thông tin đơn hàng -->
            <div class="col-md-6">
                <div class="card shadow-sm p-3">
                    <h4 class="text-success text-center">Thông Tin Đơn Hàng</h4>
                    <p><strong>Mã Đơn Hàng: </strong>{{$orderDetail[0]->id}} </p>
                    <p><strong>Ngày Đặt: </strong>{{$orderDetail[0]->created_at}} </p>
                    <p><strong>Phương Thức Thanh Toán: </strong>{{$orderDetail[0]->payment}} </p>
                    <p><strong>Ghi Chú: </strong>{{$orderDetail[0]->note}}</p>
                </div>
            </div>
        </div>

        <!-- Danh sách sản phẩm -->
        <div class="table-responsive mt-4">
            <h4 class="text-center text-success">Danh Sách Sản Phẩm</h4>
            <table class="table table-bordered text-center mt-3">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Tên Sản Phẩm</th>
                        <th>Hình Ảnh</th>
                        <th>Số Lượng</th>
                        <th>Size & Màu Sắc</th>
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
                        <td><img src="{{asset('uploads/'.$items->image)}}" width="50" class="rounded"></td>
                        <td>{{$items->quantity}}</td>
                        <td>{{$items->size.' - '.$items->color}}</td>
                        <td class="text-nowrap">{{number_format($items->price, 0, ',', '.')}} đ</td>
                        <td class="text-danger fw-bold bg-light">{{$items->status}}</td>
                        <td>{{number_format($items->quantity * $items->price, 0, ',', '.')}} đ</td>
                    </tr>
                    @endforeach
                </tbody>
               
            </table>
        </div>

        <!-- Thông tin tổng giá trị đơn hàng -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="border p-3 rounded shadow-sm d-flex justify-content-between align-items-center">
                    <p><strong>Tạm Tính: </strong>{{number_format($totalPriceCart, 0, ',', '.')}} đ</p>
                    <p><strong>Phí Vận Chuyển: </strong>{{number_format($ship, 0, ',', '.')}} đ</p>
                    <p><strong>Chiết Khấu: </strong>{{$items->code*100}} %</p>
                    <p class="fs-5"><strong class="text-danger">Tổng Giá Trị Đơn Hàng: </strong>{{number_format($total, 0, ',', '.')}}đ</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
@endsection

@section('js')
    @if (Session::has('success'))
        <script src="{{ asset('assets/js/message.js') }}"></script>
    @endif
@endsection

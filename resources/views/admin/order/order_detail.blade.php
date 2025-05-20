@can('salers')
@extends('admin.master')
@section('title', "Đơn hàng #".$data[0]->id)

@section('css')
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
    <style>
        body, .container {
            font-family: 'Poppins', sans-serif;
        }
        h2, h4, h5 {
            font-weight: 600;
            color: #2c3e50;
        }
        .btn-pdf {
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
            color: #fff;
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(244, 67, 54, 0.4);
            transition: all 0.3s ease;
        }
        .btn-pdf:hover {
            box-shadow: 0 6px 20px rgba(244, 67, 54, 0.7);
            transform: translateY(-3px);
            color: #fff;
        }
        .card-custom {
            border-radius: 16px;
            box-shadow: 0 6px 15px rgb(0 0 0 / 0.1);
            padding: 2rem 2.5rem;
            background-color: #fff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgb(0 0 0 / 0.15);
        }
        .section-title {
            position: relative;
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0.5rem;
            color: #27ae60;
            font-weight: 700;
        }
        .section-title::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 4px;
            background: #27ae60;
            border-radius: 2px;
        }
        .table thead th {
            background-color: #27ae60;
            color: #fff;
            font-weight: 600;
            border: none;
            padding: 1rem 0.75rem;
            text-transform: uppercase;
        }
        .table tbody tr:hover {
            background-color: #dff0d8;
            transition: background-color 0.3s ease;
        }
        .table td, .table th {
            vertical-align: middle !important;
        }
        .product-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .product-img:hover {
            transform: scale(1.05);
        }
        .badge-status {
            padding: 0.4em 0.8em;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            color: #fff;
            background: #e74c3c;
            box-shadow: 0 2px 8px rgb(231 76 60 / 0.3);
        }
        .summary-card {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: #fff;
            border-radius: 20px;
            padding: 2rem 2.5rem;
            box-shadow: 0 8px 25px rgb(39 174 96 / 0.4);
            font-weight: 600;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.7rem;
            font-size: 1.05rem;
        }
        .summary-row.total {
            font-size: 1.4rem;
            font-weight: 800;
            border-top: 2px solid rgba(255,255,255,0.6);
            padding-top: 1rem;
            margin-top: 1rem;
        }
        @media (max-width: 767.98px) {
            .card-custom {
                padding: 1.5rem 1.5rem;
            }
            .btn-pdf {
                padding: 0.45rem 1.2rem;
                font-size: 0.9rem;
            }
            .product-img {
                width: 55px;
                height: 55px;
            }
            .summary-card {
                padding: 1.5rem 1.8rem;
            }
        }
    </style>
@endsection

@section('content')
    @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm p-3 mt-4 mx-auto text-center" style="max-width: 600px; font-size: 1.1rem;">
            <i class="fas fa-check-circle me-2"></i> {{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
            <h2 class="fw-bold text-primary">Chi tiết Đơn hàng #{{ $data[0]->id }}</h2>
            <a href="{{ route('order.invoice', $data[0]->id) }}" class="btn btn-pdf shadow-sm" title="Xuất hóa đơn PDF">
                <i class="fa fa-file-pdf me-2"></i> Xuất hóa đơn PDF
            </a>
        </div>

        <div class="row g-4">
            <div class="col-lg-6 col-md-12">
                <div class="card-custom">
                    <h5 class="section-title">Thông Tin Khách Hàng</h5>
                    <p><strong>Khách hàng:</strong> {{ $data[0]->customer_name }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $data[0]->phone }}</p>
                    <p><strong>Email:</strong> {{ $data[0]->email }}</p>
                    <p><strong>Địa chỉ nhận hàng:</strong> {{ $data[0]->address }}</p>
                </div>
            </div>

            <div class="col-lg-6 col-md-12">
                <div class="card-custom">
                    <h5 class="section-title">Thông Tin Đơn Hàng</h5>
                    <p><strong>Mã đơn hàng:</strong> {{ $data[0]->id }}</p>
                    <p><strong>Ngày đặt:</strong> {{ \Carbon\Carbon::parse($data[0]->created_at)->format('d/m/Y H:i') }}</p>
                    <p><strong>Phương thức thanh toán:</strong> {{ $data[0]->payment }}</p>
                    <p><strong>Ghi chú:</strong> {{ $data[0]->note ?: '-' }}</p>
                </div>
            </div>
        </div>

        <div class="table-responsive mt-5 shadow-sm rounded-3">
            <h4 class="text-center text-success mb-4 fw-semibold">Danh Sách Sản Phẩm</h4>
            <table class="table table-bordered table-hover align-middle text-center mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên sản phẩm</th>
                        <th>Hình ảnh</th>
                        <th>Số lượng</th>
                        <th>Size & Màu sắc</th>
                        <th>Đơn giá</th>
                        <th>Trạng thái</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                        $totalPriceCart = 0;
                        $vat = 0.1;
                        $ship = 30000;
                        $discount = $data[0]->code ?? 0;
                    @endphp
                    @foreach ($data as $item)
                        @php
                            $itemTotal = $item->quantity * $item->price;
                            $totalPriceCart += $itemTotal * (1 - $discount);
                            if ($totalPriceCart >= 500000) {
                                $ship = 0;
                            }
                            $vatPrice = $totalPriceCart * $vat;
                            $total = $totalPriceCart + $vatPrice + $ship;
                        @endphp
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td class="text-start">{{ $item->product_name }}</td>
                            <td>
                                <img src="{{ asset('uploads/'.$item->image) }}" alt="{{ $item->product_name }}" class="product-img" />
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->size }} - {{ $item->color }}</td>
                            <td class="text-nowrap">{{ number_format($item->price, 0, ',', '.') }} đ</td>
                            <td>
                                <span class="badge-status">{{ $item->status }}</span>
                            </td>
                            <td class="text-nowrap">{{ number_format($itemTotal, 0, ',', '.') }} đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row justify-content-end mt-5">
            <div class="col-lg-5 col-md-7 col-sm-10">
                <div class="summary-card">
                    <div class="summary-row">
                        <span>Tạm tính:</span>
                        <span>{{ number_format($totalPriceCart, 0, ',', '.') }} đ</span>
                    </div>
                    <div class="summary-row">
                        <span>Thuế VAT (10%):</span>
                        <span>{{ number_format($vatPrice, 0, ',', '.') }} đ</span>
                    </div>
                    <div class="summary-row">
                        <span>Phí vận chuyển:</span>
                        <span>{{ number_format($ship, 0, ',', '.') }} đ</span>
                    </div>
                    <div class="summary-row">
                        <span>Giảm giá:</span>
                        <span>{{ $discount * 100 }}%</span>
                    </div>
                    <div class="summary-row total">
                        <span>Tổng cộng:</span>
                        <span>{{ number_format($total, 0, ',', '.') }} đ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@endcan

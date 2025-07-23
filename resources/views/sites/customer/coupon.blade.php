{{-- @extends('sites.master') --}}
@extends('sites.master', ['hideChatbox' => true])
@section('title', 'Danh sách Voucher đã dùng')
@section('content')
    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Mã Giảm Giá Đã Dùng</h4>
                        <div class="breadcrumb__links">
                            <a href="{{ route('sites.home') }}">Home</a>
                            <a href="{{ route('user.profile') }}">Tài khoản</a>
                            <span>Mã giảm giá đã dùng</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Coupon Section Begin -->
    <section class="coupon spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="coupon__list">
                        <h4 class="mb-4">Danh sách mã giảm giá đã sử dụng</h4>

                        @if (count($usedVouchers) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>STT</th>
                                            <th>Mã giảm giá</th>
                                            <th>Mức giảm</th>
                                            <th>Đơn hàng</th>
                                            <th>Ngày sử dụng</th>
                                            <th>Giá trị đơn tối thiểu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($usedVouchers as $index => $voucherUsage)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-primary">{{ $voucherUsage->voucher->vouchers_code }}</span>
                                                </td>
                                                <td>
                                                    {{ $voucherUsage->voucher->vouchers_percent_discount }}%
                                                    @if ($voucherUsage->voucher->vouchers_max_discount)
                                                        <br>
                                                        <small>(Tối đa
                                                            {{ number_format($voucherUsage->voucher->vouchers_max_discount, 0, ',', '.') }}
                                                            đ)</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('sites.showOrderDetailOfCustomer', $voucherUsage->order_id) }}"
                                                        class="text-primary">
                                                        Đơn hàng #{{ $voucherUsage->order_id }}
                                                    </a>
                                                </td>
                                                <td>{{ date('d/m/Y H:i', strtotime($voucherUsage->used_at)) }}</td>
                                                <td>{{ number_format($voucherUsage->voucher->min_order_amount, 0, ',', '.') }}
                                                    đ</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Phân trang -->
                            <div class="mt-4">
                                {{ $usedVouchers->links() }}
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Bạn chưa sử dụng mã giảm giá nào.
                            </div>
                        @endif
                    </div>

                    <div class="mt-5">
                        <h4 class="mb-4">Mã giảm giá có thể sử dụng</h4>
                        @if (count($availableVouchers) > 0)
                            <div class="row">
                                @foreach ($availableVouchers as $voucher)
                                    <div class="col-md-6 mb-4">
                                        <div
                                            class="card voucher-card {{ $voucher->is_highlighted ? 'border-success' : '' }}">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h5 class="card-title mb-0">
                                                        <span class="badge bg-primary">{{ $voucher->vouchers_code }}</span>
                                                    </h5>
                                                    <span class="text-success fw-bold">GIẢM:
                                                        {{ number_format($voucher->vouchers_percent_discount, 0, ',', '.') }}%</span>
                                                </div>

                                                <div class="voucher-details">
                                                    <p class="mb-2">
                                                        <i class="fas fa-calendar-alt me-2"></i>
                                                        Áp dụng từ
                                                        {{ date('d/m/Y', strtotime($voucher->vouchers_start_date)) }} đến
                                                        {{ date('d/m/Y', strtotime($voucher->vouchers_end_date)) }}
                                                    </p>
                                                    <p class="mb-2">
                                                        <i class="fas fa-shopping-cart me-2"></i>
                                                        Đơn tối thiểu
                                                        {{ number_format($voucher->vouchers_min_order_amount, 0, ',', '.') }}
                                                        đ
                                                    </p>
                                                    @if ($voucher->vouchers_max_discount)
                                                        <p class="mb-2">
                                                            <i class="fas fa-tag me-2"></i>
                                                            Giảm tối đa
                                                            {{ number_format($voucher->vouchers_max_discount, 0, ',', '.') }}
                                                            đ
                                                        </p>
                                                    @endif
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <small class="text-muted">Còn {{ $voucher->vouchers_usage_limit }}
                                                        lượt</small>
                                                    <a href="{{ route('sites.shop') }}"
                                                        class="btn btn-sm btn-outline-primary">Mua ngay</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-circle me-2"></i> Hiện không có mã giảm giá nào khả dụng để sử
                                dụng.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Coupon Section End -->
@endsection

@section('css')
    <style>
        .voucher-card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .voucher-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .voucher-details {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }

        .badge {
            font-size: 0.9rem;
            padding: 5px 10px;
        }

        .table th {
            background-color: #f8f9fa;
        }
    </style>
@endsection

@extends('sites.master')
@section('title', 'Theo dõi đơn hàng')
@section('content')
    <div class="container my-5">
        <div class="card shadow border-0">
            <div class="card-header border-0 bg-gradient-primary text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Theo dõi đơn hàng</h5>
                    <div class="order-status-badge">
                        <span class="badge bg-info text-white px-3 py-2">
                            {{ $dataOrder->status ?? 'Đang xử lý' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Order Summary -->
                <div class="order-summary mb-4">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title text-primary mb-3">
                                        <i class="fas fa-box-open me-2"></i>Thông tin đơn hàng
                                    </h6>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span class="text-muted">Mã đơn hàng:</span>
                                            <span class="fw-bold">#{{ $dataOrder->id }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span class="text-muted">Ngày đặt:</span>
                                            <span>{{ date('d/m/Y - H:i', strtotime($dataOrder->created_at)) }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span class="text-muted">Phương thức thanh toán:</span>
                                            <span>{{ $dataOrder->payment }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span class="text-muted">Tổng tiền:</span>
                                            <span class="fw-bold text-danger">{{ number_format($dataOrder->total, 0, ',', '.') }}đ</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title text-primary mb-3">
                                        <i class="fas fa-user-tag me-2"></i>Thông tin người nhận
                                    </h6>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span class="text-muted">Họ tên:</span>
                                            <span>{{ $dataOrder->customer_name ?? $dataOrder->receiver_name ?? 'Chưa có thông tin' }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span class="text-muted">Số điện thoại:</span>
                                            <span>{{ $dataOrder->phone ?? 'Chưa có thông tin' }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span class="text-muted">Địa chỉ:</span>
                                            <span class="text-end">{{ $dataOrder->address ?? 'Chưa có thông tin' }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span class="text-muted">Ghi chú:</span>
                                            <span class="text-end">{{ $dataOrder->note ?? 'Không có ghi chú' }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Progress -->
                <div class="delivery-progress mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-primary mb-4">
                                <i class="fas fa-truck-fast me-2"></i>Tiến trình giao hàng
                            </h6>
                            @if($orderStatusHistory && $orderStatusHistory->count() > 0)
                                <div class="timeline">
                                    @foreach($orderStatusHistory as $status)
                                        <div class="timeline-step {{ $loop->last ? 'active' : 'completed' }}">
                                            <div class="timeline-icon">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1">{{ $status->status }}</h6>
                                                <p class="text-muted mb-0">{{ date('d/m/Y - H:i', strtotime($status->created_at)) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Chưa có thông tin tiến trình giao hàng
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Delivery Person -->
                @if($deliveryPerson && $deliveryPerson->staffDelivery)
                <div class="delivery-person mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-primary mb-4">
                                <i class="fas fa-motorcycle me-2"></i>Thông tin nhân viên giao hàng
                            </h6>

                            <div class="row align-items-center">
                                <div class="col-md-2 text-center mb-3 mb-md-0">
                                    <img src="{{ $deliveryPerson->staffDelivery->avatar ?? '/images/default-avatar.png' }}"
                                         class="rounded-circle border shadow-sm"
                                         width="80" height="80" alt="Nhân viên giao hàng">
                                </div>

                                <div class="col-md-4 mb-3 mb-md-0">
                                    <h5 class="mb-1">{{ $deliveryPerson->staffDelivery->name }}</h5>
                                    <p class="text-muted mb-2">SĐT: {{ $deliveryPerson->staffDelivery->phone }}</p>
                                </div>

                                <div class="col-md-6 text-md-end">
                                    <a href="tel:{{ $deliveryPerson->staffDelivery->phone }}" class="btn btn-outline-primary btn-sm me-2">
                                        <i class="fas fa-phone-alt me-1"></i> Gọi ngay
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Order Items -->
                <div class="order-items mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-primary mb-4">
                                <i class="fas fa-boxes me-2"></i>Chi tiết đơn hàng
                            </h6>

                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th class="text-center">Số lượng</th>
                                            <th class="text-end">Đơn giá</th>
                                            <th class="text-end">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($dataOrder->orderDetails) && $dataOrder->orderDetails->count() > 0)
                                            @foreach($dataOrder->orderDetails as $item)
                                            @php
                                                $itemDiscount = $item->code ?? 0;
                                                $finalPrice = $item->price - $itemDiscount;
                                                $itemTotal = $finalPrice * $item->quantity;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $item->product_image ?? '/images/default-product.png' }}"
                                                             class="rounded me-3"
                                                             width="60" height="60"
                                                             alt="Sản phẩm">
                                                        <div>
                                                            <h6 class="mb-0">{{ $item->product_name }}</h6>
                                                            <small class="text-muted">Màu: {{ $item->color }} - Size: {{ $item->size }}</small>
                                                            @if($itemDiscount > 0)
                                                                <small class="text-success d-block">
                                                                    <i class="fas fa-tag me-1"></i>Giảm {{ number_format($itemDiscount, 0, ',', '.') }}đ
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $item->quantity }}</td>
                                                <td class="text-end">
                                                    @if($itemDiscount > 0)
                                                        <span class="text-decoration-line-through text-muted small">{{ number_format($item->price, 0, ',', '.') }}đ</span><br>
                                                        <span class="text-danger">{{ number_format($finalPrice, 0, ',', '.') }}đ</span>
                                                    @else
                                                        {{ number_format($item->price, 0, ',', '.') }}đ
                                                    @endif
                                                </td>
                                                <td class="text-end">{{ number_format($itemTotal, 0, ',', '.') }}đ</td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">
                                                    Không có sản phẩm trong đơn hàng
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    @if(isset($dataOrder->orderDetails) && $dataOrder->orderDetails->count() > 0)
                                    <tfoot>
                                        @php
                                            $subtotal = $dataOrder->orderDetails->sum(function($item) {
                                                return $item->price * $item->quantity;
                                            });
                                            $discount = $dataOrder->orderDetails->sum(function($item) {
                                                return ($item->code ?? 0) * $item->quantity;
                                            });
                                            $afterDiscount = $subtotal - $discount;
                                            $vat = $dataOrder->VAT ?? 0;
                                            $shippingFee = $dataOrder->shipping_fee ?? 0;
                                            $finalTotal = $afterDiscount + $vat + $shippingFee;
                                        @endphp

                                        <tr>
                                            <td colspan="3" class="text-end">Tạm tính:</td>
                                            <td class="text-end">{{ number_format($subtotal, 0, ',', '.') }}đ</td>
                                        </tr>

                                        @if($discount > 0)
                                        <tr>
                                            <td colspan="3" class="text-end text-success">Chiết khấu:</td>
                                            <td class="text-end text-success">-{{ number_format($discount, 0, ',', '.') }}đ</td>
                                        </tr>
                                        @endif

                                        @if($vat > 0)
                                        <tr>
                                            <td colspan="3" class="text-end">VAT:</td>
                                            <td class="text-end">{{ number_format($vat, 0, ',', '.') }}đ</td>
                                        </tr>
                                        @endif

                                        @if($shippingFee > 0)
                                        <tr>
                                            <td colspan="3" class="text-end">Phí vận chuyển:</td>
                                            <td class="text-end">{{ number_format($shippingFee, 0, ',', '.') }}đ</td>
                                        </tr>
                                        @endif

                                        <tr class="border-top">
                                            <td colspan="3" class="text-end fw-bold fs-5">Tổng cộng:</td>
                                            <td class="text-end fw-bold text-danger fs-5">
                                                {{ number_format($dataOrder->total, 0, ',', '.') }}đ
                                            </td>
                                        </tr>
                                    </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('css')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #3f51b5, #2196f3);
    }

    .card {
        border-radius: 10px;
        overflow: hidden;
    }

    .timeline {
        position: relative;
        padding-left: 50px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 25px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #e0e0e0;
    }

    .timeline-step {
        position: relative;
        padding-bottom: 30px;
    }

    .timeline-step:last-child {
        padding-bottom: 0;
    }

    .timeline-icon {
        position: absolute;
        left: -50px;
        top: 0;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f5f5f5;
        color: #9e9e9e;
        border: 2px solid #e0e0e0;
    }

    .timeline-step.completed .timeline-icon {
        background-color: #4caf50;
        color: white;
        border-color: #4caf50;
    }

    .timeline-step.active .timeline-icon {
        background-color: #2196f3;
        color: white;
        border-color: #2196f3;
        animation: pulse 1.5s infinite;
    }

    .timeline-content {
        padding: 10px 15px;
        background-color: #f9f9f9;
        border-radius: 8px;
        position: relative;
    }

    .timeline-step.completed .timeline-content,
    .timeline-step.active .timeline-content {
        background-color: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .timeline-step.active .timeline-content::after {
        content: '';
        position: absolute;
        left: -10px;
        top: 15px;
        width: 0;
        height: 0;
        border-top: 10px solid transparent;
        border-bottom: 10px solid transparent;
        border-right: 10px solid white;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(33, 150, 243, 0.4);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(33, 150, 243, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(33, 150, 243, 0);
        }
    }

    .rating {
        font-size: 14px;
    }

    .order-status-badge .badge {
        font-size: 14px;
        letter-spacing: 0.5px;
    }

    @media (max-width: 768px) {
        .timeline {
            padding-left: 40px;
        }

        .timeline::before {
            left: 20px;
        }

        .timeline-icon {
            left: -40px;
            width: 40px;
            height: 40px;
            font-size: 14px;
        }

        .action-buttons .btn {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    }
</style>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Animation for timeline steps
        $('.timeline-step').each(function(index) {
            $(this).css('opacity', 0);
            $(this).delay(200 * index).animate({
                opacity: 1
            }, 500);
        });

        // Tooltip for buttons
        $('[data-toggle="tooltip"]').tooltip();

        // Real-time tracking simulation
        function updateDeliveryStatus() {
            // This would be replaced with actual API calls in a real application
            console.log("Updating delivery status...");
        }

        // Update every 30 seconds
        setInterval(updateDeliveryStatus, 30000);
    });
</script>
@endsection

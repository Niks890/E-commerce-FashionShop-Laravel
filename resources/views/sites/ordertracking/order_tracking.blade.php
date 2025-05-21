@extends('sites.master')
@section('title', 'Theo dõi đơn hàng')
@section('content')
    <div class="container my-5">
        <div class="card shadow border-0">
            <div class="card-header border-0 bg-gradient-primary text-white py-3">
                <h5 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Theo dõi đơn hàng</h5>
            </div>
            <div class="card-body p-4">
                <!-- Order Details Section -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-4 mb-md-0">
                        <div class="order-info p-3 bg-light rounded">
                            <h6 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-file-invoice me-2"></i>Thông
                                tin đơn hàng</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Mã đơn:</span>
                                <span class="fw-bold">#DH00123</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Ngày đặt:</span>
                                <span>20/05/2025</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Trạng thái:</span>
                                <span class="badge bg-info text-white">Đang giao</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="customer-info p-3 bg-light rounded">
                            <h6 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-user me-2"></i>Người nhận</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tên:</span>
                                <span>Nguyễn Văn A</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Địa chỉ:</span>
                                <span>123 Lý Thường Kiệt, Hà Nội</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">SĐT:</span>
                                <span>0123 456 789</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipper Information Section -->
                <div class="shipper-info p-3 bg-light rounded mb-4">
                    <h6 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-motorcycle me-2"></i>Thông tin nhân viên giao hàng
                    </h6>
                    <div class="row align-items-center">
                        <div class="col-md-2 col-sm-3 mb-3 mb-md-0 text-center">
                            <div class="shipper-avatar mb-2">
                                <img src="/api/placeholder/100/100" class="rounded-circle border shadow-sm"
                                    alt="Ảnh nhân viên" width="80" height="80">
                            </div>
                            <span class="badge bg-success">Đang hoạt động</span>
                        </div>
                        <div class="col-md-5 col-sm-9 mb-3 mb-md-0">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tên:</span>
                                <span class="fw-bold">Trần Văn Bình</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Mã NV:</span>
                                <span>NV12345</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Đánh giá:</span>
                                <span>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star-half-alt text-warning"></i>
                                    <small class="text-muted ms-1">(4.5/5)</small>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">SĐT:</span>
                                <span>0987 654 321</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Thời gian giao dự kiến:</span>
                                <span>14:00 - 16:00, 21/05/2025</span>
                            </div>
                            <div class="d-flex justify-content-end mt-2">
                                <button class="btn btn-sm btn-outline-primary me-2">
                                    <i class="fas fa-phone-alt me-1"></i> Gọi điện
                                </button>
                                <button class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-comment-dots me-1"></i> Nhắn tin
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tracking Progress Section -->
                <div class="tracking-progress my-5">
                    <h6 class="text-primary border-bottom pb-2 mb-4"><i class="fas fa-map-marker-alt me-2"></i>Tiến độ đơn
                        hàng</h6>
                    <div class="tracking-step">
                        <div class="step completed">
                            <div class="circle">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="label">Đã đặt hàng</div>
                            <div class="time">19/05/2025</div>
                        </div>
                        <div class="step completed">
                            <div class="circle">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="label">Đã xác nhận</div>
                            <div class="time">19/05/2025</div>
                        </div>
                        <div class="step active">
                            <div class="circle">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="label">Đang giao</div>
                            <div class="time">20/05/2025</div>
                        </div>
                        <div class="step">
                            <div class="circle">
                                <i class="fas fa-check-double"></i>
                            </div>
                            <div class="label">Hoàn tất</div>
                            <div class="time">Dự kiến</div>
                        </div>
                    </div>
                </div>

                <!-- Order Notes Section -->
                <div class="order-notes p-3 bg-light rounded mt-4">
                    <h6 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-sticky-note me-2"></i>Ghi chú đơn hàng
                    </h6>
                    <p class="mb-0"><i class="fas fa-info-circle text-muted me-2"></i>Giao trong giờ hành chính. Liên hệ
                        trước khi giao.</p>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-center mt-4">
                    <button class="btn btn-outline-secondary" style="margin-right: 10px"><i class="fas fa-print me-1"></i> In đơn hàng</button>
                    <button class="btn btn-primary"><i class="fas fa-headset me-1"></i> Liên hệ hỗ trợ</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('client/css/tracking-order.css') }}">
@endsection
@section('js')
    <script>
        // Có thể thêm hiệu ứng JS tùy chọn sau này
        $(document).ready(function() {
            // Animation for tracking steps
            $('.step').each(function(index) {
                $(this).delay(200 * index).animate({
                    opacity: 1
                }, 500);
            });
        });
    </script>
@endsection

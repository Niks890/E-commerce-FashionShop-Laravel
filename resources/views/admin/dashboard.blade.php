@extends('admin.master')

@section('title', 'Trang Quản trị')

@section('content')
    {{-- @if (Session::has('success'))
        <div class="shadow-lg p-2 move-from-top js-div-dissappear" style="width: 18rem; display:flex; text-align:center">
            <i class="fas fa-check p-2 bg-success text-white rounded-circle pe-2 mx-2"></i>{{ Session::get('success') }}
        </div>
    @endif --}}

    <div class="row">
        {{-- Card số liệu với tooltip --}}
        <div class="col-sm-6 col-md-4">
            <div class="card card-stats card-round" data-bs-toggle="tooltip" data-bs-placement="top"
                title="Tổng số nhân viên hiện tại">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Số lượng nhân viên</p>
                                <h4 class="card-title">{{ $staffQuantity }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card card-stats card-round" data-bs-toggle="tooltip" data-bs-placement="top"
                title="Tổng số khách hàng đã đăng ký">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Số lượng khách hàng</p>
                                <h4 class="card-title">{{ $customerQuantity }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card doanh thu tháng với phần tăng trưởng --}}
        <div class="col-sm-6 col-md-4">
            <div class="card card-stats card-round" data-bs-toggle="tooltip" data-bs-placement="top"
                title="Doanh thu tháng này và tăng trưởng so với tháng trước">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                <i class="fas fa-luggage-cart"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers d-flex align-items-center gap-2">
                                <div>
                                    <p class="card-category mb-1">Doanh thu tháng này</p>
                                    <h4 class="card-title">{{ number_format($revenueMonth->tongtien ?? 0, 0, ',', '.') }} đ
                                    </h4>
                                </div>
                                @if (isset($revenueGrowthPercent))
                                    <div class="text-smaller d-flex align-items-center"
                                        style="font-weight:600; color: {{ $revenueGrowthPercent >= 0 ? '#28a745' : '#dc3545' }}">
                                        <i class="fas fa-arrow-{{ $revenueGrowthPercent >= 0 ? 'up' : 'down' }} me-1"></i>
                                        {{ abs($revenueGrowthPercent) }}%
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Đơn hàng chờ xử lý --}}
        <div class="col-sm-6 col-md-4">
            <div class="card card-stats card-round" data-bs-toggle="tooltip" data-bs-placement="top"
                title="Số đơn hàng đang chờ xử lý">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                <i class="far fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Đơn hàng chờ xử lý</p>
                                <h4 class="card-title">{{ $orderQuantity }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sản phẩm bán chạy nhất --}}
        <div class="col-sm-6 col-md-4">
            <a href="javascript:void(0);" class="text-decoration-none">
                <div class="card card-stats card-round hover-shadow" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="Xem danh sách sản phẩm bán chạy nhất">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-info bubble-shadow-small">
                                    <i class="fas fa-trophy"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Sản phẩm bán chạy nhất tháng</p>
                                    <h4 class="card-title text-primary">Đến xem ngay <i class="fas fa-arrow-right ms-1"></i>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Sản phẩm gần hết hàng --}}
        <div class="col-sm-6 col-md-4">
            <a href="javascript:void(0);" class="text-decoration-none">
                <div class="card card-stats card-round hover-shadow" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="Xem danh sách sản phẩm gần hết hàng">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-danger bubble-shadow-small">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Sản phẩm gần hết hàng</p>
                                    <h4 class="card-title text-danger">Đến xem ngay <i class="fas fa-arrow-right ms-1"></i>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Cảnh báo tồn kho thấp với nút --}}
    <div class="card mt-3 border-warning shadow-sm">
        <div class="card-body bg-warning bg-opacity-25 d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <strong>Cảnh báo:</strong> Có <b>5 sản phẩm</b> đang trong tình trạng tồn kho thấp. Vui lòng kiểm tra và bổ
                sung hàng kịp thời.
            </div>
            <a href="javascript:void(0);" class="btn btn-warning btn-sm text-white shadow-sm" data-bs-toggle="tooltip"
                title="Đi tới trang quản lý tồn kho">
                Quản lý tồn kho <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    {{-- Bảng đơn hàng mới nhất --}}
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Đơn hàng mới nhất</h5>
            <a href="javascript:void(0);" class="btn btn-primary btn-sm">Xem tất cả đơn hàng</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Mã đơn hàng</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Trạng thái</th>
                        <th>Tổng tiền</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Dữ liệu giả để demo, bạn có thể thay bằng dữ liệu thật --}}
                    <tr>
                        <td>DH001</td>
                        <td>Nguyễn Văn A</td>
                        <td>15/05/2025</td>
                        <td><span class="badge bg-info text-white">Đang xử lý</span></td>
                        <td>1.500.000 đ</td>
                    </tr>
                    <tr>
                        <td>DH002</td>
                        <td>Trần Thị B</td>
                        <td>14/05/2025</td>
                        <td><span class="badge bg-success">Đã giao</span></td>
                        <td>850.000 đ</td>
                    </tr>
                    <tr>
                        <td>DH003</td>
                        <td>Phạm Văn C</td>
                        <td>14/05/2025</td>
                        <td><span class="badge bg-danger">Hủy</span></td>
                        <td>0 đ</td>
                    </tr>
                    <tr>
                        <td>DH004</td>
                        <td>Vũ Thị D</td>
                        <td>13/05/2025</td>
                        <td><span class="badge bg-warning text-dark">Đang giao</span></td>
                        <td>2.200.000 đ</td>
                    </tr>
                    <tr>
                        <td>DH005</td>
                        <td>Hoàng Văn E</td>
                        <td>12/05/2025</td>
                        <td><span class="badge bg-success">Đã giao</span></td>
                        <td>1.200.000 đ</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Biểu đồ doanh thu theo tháng --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-round">
                <div class="mt-3 text-end">
                    <a href="javascript:void(0);" class="btn btn-primary">
                        Xem chi tiết doanh thu
                        <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Doanh thu hôm nay</h5>
                    <canvas id="revenueChart" height="100"></canvas>

                    {{-- Nút xem chi tiết --}}

                </div>
            </div>
        </div>
    </div>


    {{-- Toast thành công --}}
    <div id="toast-success" style="position: fixed; top: 1rem; right: 1rem; z-index: 1100;"
        class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                Đăng Nhập Thành công!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Tooltip Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // Chart doanh thu theo tháng (giả sử có biến JS 'revenueData')
        const revenueData = @json(array_values($revenueByMonth));
        const revenueLabels = @json(array_keys($revenueByMonth));
        var ctx = document.getElementById('revenueChart').getContext('2d');
        var revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Doanh thu',
                    data: revenueData,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + ' đ';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });


        // Hiển thị toast nếu có session success
        @if (Session::has('success'))
            var toastEl = document.getElementById('toast-success');
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        @endif
    </script>
@endsection

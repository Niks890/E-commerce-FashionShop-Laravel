@extends('admin.master')

@section('title', 'Trang Quản trị')

@section('content')

    @can('delivery workers')
        <div class="row">
            <!-- Đơn hàng cần giao -->
            <div class="col-sm-6 col-md-4">
                <a href="{{ route('order.trackingOrder') }}" class="text-decoration-none">
                    <div class="card card-stats card-round" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Đơn hàng cần giao trong ngày">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-warning bubble-shadow-small">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Đơn hàng cần giao</p>
                                        <h4 class="card-title">{{ $orderAssign ?? 0 }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Đơn hàng đang giao -->
            <div class="col-sm-6 col-md-4">
                <a href="{{ route('order.trackingOrder') }}" class="text-decoration-none">
                    <div class="card card-stats card-round" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Đơn hàng đang trong quá trình giao">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-info bubble-shadow-small">
                                        <i class="fas fa-shipping-fast"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Đơn hàng đang giao</p>
                                        <h4 class="card-title">{{ $orderProcessing ?? 0 }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Đơn hàng đã giao thành công -->
            <div class="col-sm-6 col-md-4">
                <a href="{{ route('order.trackingOrder') }}" class="text-decoration-none">
                    <div class="card card-stats card-round" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Đơn hàng đã giao thành công trong tháng">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-success bubble-shadow-small">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Đơn giao thành công</p>
                                        <h4 class="card-title">{{ $orderSuccess ?? 0 }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @endcan

    @can('managers')
        <div class="row">
            {{-- Card số liệu với tooltip --}}
            <div class="col-sm-6 col-md-4">
                <a href="{{ route('staff.index') }}">
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
                </a>
            </div>
    @endcan


        @can('managers')
            {{-- Card doanh thu tháng với phần tăng trưởng --}}
            <div class="col-sm-6 col-md-4">
                <a href="{{ route('admin.revenueDay') }}" class="text-decoration-none">
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
                                            <h4 class="card-title text-primary">Đến xem ngay <i
                                                    class="fas fa-arrow-right ms-1"></i>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-md-4">
                <a href="{{ route('admin.profitYear') }}" class="text-decoration-none">
                    <div class="card card-stats card-round" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Doanh thu tháng này và tăng trưởng so với tháng trước">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-warning bubble-shadow-small">
                                        <i class="fas fa-luggage-cart"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers d-flex align-items-center gap-2">
                                        <div>
                                            <p class="card-category mb-1">Quản lý lợi nhuận</p>
                                            <h4 class="card-title text-warning">Đến xem ngay <i
                                                    class="fas fa-arrow-right ms-1"></i>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endcan

        @can('salers')
            <div class="row">
                <div class="col-sm-6 col-md-4">
                    <a href="{{ route('customer.index') }}">
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
                    </a>
                </div>



                {{-- Đơn hàng chờ xử lý --}}
                <div class="col-sm-6 col-md-4">
                    <a href="{{ route('order.index') }}" class="text-decoration-none">
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
                    </a>
                </div>

                {{-- Sản phẩm bán chạy nhất --}}
                <div class="col-sm-6 col-md-4">
                    <a href="{{ route('admin.revenueProductBestSeller') }}" class="text-decoration-none">
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
                                            <p class="card-category">Sản phẩm bán chạy nhất trong tuần qua</p>
                                            <h4 class="card-title text-primary">Đến xem ngay <i
                                                    class="fas fa-arrow-right ms-1"></i>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4">
                    <a href="{{ route('admin.revenueProductBestSellerMonthYear') }}" class="text-decoration-none">
                        <div class="card card-stats card-round hover-shadow" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Xem danh sách sản phẩm bán chạy nhất">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-success bubble-shadow-small">
                                            <i class="fas fa-trophy"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Sản phẩm bán chạy nhất trong tháng này</p>
                                            <h4 class="card-title text-success">Đến xem ngay <i
                                                    class="fas fa-arrow-right ms-1"></i>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan


            @can('warehouse workers')
                <!-- Phần hiển thị cho nhân viên kho -->
                <div class="row">
                    <!-- Sản phẩm gần hết hàng -->
                    <div class="col-sm-6 col-md-6">
                        <a href="{{ route('admin.revenueInventory') }}" class="text-decoration-none">
                            <div class="card card-stats card-round hover-shadow" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Xem danh sách sản phẩm gần hết hàng">
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
                                                <h4 class="card-title text-danger">Đến xem ngay <i
                                                        class="fas fa-arrow-right ms-1"></i></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Quản lý nhà cung cấp -->
                    <div class="col-sm-6 col-md-6">
                        <a href="{{ route('provider.index') }}" class="text-decoration-none">
                            <div class="card card-stats card-round hover-shadow" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Quản lý nhà cung cấp">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                                <i class="fas fa-building"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Nhà cung cấp</p>
                                                <h4 class="card-title text-secondary">Đến xem ngay <i
                                                        class="fas fa-arrow-right ms-1"></i></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Thêm các card khác cho nhân viên kho nếu cần -->
                </div>

                <!-- Cảnh báo tồn kho -->
                <div class="card mt-3 border-warning shadow-sm">
                    <div
                        class="card-body bg-warning bg-opacity-25 d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <strong>Cảnh báo:</strong> Có <b>{{ $productOutOfStock ?? 0 }} sản phẩm</b> đang trong tình trạng
                            tồn kho thấp.
                        </div>
                        <a href="{{ route('admin.revenueInventory') }}" class="btn btn-danger btn-sm text-white shadow-sm">
                            Quản lý tồn kho <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

            @endcan


            @can('salers')
                {{-- Bảng đơn hàng mới nhất --}}
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Đơn hàng mới nhất</h5>
                        <a href="{{ route('order.index') }}" class="btn btn-primary btn-sm">Xem tất cả đơn hàng</a>
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
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orderPending as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->customer_name }}</td>
                                        <td>{{ $item->created_at }}</td>
                                        <td><span class="badge bg-info text-white">{{ $item->status }}</span></td>
                                        <td>{{ number_format($item->total) }} đ</td>
                                        <td><a class="btn btn-primary" href="{{ route('order.index') }}">Đến xử lý</a></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            Chưa có đơn hàng nào
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{-- Phân trang --}}
                        <div class="d-flex justify-content-center">
                            {{ $orderPending->links() }}
                        </div>
                    </div>
                </div>
            @endcan


            @can('managers')
                {{-- Biểu đồ doanh thu theo tháng --}}
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card card-round">
                            <div class="mt-3 text-end">
                                <a href="{{ route('admin.revenueMonth') }}" class="btn btn-primary">
                                    Xem chi tiết doanh thu
                                    <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Doanh thu các tháng thuộc năm {{ now()->year }}</h5>
                                <canvas id="revenueChart" height="100"></canvas>

                                <div class="stats-summary">
                                    <div class="summary-item">
                                        <div class="summary-value" id="totalRevenue">
                                            {{ number_format(array_sum($total ?? []), 0, ',', '.') ?? '0' }} VNĐ</div>
                                        <div class="summary-label">Tổng doanh thu</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-value" id="avgRevenue">
                                            {{ number_format(count($total ?? []) > 0 ? array_sum($total) / count($total) : 0, 0, ',', '.') ?? '0' }}
                                            VNĐ
                                        </div>
                                        <div class="summary-label">Doanh thu trung bình</div>
                                    </div>
                                    <div class="summary-item">
                                        <div class="summary-value" id="maxRevenue">
                                            {{ number_format(count($total ?? []) > 0 ? max($total) : 0, 0, ',', '.') ?? '0' }}
                                            VNĐ
                                        </div>
                                        <div class="summary-label">Doanh thu cao nhất</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endcan



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

        @section('css')
            <style>
                .stats-summary {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 1rem;
                    margin-top: 2rem;
                }

                .summary-item {
                    background: #f8fafc;
                    border-radius: 8px;
                    padding: 1rem;
                    text-align: center;
                }

                .summary-value {
                    font-size: 1.5rem;
                    font-weight: 700;
                    color: #0ea5e9;
                    margin-bottom: 0.5rem;
                }

                .summary-label {
                    font-size: 0.875rem;
                    color: #64748b;
                }
            </style>

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
                console.log(revenueLabels, revenueData);
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

                    // Cập nhật tổng hợp

                });


                // Hiển thị toast nếu có session success
                @if (Session::has('success'))
                    var toastEl = document.getElementById('toast-success');
                    var toast = new bootstrap.Toast(toastEl);
                    toast.show();
                @endif
            </script>
        @endsection

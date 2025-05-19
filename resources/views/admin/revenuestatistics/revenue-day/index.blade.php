@can('managers')
    @extends('admin.master')
    @section('title', 'Thống kê doanh thu bán ra theo ngày')

@section('content')
    <div class="container-fluid">
        <!-- Thông báo -->
        @if (Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <strong>{{ Session::get('success') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @elseif (Session::has('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>{{ Session::get('error') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tiêu đề trang -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 text-primary">
                        <i class="fas fa-chart-line me-2"></i>Thống kê doanh thu theo ngày
                    </h4>
                    @php
                        $from = request('from', $from);
                        $to = request('to', $to);
                    @endphp
                    <div class="btn-group">
                        <a href="{{ route('admin.revenueDay.exportPdf', ['from' => $from, 'to' => $to]) }}"
                           class="btn btn-danger">
                            <i class="fas fa-file-pdf me-1"></i> Xuất PDF
                        </a>
                        <a href="{{ route('admin.revenueDay.exportExcel', ['from' => $from, 'to' => $to]) }}"
                           class="btn btn-success">
                            <i class="fas fa-file-excel me-1"></i> Xuất Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bộ lọc thời gian -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.revenueDay') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="from" class="form-label fw-bold"><i class="fas fa-calendar-alt me-1"></i> Từ ngày:</label>
                        <input type="date" id="from" name="from" value="{{ $from }}" required class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="to" class="form-label fw-bold"><i class="fas fa-calendar-alt me-1"></i> Đến ngày:</label>
                        <input type="date" id="to" name="to" value="{{ $to }}" required class="form-control">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> Lọc dữ liệu
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Biểu đồ doanh thu -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Biểu đồ doanh thu</h5>
                <div class="chart-container" style="position: relative; height:60vh; width:100%">
                    <canvas id="doanhThuChart"></canvas>
                </div>

                <!-- Thống kê tổng quan -->
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Tổng doanh thu</h5>
                                <h3 class="mb-0">{{ number_format(array_sum($total), 0, ',', '.') }} đ</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Doanh thu cao nhất</h5>
                                <h3 class="mb-0">{{ number_format(max($total), 0, ',', '.') }} đ</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Doanh thu trung bình</h5>
                                <h3 class="mb-0">{{ number_format(array_sum($total) / count($total), 0, ',', '.') }} đ</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
    <style>
        .chart-container {
            margin: 0 auto;
        }
        .card {
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .btn {
            border-radius: 5px;
            font-weight: 500;
        }
        .btn-group .btn {
            border-radius: 0;
        }
        .btn-group .btn:first-child {
            border-top-left-radius: 5px;
            border-bottom-left-radius: 5px;
        }
        .btn-group .btn:last-child {
            border-top-right-radius: 5px;
            border-bottom-right-radius: 5px;
        }
    </style>
@endsection

@section('js')
    <!-- Chart JS -->
    <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>

    <script>
        // Dữ liệu biểu đồ
        const labels = @json($day);
        const dataValues = @json($total);

        // Tạo gradient cho background biểu đồ
        const ctx = document.getElementById('doanhThuChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(54, 162, 235, 0.5)');
        gradient.addColorStop(1, 'rgba(54, 162, 235, 0.1)');

        // Cấu hình biểu đồ
        const doanhThuChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh Thu (VNĐ)',
                    data: dataValues,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                weight: 'bold',
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        padding: 10,
                        cornerRadius: 5,
                        callbacks: {
                            label: function(context) {
                                let value = context.parsed.y;
                                value = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                return 'Doanh thu: ' + value + ' đ';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [5, 5]
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return (value / 1000).toFixed(1) + 'K';
                                }
                                return value;
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animations: {
                    tension: {
                        duration: 1000,
                        easing: 'linear'
                    }
                }
            }
        });

        // Đóng thông báo tự động sau 5 giây
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    </script>
@endsection
@else
    {{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

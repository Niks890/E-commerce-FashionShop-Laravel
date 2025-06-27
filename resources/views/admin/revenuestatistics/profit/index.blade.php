@can('managers')
    @extends('admin.master')
    @section('title', 'Thống kê lợi nhuận')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Bộ lọc thống kê lợi nhuận</h4>
            </div>
            <div class="card-body">
                <form id="filterForm" method="GET" action="{{ route('admin.profitYear') }}">
                    <div class="row">
                        <!-- Loại lọc -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Loại thống kê:</label>
                                <select name="filter_type" id="filterType" class="form-control">
                                    <option value="year" {{ $filterType == 'year' ? 'selected' : '' }}>Theo năm</option>
                                    <option value="month" {{ $filterType == 'month' ? 'selected' : '' }}>Theo tháng
                                    </option>
                                    <option value="date_range" {{ $filterType == 'date_range' ? 'selected' : '' }}>Theo
                                        khoảng ngày</option>
                                </select>
                            </div>
                        </div>

                        <!-- Lọc theo năm -->
                        <div class="col-md-2" id="yearFilter"
                            style="{{ $filterType != 'year' && $filterType != 'month' ? 'display:none' : '' }}">
                            <div class="form-group">
                                <label>Năm:</label>
                                <select name="selected_year" class="form-control">
                                    @foreach ($availableYears as $year)
                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Lọc theo tháng -->
                        <div class="col-md-2" id="monthFilter" style="{{ $filterType != 'month' ? 'display:none' : '' }}">
                            <div class="form-group">
                                <label>Tháng:</label>
                                <select name="selected_month" class="form-control">
                                    <option value="">Tất cả tháng</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>
                                            Tháng {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- Lọc theo khoảng ngày -->
                        <div class="col-md-2" id="fromDateFilter"
                            style="{{ $filterType != 'date_range' ? 'display:none' : '' }}">
                            <div class="form-group">
                                <label>Từ ngày:</label>
                                <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
                            </div>
                        </div>

                        <div class="col-md-2" id="toDateFilter"
                            style="{{ $filterType != 'date_range' ? 'display:none' : '' }}">
                            <div class="form-group">
                                <label>Đến ngày:</label>
                                <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
                            </div>
                        </div>

                        <!-- Nút lọc -->
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary form-control">
                                    <i class="fa fa-filter text-nowrap"></i> Lọc
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Biểu đồ -->
        <div class="card mt-4">
            <div class="card-header">
                <h4 class="card-title">
                    Biểu đồ lợi nhuận
                    @if ($filterType == 'year')
                        theo năm
                    @elseif($filterType == 'month')
                        theo tháng
                    @else
                        theo ngày
                    @endif
                </h4>
            </div>
            <div class="chart-container" style="position: relative; height: 500px; overflow-x: auto;">
                <div style="min-width: {{ count($labels) * 50 }}px;">
                    <canvas id="doanhThuChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Bảng dữ liệu chi tiết -->
        <div class="card mt-4">
            <div class="card-header">
                <h4 class="card-title">Chi tiết lợi nhuận</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Thời gian</th>
                                <th>Doanh thu</th>
                                <th>Chi phí</th>
                                <th>Lợi nhuận</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalRevenue = 0;
                                $totalCost = 0;
                                $totalProfit = 0;
                            @endphp
                            @foreach ($profitData as $index => $data)
                                @php
                                    $revenue = $data['doanhthu'] ?? 0;
                                    $cost = $data['chiphi'] ?? 0;
                                    $profit = $data['loiNhuan'] ?? 0;

                                    $totalRevenue += $revenue;
                                    $totalCost += $cost;
                                    $totalProfit += $profit;
                                @endphp
                                <tr>
                                    <td>{{ $labels[$index] ?? '' }}</td>
                                    <td>{{ number_format($revenue) }} VNĐ</td>
                                    <td>{{ number_format($cost) }} VNĐ</td>
                                    <td class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($profit) }} VNĐ
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td>Tổng cộng</td>
                                <td>{{ number_format($totalRevenue) }} VNĐ</td>
                                <td>{{ number_format($totalCost) }} VNĐ</td>
                                <td class="{{ $totalProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($totalProfit) }} VNĐ
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .chart-container {
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

@section('js')
    <!-- Chart JS -->
    <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>

    <script>
        // Xử lý thay đổi loại lọc
        document.getElementById('filterType').addEventListener('change', function() {
            const filterType = this.value;

            // Ẩn/hiện các trường lọc
            document.getElementById('yearFilter').style.display =
                (filterType === 'year' || filterType === 'month') ? 'block' : 'none';

            document.getElementById('monthFilter').style.display =
                filterType === 'month' ? 'block' : 'none';

            document.getElementById('fromDateFilter').style.display =
                filterType === 'date_range' ? 'block' : 'none';

            document.getElementById('toDateFilter').style.display =
                filterType === 'date_range' ? 'block' : 'none';
        });

        // Chuyển dữ liệu từ PHP sang JavaScript
        const labels = @json($labels);
        const dataValues = @json($loiNhuan);

        // Tạo màu động cho biểu đồ
        const backgroundColors = dataValues.map(value =>
            value >= 0 ? 'rgba(75, 192, 192, 0.2)' : 'rgba(255, 99, 132, 0.2)'
        );
        const borderColors = dataValues.map(value =>
            value >= 0 ? 'rgba(75, 192, 192, 1)' : 'rgba(255, 99, 132, 1)'
        );

        // Vẽ biểu đồ
        const ctx = document.getElementById('doanhThuChart').getContext('2d');
        const doanhThuChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Lợi nhuận (VNĐ)',
                    data: dataValues,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: borderColors,
                    pointBorderColor: borderColors,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' +
                                    new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' VNĐ';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return new Intl.NumberFormat('vi-VN').format(value) + ' VNĐ';
                            }
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                },
                elements: {
                    point: {
                        hoverRadius: 8
                    }
                }
            }
        });

        // Auto submit form khi thay đổi
        document.querySelectorAll('#filterForm select, #filterForm input').forEach(element => {
            element.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        });
    </script>
@endsection
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

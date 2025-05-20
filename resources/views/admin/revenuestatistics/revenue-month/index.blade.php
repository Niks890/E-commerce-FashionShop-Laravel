@can('managers')
    @extends('admin.master')
    @section('title', 'Thống kê doanh thu theo tháng và năm')

@section('css')
    <style>
        .stats-card {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            background: white;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .stats-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stats-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #334155;
            margin: 0;
        }

        .period-selector {
            display: flex;
            background: #f1f5f9;
            border-radius: 8px;
            padding: 0.5rem;
            gap: 0.5rem;
        }

        .period-selector button {
            padding: 0.5rem 1rem;
            border: none;
            background: transparent;
            border-radius: 6px;
            font-size: 0.875rem;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
        }

        .period-selector button.active {
            background: white;
            color: #0ea5e9;
            font-weight: 500;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .chart-container {
            position: relative;
            height: 350px;
            margin-top: 1rem;
        }

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

        .filter-container {
            width: 150px;
        }

        .form-select {
            width: 100%;
            padding: 0.5rem;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background-color: white;
            color: #334155;
            font-size: 0.875rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            font-size: 0.875rem;
        }

        .alert-warning {
            background-color: #fff7ed;
            border-left: 4px solid #f97316;
            color: #9a3412;
        }

        .d-none {
            display: none;
        }

        .d-flex {
            display: flex;
        }

        .align-items-center {
            align-items: center;
        }

        .gap-3 {
            gap: 1rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="stats-card">
                    <div class="stats-header">
                        <h5 class="stats-title">Thống kê doanh thu</h5>
                        <div class="d-flex align-items-center gap-3">
                            <!-- Các filter hiện tại -->
                            <div class="filter-container">
                                <select id="yearSelect" class="form-select">
                                    <option value="">Chọn năm</option>
                                    @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="period-selector" role="group" aria-label="Chọn khoảng thời gian">
                                <button type="button" data-period="month">Tháng</button>
                                <button type="button" data-period="quarter">Quý</button>
                                <button type="button" data-period="year" class="active">Năm</button>
                            </div>

                            <!-- Nút xuất file PDF và Excel -->
                            <div>
                                <button id="exportPdfBtn" type="button" class="btn btn-danger btn-sm">Xuất PDF</button>
                                <button id="exportExcelBtn" type="button" class="btn btn-success btn-sm">Xuất
                                    Excel</button>
                            </div>
                        </div>

                    </div>

                    <div id="yearWarning" class="alert alert-warning d-none">
                        Vui lòng chọn năm trước khi xem theo tháng hoặc quý!
                    </div>

                    <div class="chart-container">
                        <canvas id="doanhThuChart"></canvas>
                    </div>

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
                                {{ number_format(count($total ?? []) > 0 ? max($total) : 0, 0, ',', '.') ?? '0' }} VNĐ
                            </div>
                            <div class="summary-label">Doanh thu cao nhất</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('doanhThuChart').getContext('2d');
        let doanhThuChart = null;

        // Format tiền VNĐ
        function formatCurrency(value) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND',
                minimumFractionDigits: 0
            }).format(value);
        }

        // Khởi tạo biểu đồ
        function initChart(labels, data, title) {
            if (doanhThuChart) doanhThuChart.destroy();

            doanhThuChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Doanh Thu',
                        data: data,
                        borderColor: '#0ea5e9',
                        backgroundColor: 'rgba(14, 165, 233, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#0ea5e9',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 8,
                        pointHoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: title,
                            font: {
                                size: 16,
                                weight: 'bold'
                            },
                            padding: {
                                bottom: 20
                            }
                        },
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#334155',
                            bodyColor: '#334155',
                            bodyFont: {
                                weight: 'bold'
                            },
                            borderColor: '#e2e8f0',
                            borderWidth: 1,
                            padding: 15,
                            displayColors: false,
                            callbacks: {
                                label(ctx) {
                                    return 'Doanh thu: ' + formatCurrency(ctx.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false,
                                color: 'rgba(0,0,0,0.05)'
                            },
                            ticks: {
                                callback: val => formatCurrency(val),
                                font: {
                                    size: 12
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                }
            });

            // Cập nhật tổng hợp
            if (data.length > 0) {
                const total = data.reduce((a, b) => a + b, 0);
                const avg = total / data.length;
                const max = Math.max(...data);

                document.getElementById('totalRevenue').textContent = formatCurrency(total);
                document.getElementById('avgRevenue').textContent = formatCurrency(avg);
                document.getElementById('maxRevenue').textContent = formatCurrency(max);
            } else {
                // Clear nếu không có dữ liệu
                document.getElementById('totalRevenue').textContent = formatCurrency(0);
                document.getElementById('avgRevenue').textContent = formatCurrency(0);
                document.getElementById('maxRevenue').textContent = formatCurrency(0);
            }
        }

        // Gọi AJAX lấy dữ liệu từ controller
        async function fetchData(year, period) {
            const params = new URLSearchParams({
                year,
                period
            });
            try {
                const response = await fetch("{{ route('api.revenueMonthApi') }}?" + params.toString());
                if (!response.ok) throw new Error('Không lấy được dữ liệu');

                const json = await response.json();
                // Kiểm tra keys trả về có đủ
                if (!json.labels || !json.values) {
                    alert('Dữ liệu không đúng định dạng');
                    return null;
                }
                return json;
            } catch (e) {
                alert(e.message);
                return null;
            }
        }

        // Cập nhật chart theo lựa chọn
        async function updateChart(year, period) {
            const warningEl = document.getElementById('yearWarning');
            if ((period === 'month' || period === 'quarter') && !year) {
                warningEl.classList.remove('d-none');
                warningEl.classList.add('d-flex');
                if (doanhThuChart) doanhThuChart.destroy();
                return;
            }
            warningEl.classList.add('d-none');
            warningEl.classList.remove('d-flex');

            const data = await fetchData(year, period);
            if (!data) return;

            // data.labels và data.values theo API
            initChart(data.labels, data.values, `Doanh thu theo ${period} năm ${year || 'tất cả'}`);
        }


        // Event listeners cho nút chọn khoảng thời gian
        const periodButtons = document.querySelectorAll('.period-selector button');
        let selectedPeriod = 'year';
        periodButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.classList.contains('active')) return;
                periodButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                selectedPeriod = btn.getAttribute('data-period');

                const selectedYear = document.getElementById('yearSelect').value;
                updateChart(selectedYear, selectedPeriod);
            });
        });

        // Event listener cho select năm
        document.getElementById('yearSelect').addEventListener('change', e => {
            const selectedYear = e.target.value;
            updateChart(selectedYear, selectedPeriod);
        });

        // Khởi tạo lần đầu
        window.addEventListener('DOMContentLoaded', () => {
            const selectedYear = document.getElementById('yearSelect').value;
            updateChart(selectedYear, selectedPeriod);
        });

        // Gắn sự kiện cho nút Xuất PDF
        document.getElementById('exportPdfBtn').addEventListener('click', () => {
            const year = document.getElementById('yearSelect').value || '';
            const period = selectedPeriod;
            const url = `{{ route('admin.revenueMonth.exportPdf') }}?year=${year}&period=${period}`;
            window.open(url, '_blank');
        });

        // Gắn sự kiện cho nút Xuất Excel
        document.getElementById('exportExcelBtn').addEventListener('click', () => {
            const year = document.getElementById('yearSelect').value || '';
            const period = selectedPeriod;
            const url = `{{ route('admin.revenueMonth.exportExcel') }}?year=${year}&period=${period}`;
            window.open(url, '_blank');
        });
    </script>
@endsection
@endcan

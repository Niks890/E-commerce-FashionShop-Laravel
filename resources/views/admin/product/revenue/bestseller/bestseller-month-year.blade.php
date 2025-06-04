@extends('admin.master')
@can('managers')
    @section('title', 'Thống kê sản phẩm bán chạy theo tháng quý năm')
@section('css')
    <style>
        /* Style giống như bạn đã có */
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
            height: 400px;
            margin-top: 1rem;
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
            display: none !important;
        }

        /* Bảng chi tiết */
        #detailTable {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            font-size: 0.9rem;
        }

        #detailTable th,
        #detailTable td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        #detailTable th {
            background-color: #0ea5e9;
            color: white;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 text-primary">
                            <i class="fas fa-chart-line me-2"></i>Thống kê sản phẩm bán chạy theo tháng quý năm
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="stats-card">
                    <div class="stats-header">
                        <h5 class="stats-title">Top sản phẩm bán chạy</h5>
                        <div class="d-flex align-items-center gap-3">
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
                        </div>
                    </div>

                    <div id="yearWarning" class="alert alert-warning d-none">
                        Vui lòng chọn năm trước khi xem theo tháng hoặc quý!
                    </div>

                    <div class="chart-container">
                        <canvas id="topProductChart"></canvas>
                    </div>

                    <!-- Bảng dữ liệu chi tiết -->
                    <div id="detailContainer" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal chi tiết -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Chi tiết lượt bán của sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div id="detail-loading" class="text-center my-3">Đang tải...</div>
                    <table class="table table-bordered table-hover d-none" id="detailTableModal">
                        <thead>
                            <tr>
                                <th>Màu</th>
                                <th>Size</th>
                                <th>Số lượng bán</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('topProductChart').getContext('2d');
        let topProductChart = null;
        let currentDetailData = null;

        function initChart(labels, totalQuantityData, totalRevenueData) {
            if (topProductChart) topProductChart.destroy();

            topProductChart = new Chart(ctx, {
                data: {
                    labels: labels,
                    datasets: [{
                            type: 'bar',
                            label: 'Số lượng bán',
                            data: totalQuantityData,
                            backgroundColor: 'rgba(14, 165, 233, 0.7)',
                            borderColor: '#0ea5e9',
                            borderWidth: 1,
                            borderRadius: 5,
                            yAxisID: 'yQuantity'
                        },
                        {
                            type: 'line',
                            label: 'Doanh thu',
                            data: totalRevenueData,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            fill: false,
                            tension: 0.2,
                            yAxisID: 'yRevenue'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    if (context.dataset.label === 'Doanh thu') {
                                        return context.dataset.label + ': ' + context.parsed.y.toLocaleString(
                                            'vi-VN', {
                                                style: 'currency',
                                                currency: 'VND'
                                            });
                                    }
                                    return context.dataset.label + ': ' + context.parsed.y;
                                }
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        },
                    },
                    scales: {
                        yQuantity: {
                            type: 'linear',
                            position: 'left',
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Số lượng'
                            }
                        },
                        yRevenue: {
                            type: 'linear',
                            position: 'right',
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false
                            },
                            title: {
                                display: true,
                                text: 'Doanh thu (VND)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('vi-VN', {
                                        style: 'currency',
                                        currency: 'VND'
                                    }).format(value);
                                }
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        intersect: true
                    },
                    onClick: chartClickHandler
                }
            });
        }



        function chartClickHandler(evt, elements) {
            if (elements.length === 0) return;
            const firstElement = elements[0];
            const label = topProductChart.data.labels[firstElement.index];
            const period = document.querySelector('.period-selector button.active').dataset.period;
            const year = document.getElementById('yearSelect').value;

            let periodValue = null; // Biến mới để lưu giá trị (tháng, quý, năm)
            if (period === 'month') {
                periodValue = firstElement.index + 1; // Month labels are 1-indexed (Tháng 1, Tháng 2, ...)
            } else if (period === 'quarter') {
                periodValue = firstElement.index + 1; // Quarter labels are 1-indexed (Quý 1, Quý 2, ...)
            } else { // period === 'year'
                periodValue = parseInt(label); // Năm là chính label
            }

            // Hiển thị bảng chi tiết nếu có dữ liệu
            if (currentDetailData && currentDetailData[periodValue]) { // Dùng periodValue ở đây
                renderDetailTable(currentDetailData[periodValue], year, label);
            } else {
                clearDetailTable();
            }
        }

        // Cập nhật hàm renderDetailTable để truyền thêm period và value vào button Xem chi tiết
        function renderDetailTable(productsObj, year, periodLabel) {
            const period = document.querySelector('.period-selector button.active').dataset.period;
            let html = `
        <h6>Chi tiết sản phẩm bán chạy trong <strong>${periodLabel}</strong> năm <strong>${year}</strong></h6>
        <table id="detailTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng bán</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
    `;

            for (const [productName, productData] of Object.entries(productsObj)) {
                // productData = { id, quantity }
                let currentPeriodValue;
                if (period === 'month') {
                    // currentDetailData key là số tháng (1-12)
                    for (const key in currentDetailData) {
                        if (currentDetailData[key] === productsObj) { // Tìm key tương ứng với productsObj hiện tại
                            currentPeriodValue = key;
                            break;
                        }
                    }
                } else if (period === 'quarter') {
                    // currentDetailData key là số quý (1-4)
                    for (const key in currentDetailData) {
                        if (currentDetailData[key] === productsObj) { // Tìm key tương ứng với productsObj hiện tại
                            currentPeriodValue = key;
                            break;
                        }
                    }
                } else { // period === 'year'
                    currentPeriodValue = year; // Khi period là year, value chính là năm
                }

                html += `
            <tr>
                <td>${productData.product_name}</td>
                <td>${productData.total_sold}</td>
                <td>
                    <button class="btn btn-sm btn-info"
                            onclick="viewProductDetail('${productData.product_id}', '${period}', '${currentPeriodValue}', '${year}')">
                        Xem chi tiết
                    </button>
                </td>
            </tr>
        `;
            }

            html += `</tbody></table>`;

            document.getElementById('detailContainer').innerHTML = html;
        }

        // Hàm viewProductDetail mới
        function viewProductDetail(productId, period, value, year) {
            let url = `/api/revenue-product-detail-month-year/${productId}?period=${period}&value=${value}&year=${year}`;

            // ... (phần fetch và hiển thị modal không đổi)
            fetch(url)
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    console.log('Chi tiết sản phẩm:', data);

                    if (data.length === 0) {
                        alert('Không có dữ liệu chi tiết cho sản phẩm này trong khoảng thời gian đã chọn.');
                        return;
                    }

                    const tbody = document.querySelector('#detailTableModal tbody');
                    tbody.innerHTML = ''; // Xóa dữ liệu cũ

                    data.forEach(item => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                    <td>${item.color}</td>
                    <td>${item.size}</td>
                    <td>${item.total_sold}</td>
                `;
                        tbody.appendChild(tr);
                    });

                    document.getElementById('detail-loading').classList.add('d-none');
                    document.getElementById('detailTableModal').classList.remove('d-none');

                    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
                    detailModal.show();
                })
                .catch(err => {
                    console.error(err);
                    alert('Lỗi khi tải chi tiết sản phẩm');
                });
        }




        function clearDetailTable() {
            document.getElementById('detailContainer').innerHTML = '';
        }


        async function fetchChartData(period, year) {
            if ((period === 'month' || period === 'quarter') && !year) {
                document.getElementById('yearWarning').classList.remove('d-none');
                clearDetailTable();
                if (topProductChart) topProductChart.destroy();
                return;
            }
            document.getElementById('yearWarning').classList.add('d-none');

            let url = `{{ route('api.topProductSalesApi') }}?period=${period}`;
            if (year) url += `&year=${year}`;

            try {
                const res = await fetch(url);
                if (!res.ok) throw new Error('Lỗi khi lấy dữ liệu');
                const json = await res.json();
                // console.log('json = ', json);
                currentDetailData = json.detail;
                // console.log('currentDetailData = ', currentDetailData);

                initChart(json.labels, json.data.total_quantity, json.data.total_revenue);
                clearDetailTable();
            } catch (error) {
                alert('Lỗi: ' + error.message);
            }
        }

        // Xử lý chọn năm và period
        document.getElementById('yearSelect').addEventListener('change', () => {
            const period = document.querySelector('.period-selector button.active').dataset.period;
            const year = document.getElementById('yearSelect').value;
            fetchChartData(period, year);
        });

        document.querySelectorAll('.period-selector button').forEach(btn => {
            btn.addEventListener('click', (e) => {
                document.querySelectorAll('.period-selector button').forEach(b => b.classList.remove(
                    'active'));
                btn.classList.add('active');
                const period = btn.dataset.period;
                const year = document.getElementById('yearSelect').value;
                fetchChartData(period, year);
            });
        });

        // Khởi tạo lần đầu, mặc định là năm hiện tại & period = 'year'
        fetchChartData('year', '{{ date('Y') }}');
    </script>
@endsection
@endcan

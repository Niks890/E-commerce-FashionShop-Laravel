@can('managers')
    @extends('admin.master')
    @section('title', 'Quản lý chi phí nhập hàng')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    {{-- Thêm Font Awesome để có biểu tượng mũi tên --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPYXKC2b0/J2gQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .product-row {
            cursor: pointer;
            background-color: #f8f9fa;
            /* Màu nền nhẹ cho hàng sản phẩm chính */
        }

        .variant-detail {
            display: none;
            /* Mặc định ẩn chi tiết variant */
            background-color: #e9ecef;
            /* Màu nền cho chi tiết variant */
        }

        .product-row.expanded {
            background-color: #e2e6ea;
            /* Màu nền khi sản phẩm được mở rộng */
        }

        .toggle-icon {
            transition: transform 0.2s ease-in-out;
        }

        .product-row.expanded .toggle-icon {
            transform: rotate(180deg);
        }
    </style>
@endsection

@section('content')
    <div class="container mt-4">
        <h3 class="mb-4">📦 Quản lý chi phí nhập hàng</h3>

        {{-- Bộ lọc --}}
        <form method="GET" action="{{ route('admin.revenueInventoryDatetime') }}" class="card mb-4">
            <div class="card-body">
                <div class="row">
                    {{-- Kiểu thống kê --}}
                    <div class="col-md-3">
                        <label>Chọn kiểu thống kê</label>
                        <select name="time_range" class="form-control" onchange="this.form.submit()">
                            <option value="day" {{ $timeRange === 'day' ? 'selected' : '' }}>Theo ngày</option>
                            <option value="month" {{ $timeRange === 'month' ? 'selected' : '' }}>Theo tháng</option>
                            <option value="year" {{ $timeRange === 'year' ? 'selected' : '' }}>Theo năm</option>
                        </select>
                    </div>

                    {{-- Nếu theo ngày --}}
                    @if ($timeRange === 'day')
                        <div class="col-md-4">
                            <label>Từ ngày</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}"
                                onchange="this.form.submit()">
                        </div>
                        <div class="col-md-4">
                            <label>Đến ngày</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}"
                                onchange="this.form.submit()">
                        </div>

                        {{-- Nếu theo tháng --}}
                    @elseif($timeRange === 'month')
                        <div class="col-md-4">
                            <label>Tháng</label>
                            <select name="month" class="form-control" onchange="this.form.submit()">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>Tháng
                                        {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Năm</label>
                            <select name="year" class="form-control" onchange="this.form.submit()">
                                @for ($i = $currentYear - 5; $i <= $currentYear; $i++)
                                    <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>Năm
                                        {{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        {{-- Nếu theo năm --}}
                    @else
                        <div class="col-md-4">
                            <label>Năm</label>
                            <select name="year" class="form-control" onchange="this.form.submit()">
                                @for ($i = $currentYear - 5; $i <= $currentYear; $i++)
                                    <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>Năm
                                        {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    @endif
                </div>
            </div>
        </form>

        {{-- Summary Cards --}}
        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Tổng chi phí</h5>
                        <p class="card-text h4">{{ number_format($summary['total_cost'] ?? 0) }} đ</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Tổng số lượt nhập</h5>
                        <p class="card-text h4">{{ number_format($summary['import_count'] ?? 0) }} lượt</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Tổng số lượng nhập</h5>
                        <p class="card-text h4">{{ number_format($summary['total_quantity'] ?? 0) }} sản phẩm</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Biểu đồ --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Biểu đồ thống kê</h5>
                <canvas id="importChart" height="100"></canvas>
            </div>
        </div>

        {{-- Danh sách sản phẩm --}}
        {{-- Danh sách sản phẩm --}}
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Danh sách sản phẩm đã nhập</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Tổng số lượng nhập</th>
                                <th>Tổng chi phí nhập</th>
                                <th>Ngày nhập gần nhất</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr class="product-row" data-product-id="{{ $product->id }}">
                                    <td>
                                        <strong>{{ $product->product_name }}</strong>
                                        <i class="fas fa-chevron-down float-right toggle-icon"></i>
                                    </td>
                                    <td>
                                        {{-- Hiển thị tổng số lượng nhập từ thuộc tính đã tính --}}
                                        {{ number_format($product->total_imported_quantity ?? 0) }}
                                    </td>
                                    <td>
                                        {{-- Hiển thị tổng chi phí nhập từ thuộc tính đã tính --}}
                                        {{ number_format($product->total_imported_cost ?? 0) }} đ
                                    </td>
                                    <td>
                                        <?php
                                        // Lấy ngày nhập gần nhất từ các inventoryDetails đã eager loaded
                                        $latestImportDate = null;
                                        if ($product->inventoryDetails->isNotEmpty()) {
                                            $latestImportDate = $product->inventoryDetails->max('created_at');
                                        }
                                        ?>
                                        {{ $latestImportDate ? \Carbon\Carbon::parse($latestImportDate)->format('d/m/Y') : 'N/A' }}
                                    </td>
                                </tr>
                                {{-- Hàng ẩn chứa chi tiết các variant --}}
                                <tr class="variant-detail" id="variants-{{ $product->id }}">
                                    <td colspan="4">
                                        <div class="pl-4">
                                            <h6>Chi tiết Variants:</h6>
                                            <table class="table table-sm table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Màu sắc</th>
                                                        <th>Kích thước</th>
                                                        <th>Số lượng tồn kho</th>
                                                        <th>Ghi chú</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($product->productVariants as $variant)
                                                        <tr>
                                                            <td>{{ $variant->color ?? 'N/A' }}</td>
                                                            <td>{{ $variant->size ?? 'N/A' }}</td>
                                                            <td>{{ number_format($variant->stock ?? 0) }}</td>
                                                            <td>
                                                                <small class="text-muted">
                                                                    Tồn kho hiện tại
                                                                </small>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center">Không có variant nào cho
                                                                sản phẩm này.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Không có dữ liệu</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Phân trang --}}
                <div class="d-flex justify-content-center">
                    {{ $products->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Khởi tạo Flatpickr cho ô input ngày
        flatpickr("#start_date, #end_date", {
            dateFormat: "Y-m-d"
        });

        // Dữ liệu và logic cho biểu đồ (giữ nguyên)
        const chartData = @json($chartData);
        const timeRange = @json($timeRange);

        const labels = chartData.map(item => {
            if (timeRange === 'day') {
                return item.label;
            } else if (timeRange === 'month') {
                return `Tháng ${item.label}`;
            } else {
                return `Tháng ${item.label}`;
            }
        });
        const data = chartData.map(item => item.total_cost);

        if (labels.length > 0) {
            new Chart(document.getElementById('importChart'), {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Chi phí nhập hàng',
                        data,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => value.toLocaleString() + ' đ'
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: context => context.dataset.label + ': ' + context.raw.toLocaleString() + ' đ'
                            }
                        }
                    },
                    onClick: (e, elements) => {
                        if (elements.length > 0) {
                            const index = elements[0].index;
                            const params = new URLSearchParams(window.location.search);
                            const clickedLabel = chartData[index].label;

                            if (timeRange === 'day') {
                                params.set('start_date', clickedLabel);
                                params.set('end_date', clickedLabel);
                            } else if (timeRange === 'month') {
                                params.set('time_range', 'day');
                                const year = params.get('year') || new Date().getFullYear();
                                const month = clickedLabel;
                                const firstDay = `${year}-${String(month).padStart(2, '0')}-01`;
                                const lastDay = new Date(year, month, 0).toISOString().slice(0, 10);
                                params.set('start_date', firstDay);
                                params.set('end_date', lastDay);
                            } else {
                                params.set('time_range', 'month');
                                params.set('month', clickedLabel);
                                params.set('year', params.get('year') || new Date().getFullYear());
                            }
                            location.href = window.location.pathname + '?' + params.toString();
                        }
                    }
                }
            });
        } else {
            document.getElementById('importChart').innerText = 'Không có dữ liệu để hiển thị biểu đồ.';
        }

        // JavaScript cho hiệu ứng ẩn/hiện chi tiết variant
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.product-row').forEach(row => {
                row.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    const variantDetailRow = document.getElementById(`variants-${productId}`);
                    if (variantDetailRow) {
                        // Toggle display style
                        variantDetailRow.style.display = variantDetailRow.style.display === 'none' ?
                            'table-row' : 'none';
                        // Toggle 'expanded' class for styling
                        this.classList.toggle('expanded');
                        // Toggle icon
                        const icon = this.querySelector('.toggle-icon');
                        if (icon) {
                            icon.classList.toggle('fa-chevron-down');
                            icon.classList.toggle('fa-chevron-up');
                        }
                    }
                });
            });
        });
    </script>
@endsection
@endcan

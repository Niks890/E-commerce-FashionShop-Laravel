@can('managers')
    @extends('admin.master')
    @section('title', 'Quản lý chi phí nhập hàng')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Danh sách sản phẩm đã nhập</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Màu sắc</th>
                                <th>Kích thước</th>
                                <th>Số lượng</th>
                                <th>Giá nhập</th>
                                <th>Thành tiền</th>
                                <th>Ngày nhập</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($importProducts as $product)
                                <tr>
                                    <td>{{ $product->product_name }}</td>
                                    <td>{{ $product->color }}</td>
                                    <td>{{ $product->size }}</td>
                                    <td>{{ $product->quantity }}</td>
                                    <td>{{ number_format($product->price) }} đ</td>
                                    <td>{{ number_format($product->total_price) }} đ</td>
                                    <td>{{ \Carbon\Carbon::parse($product->created_at)->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Không có dữ liệu</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Phân trang --}}
                <div class="d-flex justify-content-center">
                    {{ $importProducts->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        flatpickr("#start_date, #end_date", {
            dateFormat: "Y-m-d"
        });

        const chartData = @json($chartData);
        const timeRange = @json($timeRange);

        // Sửa lỗi ở đây: `item.date` có thể là `undefined` nếu là theo tháng hoặc năm.
        // Tên trường trong chartData đã được đổi thành `label` để đồng nhất.
        const labels = chartData.map(item => {
            if (timeRange === 'day') {
                return item.label; // item.label sẽ là ngày (YYYY-MM-DD)
            } else if (timeRange === 'month') {
                return `Tháng ${item.label}`; // item.label sẽ là số tháng
            } else { // year
                return `Tháng ${item.label}`; // item.label sẽ là số tháng
            }
        });
        const data = chartData.map(item => item.total_cost);

        // Khởi tạo biểu đồ nếu có dữ liệu
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
                            const clickedLabel = chartData[index].label; // Lấy giá trị label gốc từ chartData

                            if (timeRange === 'day') {
                                params.set('start_date', clickedLabel);
                                params.set('end_date', clickedLabel);
                            } else if (timeRange === 'month') {
                                // Nếu đang xem theo tháng, click vào biểu đồ sẽ lọc theo ngày của tháng đó
                                // Cần chuyển sang chế độ lọc theo ngày
                                params.set('time_range', 'day');
                                const year = params.get('year') || new Date().getFullYear();
                                const month = clickedLabel; // clickedLabel là số tháng
                                // Đặt ngày đầu tiên và cuối cùng của tháng để lọc theo ngày
                                const firstDay = `${year}-${String(month).padStart(2, '0')}-01`;
                                const lastDay = new Date(year, month, 0).toISOString().slice(0, 10);
                                params.set('start_date', firstDay);
                                params.set('end_date', lastDay);
                            } else { // timeRange === 'year'
                                // Nếu đang xem theo năm, click vào biểu đồ sẽ lọc theo tháng
                                params.set('time_range', 'month');
                                params.set('month', clickedLabel); // clickedLabel là số tháng
                                params.set('year', params.get('year') || new Date().getFullYear());
                            }
                            location.href = window.location.pathname + '?' + params.toString();
                        }
                    }
                }
            });
        } else {
            // Hiển thị thông báo nếu không có dữ liệu biểu đồ
            document.getElementById('importChart').innerText = 'Không có dữ liệu để hiển thị biểu đồ.';
        }
    </script>
@endsection
@endcan

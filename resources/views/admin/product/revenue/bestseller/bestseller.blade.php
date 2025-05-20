@extends('admin.master')
@section('title', 'Thống kê sản phẩm bán chạy')

@section('content')
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
<div class="container py-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 text-primary">
                        <i class="fas fa-chart-line me-2"></i>Thống kê sản phẩm bán chạy theo ngày
                    </h4>
                    @php
                        $from = request('from', $from);
                        $to = request('to', $to);
                    @endphp
                    <div class="btn-group">
                        <a href="{{ route('admin.revenueProductBestSeller.exportPdf', ['from' => $from, 'to' => $to]) }}"
                           class="btn btn-danger">
                            <i class="fas fa-file-pdf me-1"></i> Xuất PDF
                        </a>
                        <a href="{{ route('admin.revenueProductBestSeller.exportExcel', ['from' => $from, 'to' => $to]) }}"
                           class="btn btn-success">
                            <i class="fas fa-file-excel me-1"></i> Xuất Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <form method="GET" action="{{ route('admin.revenueProductBestSeller') }}" class="row g-3 align-items-end mb-4">
        <div class="col-md-3">
            <label for="from" class="form-label">Từ ngày</label>
            <input type="date" name="from" id="from" value="{{ request('from') }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="to" class="form-label">Đến ngày</label>
            <input type="date" name="to" id="to" value="{{ request('to') }}" class="form-control">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Lọc</button>
        </div>
    </form>


    {{-- Biểu đồ --}}
    <div class="card mb-4">
        <div class="card-body">
            <div style="position: relative; height:400px; width:100%">
                <canvas id="bestsellerChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Bảng danh sách sản phẩm --}}
    <div class="card">
        <div class="card-header bg-success text-white">Top các sản phẩm bán chạy</div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tên sản phẩm</th>
                        <th>Số lượng bán</th>
                        <th>Tổng doanh thu</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
              <tbody>
    @forelse($bestsellers as $index => $item)
        <tr>
            <td>{{ ($bestsellers->currentPage() - 1) * $bestsellers->perPage() + $index + 1 }}</td>
            <td>{{ $item->product_name }}</td>
            <td>{{ number_format($item->total_sold) }}</td>
            <td>{{ number_format($item->total_revenue) }} ₫</td>
            <td>
    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailModal"
        data-product="{{ $item->product_name }}"
        data-product-id="{{ $item->product_id }}"
        data-from="{{ $from }}"
        data-to="{{ $to }}">
        <i class="fas fa-search"></i> Xem chi tiết
    </button>
</td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="text-center">Không có dữ liệu.</td>
        </tr>
    @endforelse
</tbody>

            </table>
            <div class="p-3 d-flex justify-content-center">
    {{ $bestsellers->withQueryString()->links() }}
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
        <table class="table table-bordered table-hover d-none" id="detailTable">
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
    // console.log(@json($bestsellers));
    document.addEventListener('DOMContentLoaded', function() {


          const fromInput = document.getElementById('from');
    const toInput = document.getElementById('to');

    // Nếu cả hai đều chưa có giá trị (user chưa lọc)
    if (!fromInput.value && !toInput.value) {
        const today = new Date();
        const sevenDaysAgo = new Date();
        sevenDaysAgo.setDate(today.getDate() - 7);

        // Hàm format yyyy-mm-dd
        function formatDate(date) {
            const d = date.getDate().toString().padStart(2, '0');
            const m = (date.getMonth() + 1).toString().padStart(2, '0');
            const y = date.getFullYear();
            return `${y}-${m}-${d}`;
        }

        fromInput.value = formatDate(sevenDaysAgo);
        toInput.value = formatDate(today);
    }


        const detailModal = document.getElementById('detailModal');
        detailModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const productId = button.getAttribute('data-product-id');
        // console.log(productId);
        const productName = button.getAttribute('data-product');
        const from = button.getAttribute('data-from');
        const to = button.getAttribute('data-to');
        // console.log(from, to);


        detailModal.querySelector('#detailModalLabel').innerText = `Báo cáo chi tiết sản phẩm "${productName}" từ ${from} đến ${to}`;

        const table = detailModal.querySelector('#detailTable');
        const tbody = table.querySelector('tbody');
        tbody.innerHTML = '';
        table.classList.add('d-none');
        document.getElementById('detail-loading').classList.remove('d-none');

        fetch(`/api/revenue-product-detail/${productId}?from=${from}&to=${to}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="text-center">Không có dữ liệu</td></tr>';
                } else {
                    data.forEach(row => {
                        tbody.innerHTML += `<tr>
                            <td>${row.color}</td>
                            <td>${row.size}</td>
                            <td>${row.total_sold}</td>
                        </tr>`;
                    });
                }
                table.classList.remove('d-none');
                document.getElementById('detail-loading').classList.add('d-none');
            })
            .catch(error => {
                console.error(error);
                tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Lỗi khi tải dữ liệu</td></tr>';
                table.classList.remove('d-none');
                document.getElementById('detail-loading').classList.add('d-none');
            });
    });
 const ctx = document.getElementById('bestsellerChart');
if (!ctx) {
  console.error('Không tìm thấy phần tử canvas');
  return;
}
const context = ctx.getContext('2d');
if (!context) {
  console.error('Không lấy được context 2D');
  return;
}


        // Lấy dữ liệu từ PHP
        const productData = @json($bestsellersForChart->map(function($item) {
            return [
                'name' => $item->product_name,
                'sold' => (int)$item->total_sold,
                'revenue' => $item->total_revenue
            ];
        }));

        // console.log('Dữ liệu sản phẩm:', productData);

        try {
            new Chart(context, {
                type: 'bar',
                data: {
                    labels: productData.map(item => item.name),
                    datasets: [{
                        label: 'Số lượng bán',
                        data: productData.map(item => item.sold),
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    }, {
                        label: 'Doanh thu (triệu ₫)',
                        data: productData.map(item => item.revenue / 1000000),
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        type: 'line',
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.datasetIndex === 0) {
                                        label += context.raw.toLocaleString() + ' sản phẩm';
                                    } else {
                                        label += (context.raw * 1000000).toLocaleString() + ' ₫';
                                    }
                                    return label;
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Số lượng bán'
                            },
                            ticks: {
                                precision: 0
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Doanh thu (triệu ₫)'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Lỗi khi tạo biểu đồ:', error);
        }
           setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    });
</script>
@endsection

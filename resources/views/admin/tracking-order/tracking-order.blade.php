@can('delivery workers')
    @extends('admin.master')
    @section('title', 'Quản lý Đơn hàng đang giao')
@section('content')
    <div id="root" class="container mx-auto max-w-7xl px-4 py-6">
        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center shadow-sm py-2 px-3 mb-4 rounded-lg" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger d-flex align-items-center shadow-sm py-2 px-3 mb-4 rounded-lg" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="card shadow-sm rounded-xl">
            <div class="card-body p-4 p-md-5">
                <h2 class="card-title text-2xl font-bold mb-4 text-gray-800">Quản lý giao hàng</h2>

                <form id="search-form" class="mb-4" action="{{ route('order.searchOrderTracking') }}" method="GET">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group shadow-sm-sm">
                                <input name="query" type="text" class="form-control rounded-s-lg"
                                    placeholder="Nhập ID đơn hàng hoặc số điện thoại để tìm kiếm..."
                                    value="{{ request('query') }}">
                                <button class="btn btn-primary rounded-e-lg" type="submit" aria-label="Tìm kiếm">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="start_date" class="form-control shadow-sm-sm rounded-lg"
                                value="{{ request('start_date') }}"
                                onchange="document.getElementById('search-form').submit()">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="end_date" class="form-control shadow-sm-sm rounded-lg"
                                value="{{ request('end_date') }}"
                                onchange="document.getElementById('search-form').submit()">
                        </div>
                        <div class="col-md-12 mt-3">
                            <select name="status_filter" class="form-select shadow-sm-sm rounded-lg"
                                onchange="document.getElementById('search-form').submit()">
                                <option value="">Tất cả trạng thái</option>
                                <option value="Đã gửi cho đơn vị vận chuyển"
                                    {{ request('status_filter') == 'Đã gửi cho đơn vị vận chuyển' ? 'selected' : '' }}>Đã
                                    gửi cho đơn vị vận chuyển</option>
                                <option value="Đang giao hàng"
                                    {{ request('status_filter') == 'Đang giao hàng' ? 'selected' : '' }}>Đang giao hàng
                                </option>
                                <option value="Giao hàng thành công"
                                    {{ request('status_filter') == 'Giao hàng thành công' ? 'selected' : '' }}>Giao thành
                                    công</option>
                                <option value="Đã huỷ đơn hàng"
                                    {{ request('status_filter') == 'Đã huỷ đơn hàng' ? 'selected' : '' }}>Đã
                                    bị huỷ</option>
                            </select>
                        </div>
                    </div>
                </form>

                <div class="mb-4">
                    <h6 class="text-muted mb-2">Lọc nhanh:</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @php
                            $filters = [
                                ['value' => '', 'label' => 'Tất cả', 'icon' => 'fa-list', 'count' => $totalOrders ?? 0],
                                [
                                    'value' => 'Đã gửi cho đơn vị vận chuyển',
                                    'label' => 'Đã gửi cho ĐVVC',
                                    'icon' => 'fa-check',
                                    'count' => $processedOrders ?? 0,
                                ],
                                [
                                    'value' => 'Đang giao hàng',
                                    'label' => 'Đang giao hàng',
                                    'icon' => 'fa-truck',
                                    'count' => $shippingOrders ?? 0,
                                ],
                                [
                                    'value' => 'Giao hàng thành công',
                                    'label' => 'Thành công',
                                    'icon' => 'fa-thumbs-up',
                                    'count' => $successOrders ?? 0,
                                ],
                                [
                                    'value' => 'Đã huỷ đơn hàng',
                                    'label' => 'Đã huỷ đơn hàng',
                                    'icon' => 'fa-times-circle',
                                    'count' => $failedOrders ?? 0,
                                ],
                            ];
                        @endphp

                        @foreach ($filters as $filter)
                            <a href="{{ route('order.searchOrderTracking', ['status_filter' => $filter['value'], 'query' => request('query'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                                class="badge {{ request('status_filter') == $filter['value'] && (!request('query') || $filter['value'] != '') ? 'bg-primary' : 'bg-light text-dark' }} text-decoration-none p-2 filter-badge">
                                <i class="fa {{ $filter['icon'] }} me-1"></i>{{ $filter['label'] }}
                                <span class="badge bg-secondary ms-1">{{ $filter['count'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div id="filter-info"
                    class="alert alert-info d-flex align-items-center mb-3 rounded-lg {{ !request('query') && !request('status_filter') && !request('start_date') && !request('end_date') ? 'hidden' : '' }}">
                    <i class="fa fa-info-circle me-2"></i>
                    <span>
                        Đang hiển thị:
                        <strong>{{ request('status_filter') ? request('status_filter') : 'Tất cả trạng thái' }}</strong>
                        @if (request('query'))
                            | Tìm kiếm: <strong>"{{ request('query') }}"</strong>
                        @endif
                        @if (request('start_date'))
                            | Từ ngày: <strong>{{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }}</strong>
                        @endif
                        @if (request('end_date'))
                            | Đến ngày: <strong>{{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}</strong>
                        @endif
                        | Tổng: <strong>{{ $data->total() }}</strong> đơn hàng
                    </span>
                    <button type="button" id="clear-filters-btn"
                        class="btn btn-sm btn-outline-secondary ms-auto rounded-md">
                        <i class="fa fa-times me-1"></i>Xóa bộ lọc
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-uppercase text-secondary">
                            <tr>
                                <th scope="col">Mã đơn</th>
                                <th scope="col">Tên khách hàng</th>
                                <th scope="col">Địa chỉ</th>
                                <th scope="col">Số điện thoại</th>
                                <th scope="col">Tổng tiền</th>
                                <th scope="col">Trạng thái hiện tại</th>
                                <th scope="col">Ngày tạo</th>
                                <th scope="col" class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="order-table-body">
                            @forelse ($data as $order)
                                <tr id="order-row-{{ $order->id }}">
                                    <td data-label="ID">{{ $order->id }}</td>
                                    <td data-label="Tên khách hàng" class="fw-semibold">{{ $order->customer_name }}</td>
                                    <td data-label="Địa chỉ">{{ $order->address }}</td>
                                    <td data-label="Số điện thoại">{{ $order->phone }}</td>
                                    <td data-label="Tổng tiền">{{ number_format($order->total, 0, ',', '.') }} đ</td>
                                    <td data-label="Trạng thái">
                                        @php
                                            $badgeClass = '';
                                            $iconClass = '';
                                            switch ($order->status) {
                                                case 'Đã gửi cho đơn vị vận chuyển':
                                                    $badgeClass = 'bg-info';
                                                    $iconClass = 'fa-paper-plane';
                                                    break;
                                                case 'Đang giao hàng':
                                                    $badgeClass = 'bg-primary';
                                                    $iconClass = 'fa-truck';
                                                    break;
                                                case 'Giao hàng thành công':
                                                    $badgeClass = 'bg-success';
                                                    $iconClass = 'fa-thumbs-up';
                                                    break;
                                                case 'Đã huỷ đơn hàng':
                                                    $badgeClass = 'bg-danger';
                                                    $iconClass = 'fa-times-circle';
                                                    break;
                                                default:
                                                    $badgeClass = 'bg-secondary';
                                                    $iconClass = 'fa-info-circle';
                                                    break;
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }} fw-semibold">
                                            <i class="fa {{ $iconClass }} me-1"></i>{{ $order->status }}
                                        </span>
                                    </td>
                                    <td data-label="Ngày tạo">
                                        {{ \Carbon\Carbon::parse($order->created_at)->format('H:i d/m/Y') }}</td>
                                    <td data-label="Hành động" class="text-center">
                                        <a href="{{ route('order.show', $order->id) }}" class="btn btn-sm btn-secondary"
                                            title="Xem chi tiết">
                                            <i class="fa fa-eye"></i>
                                        </a>


                                        {{-- Dropdown để chọn trạng thái mới --}}
                                        @if (in_array($order->status, ['Đã gửi cho đơn vị vận chuyển', 'Đang giao hàng']))
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                                    data-bs-toggle="dropdown" aria-expanded="false"
                                                    title="Cập nhật trạng thái">
                                                    <i class="fa fa-edit"></i> Cập nhật
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @if ($order->status == 'Đã gửi cho đơn vị vận chuyển')
                                                        <li>
                                                            <form method="POST"
                                                                action="{{ route('order.updateStatus', $order->id) }}"
                                                                class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="status"
                                                                    value="Đang giao hàng">
                                                                <button type="submit" class="dropdown-item"
                                                                    onclick="return confirm('Xác nhận chuyển sang trạng thái Đang giao hàng?')">
                                                                    <i class="fa fa-truck me-2"></i>Đang giao hàng
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form method="POST"
                                                                action="{{ route('order.updateStatus', $order->id) }}"
                                                                class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="status"
                                                                    value="Đã huỷ đơn hàng">
                                                                <button type="submit" class="dropdown-item text-danger"
                                                                    onclick="return confirm('Xác nhận hủy đơn hàng này?')">
                                                                    <i class="fa fa-times-circle me-2"></i>Đã huỷ đơn hàng
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @elseif($order->status == 'Đang giao hàng')
                                                        <li>
                                                            <form method="POST"
                                                                action="{{ route('order.updateStatus', $order->id) }}"
                                                                class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="status"
                                                                    value="Giao hàng thành công">
                                                                <button type="submit" class="dropdown-item text-success"
                                                                    onclick="return confirm('Xác nhận giao hàng thành công?')">
                                                                    <i class="fa fa-check me-2"></i>Giao hàng thành công
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form method="POST"
                                                                action="{{ route('order.updateStatus', $order->id) }}"
                                                                class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="status"
                                                                    value="Đã huỷ đơn hàng">
                                                                <button type="submit" class="dropdown-item text-danger"
                                                                    onclick="return confirm('Xác nhận hủy đơn hàng này?')">
                                                                    <i class="fa fa-times-circle me-2"></i>Đã huỷ đơn hàng
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">Không tìm thấy đơn hàng nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        :root {
            --primary-color: #007bff;
            --success-color: #28a745;
            --info-color: #17a2b8;
            --danger-color: #dc3545;
            --secondary-color: #6c757d;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-radius-base: 0.75rem;
            --border-radius-sm: 0.5rem;
            --border-radius-pill: 9999px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .card {
            border-radius: var(--border-radius-base);
            border: none;
        }

        .card-body {
            padding: 1.5rem;
        }

        .btn {
            border-radius: var(--border-radius-sm);
            font-weight: 500;
        }

        .form-control,
        .form-select {
            border-radius: var(--border-radius-sm);
        }

        .input-group>.form-control,
        .input-group>.btn {
            border-radius: 0 !important;
        }

        .input-group>.form-control.rounded-s-lg {
            border-top-left-radius: var(--border-radius-sm) !important;
            border-bottom-left-radius: var(--border-radius-sm) !important;
        }

        .input-group>.btn.rounded-e-lg {
            border-top-right-radius: var(--border-radius-sm) !important;
            border-bottom-right-radius: var(--border-radius-sm) !important;
        }

        .badge {
            border-radius: var(--border-radius-pill);
            padding: 0.4rem 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            text-transform: capitalize;
        }

        .badge i {
            font-size: 0.8em;
        }

        .alert {
            border-radius: var(--border-radius-sm);
        }

        .badge.bg-success {
            background-color: var(--success-color) !important;
            color: white;
        }

        .badge.bg-info {
            background-color: var(--info-color) !important;
            color: white;
        }

        .badge.bg-primary {
            background-color: var(--primary-color) !important;
            color: white;
        }

        .badge.bg-danger {
            background-color: var(--danger-color) !important;
            color: white;
        }

        .badge.bg-secondary {
            background-color: var(--secondary-color) !important;
            color: white;
        }

        .badge.bg-light {
            background-color: var(--light-color) !important;
            color: var(--dark-color);
            border: 1px solid #dee2e6;
        }

        .table {
            --bs-table-hover-bg: #f5f5f5;
        }

        .table thead th {
            border-bottom: 2px solid #e2e8f0;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody td {
            padding: 0.75rem 1rem;
        }

        .dropdown-menu {
            border-radius: var(--border-radius-sm);
            box-shadow: var(--shadow-md);
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        /* Responsive table styles */
        @media (max-width: 767px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .card-body {
                padding: 1rem;
            }

            table thead {
                display: none;
            }

            table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #e5e7eb;
                border-radius: var(--border-radius-sm);
                padding: 1rem;
                background-color: #ffffff;
                box-shadow: var(--shadow-sm);
            }

            table tbody tr td {
                display: flex;
                justify-content: space-between;
                padding: 0.5rem 0;
                border-bottom: 1px solid #f3f4f6;
            }

            table tbody tr td:last-child {
                border-bottom: none;
                flex-direction: column;
                align-items: center;
                padding-top: 0.75rem;
                gap: 0.5rem;
            }

            table tbody tr td::before {
                content: attr(data-label) ": ";
                font-weight: 600;
                color: #6b7280;
                min-width: 120px;
            }

            .table-responsive {
                overflow-x: hidden;
            }

            .d-flex.flex-wrap.gap-2 {
                gap: 0.5rem !important;
            }

            .badge.text-decoration-none {
                font-size: 0.7rem;
                padding: 0.4rem 0.6rem !important;
            }

            .btn-group {
                width: 100%;
            }

            .btn-group .btn {
                width: 100%;
            }
        }
    </style>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Clear filters button
            const clearFiltersBtn = document.getElementById('clear-filters-btn');
            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', function() {
                    window.location.href = "{{ route('order.trackingOrder') }}";
                });
            }

            // Hide filter info if no filters are applied initially
            const currentQuery = "{{ request('query') }}";
            const currentStatusFilter = "{{ request('status_filter') }}";
            const currentStartDate = "{{ request('start_date') }}";
            const currentEndDate = "{{ request('end_date') }}";
            const filterInfo = document.getElementById('filter-info');

            if (!currentQuery && !currentStatusFilter && !currentStartDate && !currentEndDate) {
                filterInfo.classList.add('hidden');
            } else {
                filterInfo.classList.remove('hidden');
            }
        });
    </script>
@endsection
@endcan

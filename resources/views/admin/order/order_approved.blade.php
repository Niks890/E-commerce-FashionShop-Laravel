{{-- @php
    dd($employeesWithDeliveryCount);
@endphp --}}
@can('salers')
    @extends('admin.master')
    @section('title', 'Đơn hàng đã xử lý')

@section('content')
    @if (Session::has('success'))
        <div class="alert alert-success d-flex align-items-center shadow-sm py-2 px-3 mb-3 js-div-dissappear"
            style="max-width: 26rem;">
            <i class="fas fa-check-circle me-2"></i>
            <span>{{ Session::get('success') }}</span>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Form tìm kiếm -->
            <form method="GET" action="{{ route('order.searchOrderApproval') }}" class="mb-3" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input name="query" type="text" class="form-control"
                                placeholder="Nhập ID đơn hàng hoặc số điện thoại..." aria-label="Tìm kiếm đơn hàng"
                                value="{{ request('query') }}" />
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-2">
                            <label class="form-label small text-muted mb-0">Trạng thái</label>
                            <select name="status_filter" class="form-select" id="statusFilter">
                                <option value="">Tất cả</option>
                                <option value="Đã xử lý" {{ request('status_filter') == 'Đã xử lý' ? 'selected' : '' }}>Đã
                                    xử lý</option>
                                <option value="Đã gửi cho đơn vị vận chuyển"
                                    {{ request('status_filter') == 'Đã gửi cho đơn vị vận chuyển' ? 'selected' : '' }}>Đã
                                    gửi vận chuyển</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="mb-2">
                            <label class="form-label small text-muted mb-0">Khoảng thời gian</label>
                            <div class="d-flex gap-2">
                                <input type="date" name="date_from" class="form-control date-filter"
                                    value="{{ request('date_from') }}" placeholder="Từ ngày">
                                <span class="align-self-center text-muted">đến</span>
                                <input type="date" name="date_to" class="form-control date-filter"
                                    value="{{ request('date_to') }}" placeholder="Đến ngày">
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Bộ lọc nhanh bằng badge -->
            <div class="mb-4">
                <h6 class="text-muted mb-2">Lọc nhanh:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('order.searchOrderApproval', ['status_filter' => '', 'date_from' => '', 'date_to' => '']) }}"
                        class="badge {{ !request('status_filter') && !request('date_from') && !request('date_to') ? 'bg-primary' : 'bg-light text-dark' }} text-decoration-none p-2">
                        <i class="fa fa-list me-1"></i>Tất cả
                        <span class="badge bg-secondary ms-1">{{ $totalApprovalCount ?? 0 }}</span>
                    </a>
                    <a href="{{ route('order.searchOrderApproval', ['status_filter' => 'Đã xử lý', 'date_from' => request('date_from'), 'date_to' => request('date_to')]) }}"
                        class="badge {{ request('status_filter') == 'Đã xử lý' ? 'bg-success' : 'bg-light text-dark' }} text-decoration-none p-2">
                        <i class="fa fa-check me-1"></i>Đã xử lý
                        <span class="badge bg-secondary ms-1">{{ $processedCount ?? 0 }}</span>
                    </a>
                    <a href="{{ route('order.searchOrderApproval', ['status_filter' => 'Đã gửi cho đơn vị vận chuyển', 'date_from' => request('date_from'), 'date_to' => request('date_to')]) }}"
                        class="badge {{ request('status_filter') == 'Đã gửi cho đơn vị vận chuyển' ? 'bg-info' : 'bg-light text-dark' }} text-decoration-none p-2">
                        <i class="fa fa-truck me-1"></i>Đã gửi vận chuyển
                        <span class="badge bg-secondary ms-1">{{ $shippingCount ?? 0 }}</span>
                    </a>
                    @if (request('date_from') || request('date_to'))
                        <a href="{{ route('order.searchOrderApproval', ['status_filter' => request('status_filter'), 'date_from' => '', 'date_to' => '']) }}"
                            class="badge bg-light text-dark text-decoration-none p-2">
                            <i class="fa fa-times me-1"></i>Xóa lọc ngày
                        </a>
                    @endif
                </div>
            </div>

            <!-- Hiển thị thông tin lọc hiện tại -->
            @if (request('status_filter') || request('query') || request('date_from') || request('date_to'))
                <div class="alert alert-info d-flex align-items-center mb-3">
                    <i class="fa fa-info-circle me-2"></i>
                    <span>
                        Đang hiển thị:
                        @if (request('status_filter'))
                            <strong>{{ request('status_filter') }}</strong>
                        @else
                            <strong>Tất cả trạng thái</strong>
                        @endif
                        @if (request('query'))
                            | Tìm kiếm: <strong>"{{ request('query') }}"</strong>
                        @endif
                        @if (request('date_from') || request('date_to'))
                            | Khoảng thời gian:
                            <strong>
                                {{ request('date_from') ? date('d/m/Y', strtotime(request('date_from'))) : '...' }}
                                đến
                                {{ request('date_to') ? date('d/m/Y', strtotime(request('date_to'))) : '...' }}
                            </strong>
                        @endif
                        | Tổng: <strong>{{ $data->total() }}</strong> đơn hàng
                    </span>
                    <a href="{{ route('order.searchOrderApproval') }}" class="btn btn-sm btn-outline-secondary ms-auto">
                        <i class="fa fa-times me-1"></i>Xóa bộ lọc
                    </a>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light text-uppercase text-secondary">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Tên khách hàng</th>
                            <th scope="col">Địa chỉ</th>
                            <th scope="col">Số điện thoại</th>
                            <th scope="col">Tổng tiền</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col">Ngày tạo</th>
                            <th scope="col" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $model)
                            <tr>
                                <td data-label="ID">{{ $model->id }}</td>
                                <td data-label="Tên khách hàng">{{ $model->customer_name }}</td>
                                <td data-label="Địa chỉ">{{ $model->address }}</td>
                                <td data-label="Số điện thoại">{{ $model->phone }}</td>
                                <td data-label="Tổng tiền">{{ number_format($model->total, 0, ',', '.') }} đ</td>
                                <td data-label="Trạng thái">
                                    @if ($model->status === 'Đã xử lý')
                                        <span class="badge bg-success fw-semibold">
                                            <i class="fa fa-check me-1"></i>{{ $model->status }}
                                        </span>
                                    @elseif ($model->status === 'Đã gửi cho đơn vị vận chuyển')
                                        <span class="badge bg-info fw-semibold">
                                            <i class="fa fa-truck me-1"></i>{{ $model->status }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">{{ $model->status }}</span>
                                    @endif
                                </td>
                                <td data-label="Ngày tạo">{{ $model->created_at }}</td>
                                <td data-label="Hành động" class="text-center">
                                    <a href="{{ route('order.show', $model->id) }}" class="btn btn-sm btn-secondary me-1"
                                        title="Xem chi tiết">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    @if ($model->status === 'Đã xử lý')
                                        <button class="btn btn-sm btn-success btn-assign-shipper"
                                            data-order-id="{{ $model->id }}" title="Gửi cho đơn vị vận chuyển"
                                            type="button">
                                            <i class="fa fa-truck me-1"></i>Giao hàng
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fa fa-inbox fa-2x mb-2 d-block"></i>
                                    @if (request('status_filter') || request('query'))
                                        Không tìm thấy đơn hàng nào phù hợp với bộ lọc.
                                    @else
                                        Không có đơn hàng nào.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $data->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- Modal chọn nhân viên giao hàng-->
    <div class="modal fade" id="assignShipperModal" tabindex="-1" aria-labelledby="assignShipperModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="assignShipperForm" method="POST"
                action="{{ route('order.updateOrderStatusDelivery', ['id' => '__ORDER_ID__']) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="order_id" id="modalOrderId" value="">
                <div class="modal-content border-0 shadow-lg">
                    <!-- Modal Header -->
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fs-5" id="assignShipperModalLabel">
                            <i class="fas fa-truck me-2"></i>Phân công giao hàng
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Đóng"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label for="shipperSelect" class="form-label fw-semibold mb-3">
                                <i class="fas fa-user-tie me-2"></i>Chọn nhân viên giao hàng
                            </label>
                            <select id="shipperSelect" name="shipper_id" class="form-select form-select-lg py-2"
                                required>
                                <option value="" selected disabled>-- Chọn nhân viên --</option>
                                @foreach ($employeesWithDeliveryCount as $employee)
                                    <option value="{{ $employee->staff_id }}"
                                        data-count="{{ $employee->delivery_count }}">
                                        {{ $employee->staff_name }}
                                        <span class="badge bg-info text-dark ms-2">
                                            <i class="fas fa-box-open me-1"></i>
                                            {{ $employee->delivery_count }} đơn hôm nay
                                        </span>
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="updated_by" id="updatedByInput" value="">
                            <div class="form-text mt-2">
                                <i class="fas fa-info-circle me-1"></i> Vui lòng chọn nhân viên có ít đơn giao nhất để cân
                                bằng tải
                            </div>
                        </div>

                        <div class="alert alert-warning mt-3 d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2 fs-5"></i>
                            <div>
                                <strong>Lưu ý:</strong> Sau khi xác nhận, đơn hàng sẽ được chuyển trạng thái sang "Đã gửi cho đơn vị vận chuyển"
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Hủy
                        </button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-check-circle me-1"></i> Xác nhận
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
    <style>
        /* Badge hover effects */
        .badge.text-decoration-none:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        /* Status badges with icons */
        .badge i {
            font-size: 0.875em;
        }

        /* Responsive table */
        @media (max-width: 575.98px) {
            table thead {
                display: none;
            }

            table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
                padding: 1rem;
                background: #fff;
            }

            table tbody tr td {
                display: flex;
                justify-content: space-between;
                padding: 0.25rem 0.5rem;
                border: none;
                border-bottom: 1px solid #dee2e6;
            }

            table tbody tr td:last-child {
                border-bottom: none;
                justify-content: center;
                padding-top: 0.75rem;
            }

            table tbody tr td::before {
                content: attr(data-label) ": ";
                font-weight: 600;
                color: #6c757d;
                min-width: 120px;
            }

            /* Mobile filter adjustments */
            .d-flex.flex-wrap.gap-2 {
                gap: 0.5rem !important;
            }

            .badge.text-decoration-none {
                font-size: 0.75rem;
                padding: 0.5rem !important;
            }
        }

        @media (max-width: 768px) {

            .col-md-8,
            .col-md-4 {
                margin-bottom: 0.5rem;
            }
        }
    </style>
@endsection

@section('js')
    @if (Session::has('success'))
        <script src="{{ asset('assets/js/message.js') }}"></script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const assignButtons = document.querySelectorAll('.btn-assign-shipper');
            const modal = new bootstrap.Modal(document.getElementById('assignShipperModal'));
            const modalOrderIdInput = document.getElementById('modalOrderId');
            const shipperSelect = document.getElementById('shipperSelect');
            const updatedByInput = document.getElementById('updatedByInput');
            const assignShipperForm = document.getElementById('assignShipperForm');



            const dateFilters = document.querySelectorAll('.date-filter');
            const filterForm = document.getElementById('filterForm');

            dateFilters.forEach(function(filter) {
                filter.addEventListener('change', function() {
                    const dateFrom = document.querySelector('input[name="date_from"]').value;
                    const dateTo = document.querySelector('input[name="date_to"]').value;

                    if (dateFrom || dateTo) {
                        setTimeout(() => {
                            filterForm.submit();
                        }, 300);
                    }
                });
            });

            // Highlight option có ít đơn nhất
            function highlightLeastBusyShipper() {
                const options = shipperSelect.querySelectorAll('option[data-count]');
                let leastCount = Infinity;
                let bestOption = null;

                options.forEach(option => {
                    const count = parseInt(option.getAttribute('data-count'));
                    if (count < leastCount) {
                        leastCount = count;
                        bestOption = option;
                    }
                });

                if (bestOption) {
                    bestOption.style.backgroundColor = '#e8f5e9';
                    bestOption.innerHTML =
                        `${bestOption.textContent} <span class="badge bg-success ms-2"><i class="fas fa-thumbs-up me-1"></i>Ít đơn nhất</span>`;
                }
            }

            assignButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const orderId = button.getAttribute('data-order-id');
                    modalOrderIdInput.value = orderId;

                    // Cập nhật action của form với order_id
                    const formAction = assignShipperForm.getAttribute('action').replace(
                        '__ORDER_ID__', orderId);
                    assignShipperForm.setAttribute('action', formAction);

                    // Reset và highlight khi mở modal
                    shipperSelect.value = "";
                    highlightLeastBusyShipper();

                    modal.show();
                });
            });

            // Khi chọn shipper, cập nhật luôn vào trường updated_by
            shipperSelect.addEventListener('change', function() {
                updatedByInput.value = this.value;
            });
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('filterForm');
            const filterElements = [
                ...document.querySelectorAll('.date-filter'),
                document.getElementById('statusFilter')
            ];

            filterElements.forEach(element => {
                element.addEventListener('change', function() {
                    setTimeout(() => {
                        filterForm.submit();
                    }, 300);
                });
            });
        });
    </script>
@endsection
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

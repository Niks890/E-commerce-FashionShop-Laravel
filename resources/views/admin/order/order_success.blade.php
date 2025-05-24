@can('salers')
@extends('admin.master')
@section('title', 'Đơn hàng đã thanh toán thành công')

@section('content')
    @if (Session::has('success'))
        <div class="alert alert-success d-flex align-items-center shadow-sm py-2 px-3 mb-3 js-div-dissappear" style="max-width: 26rem;">
            <i class="fas fa-check-circle me-2"></i>
            <span>{{ Session::get('success') }}</span>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Form tìm kiếm -->
            <form method="GET" action="{{ route('order.searchOrderSuccess') }}" class="mb-3">
                {{-- @csrf --}}
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input name="query" type="text" class="form-control"
                                   placeholder="Nhập ID đơn hàng hoặc số điện thoại để tìm kiếm..."
                                   aria-label="Tìm kiếm đơn hàng"
                                   value="{{ request('query') }}" />
                            <button class="btn btn-primary" type="submit" aria-label="Tìm kiếm">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="status_filter" class="form-select" onchange="this.form.submit()">
                            <option value="">Tất cả trạng thái</option>
                            <option value="Đã giao hàng" {{ request('status_filter') == 'Đã giao hàng' ? 'selected' : '' }}>
                                Đã giao hàng
                            </option>
                            <option value="Đã thanh toán" {{ request('status_filter') == 'Đã thanh toán' ? 'selected' : '' }}>
                                Đã thanh toán
                            </option>
                        </select>
                    </div>
                </div>
            </form>

            <!-- Bộ lọc nhanh bằng badge -->
            <div class="mb-4">
                <h6 class="text-muted mb-2">Lọc nhanh:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('order.searchOrderSuccess', ['status_filter' => '']) }}"
                       class="badge {{ !request('status_filter') ? 'bg-primary' : 'bg-light text-dark' }} text-decoration-none p-2">
                        <i class="fa fa-list me-1"></i>Tất cả
                        <span class="badge bg-secondary ms-1">{{ $totalSuccessCount ?? 0 }}</span>
                    </a>
                    <a href="{{ route('order.searchOrderSuccess', ['status_filter' => 'Đã giao hàng']) }}"
                       class="badge {{ request('status_filter') == 'Đã giao hàng' ? 'bg-success' : 'bg-light text-dark' }} text-decoration-none p-2">
                        <i class="fa fa-check-circle me-1"></i>Đã giao hàng
                        <span class="badge bg-secondary ms-1">{{ $deliveredCount ?? 0 }}</span>
                    </a>
                    <a href="{{ route('order.searchOrderSuccess', ['status_filter' => 'Đã thanh toán']) }}"
                       class="badge {{ request('status_filter') == 'Đã thanh toán' ? 'bg-success' : 'bg-light text-dark' }} text-decoration-none p-2">
                        <i class="fa fa-money-bill me-1"></i>Đã thanh toán
                        <span class="badge bg-secondary ms-1">{{ $paidCount ?? 0 }}</span>
                    </a>
                </div>
            </div>

            <!-- Hiển thị thông tin lọc hiện tại -->
            @if(request('status_filter') || request('query'))
                <div class="alert alert-info d-flex align-items-center mb-3">
                    <i class="fa fa-info-circle me-2"></i>
                    <span>
                        Đang hiển thị:
                        @if(request('status_filter'))
                            <strong>{{ request('status_filter') }}</strong>
                        @else
                            <strong>Tất cả trạng thái</strong>
                        @endif
                        @if(request('query'))
                            | Tìm kiếm: <strong>"{{ request('query') }}"</strong>
                        @endif
                        | Tổng: <strong>{{ $data->total() }}</strong> đơn hàng
                    </span>
                    <a href="{{ route('order.searchOrderSuccess') }}" class="btn btn-sm btn-outline-secondary ms-auto">
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
                                    @if ($model->status === 'Đã giao hàng')
                                        <span class="badge bg-success fw-semibold">
                                            <i class="fa fa-check-circle me-1"></i>{{ $model->status }}
                                        </span>
                                    @elseif ($model->status === 'Đã thanh toán')
                                        <span class="badge bg-success fw-semibold">
                                            <i class="fa fa-money-bill me-1"></i>{{ $model->status }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">{{ $model->status }}</span>
                                    @endif
                                </td>
                                <td data-label="Ngày tạo">{{ $model->created_at }}</td>
                                <td data-label="Hành động" class="text-center">
                                    <a href="{{ route('order.show', $model->id) }}" class="btn btn-sm btn-secondary" title="Xem chi tiết">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    @if ($model->status === 'Đã giao hàng')
                                        <button
                                            class="btn btn-sm btn-success btn-mark-paid"
                                            data-order-id="{{ $model->id }}"
                                            title="Đánh dấu đã thanh toán"
                                            type="button"
                                            >
                                            <i class="fa fa-money-bill me-1"></i>Thanh toán
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fa fa-inbox fa-2x mb-2 d-block"></i>
                                    @if(request('status_filter') || request('query'))
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
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
    <style>
        /* Badge hover effects */
        .badge.text-decoration-none:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
            .col-md-8, .col-md-4 {
                margin-bottom: 0.5rem;
            }
        }
    </style>
@endsection

@section('js')
    @if (Session::has('success'))
        <script src="{{ asset('assets/js/message.js') }}"></script>
    @endif
@endsection

@else
    {{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

@can('salers')
@extends('admin.master')
@section('title', 'Đơn hàng chưa xử lý hoặc đã bị huỷ')

@section('content')
    @if (Session::has('success'))
        <div class="alert alert-success d-flex align-items-center shadow-sm py-2 px-3 mb-3 js-div-dissappear" style="max-width: 26rem;">
            <i class="fas fa-check-circle me-2"></i>
            <span>{{ Session::get('success') }}</span>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('order.search') }}" class="mb-3">
                @csrf
                <div class="input-group">
                    <input name="query" type="text" class="form-control" placeholder="Nhập ID đơn hàng hoặc số điện thoại để tìm kiếm..." aria-label="Tìm kiếm đơn hàng" />
                    <button class="btn btn-primary" type="submit" aria-label="Tìm kiếm">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>

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
                                <td>{{ $model->id }}</td>
                                <td>{{ $model->customer_name }}</td>
                                <td>{{ $model->address }}</td>
                                <td>{{ $model->phone }}</td>
                                <td>{{ number_format($model->total, 0, ',', '.') }} đ</td>
                                <td>
                                    @if ($model->status === 'Chờ xử lý')
                                        <span class="badge bg-warning fw-semibold">{{ $model->status }}</span>
                                    @elseif ($model->status === 'Đã huỷ đơn hàng')
                                        <span class="badge bg-danger fw-semibold">{{ $model->status }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $model->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $model->created_at}}</td>
                                <td class="text-center">
                                    <a href="{{ route('order.show', $model->id) }}" class="btn btn-sm btn-secondary me-1" title="Xem chi tiết">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @if ($model->status === 'Chờ xử lý')
                                        <form action="{{ route('order.update', $model->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-success">
                                                Duyệt đơn
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">Không có đơn hàng nào.</td>
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
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
    <style>
        /* Một số tùy chỉnh cho responsive nhỏ hơn */
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
                content: attr(data-label);
                font-weight: 600;
                color: #6c757d;
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

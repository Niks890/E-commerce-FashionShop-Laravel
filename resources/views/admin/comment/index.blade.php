@can('managers')
    @extends('admin.master')

    @section('title', 'Danh sách bình luận')

@section('content')

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-comments text-primary mr-2"></i>Quản lý bình luận
            </h1>
            <div class="d-none d-sm-inline-block">
                <span class="badge badge-pill badge-primary shadow-sm px-3 py-2">
                    <i class="fas fa-database mr-1"></i>
                    Tổng: <strong>{{ $data->total() }}</strong> bình luận
                </span>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card shadow mb-4 border-top-primary">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-sliders-h mr-2"></i>Bộ lọc nâng cao
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('comment.index') }}" method="GET" id="filterForm">
                    <div class="row">
                        <!-- Search Column -->
                        <div class="col-md-3 mb-3">
                            <label for="search" class="form-label font-weight-bold text-gray-700">
                                <i class="fas fa-search text-primary mr-1"></i>Tìm kiếm
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light"><i
                                            class="fas fa-keyboard text-muted"></i></span>
                                </div>
                                <input type="text" class="form-control" name="search" id="search"
                                    value="{{ request('search') }}" placeholder="Tên KH, sản phẩm hoặc nội dung...">
                            </div>
                        </div>

                        <!-- Rating Column -->
                        <div class="col-md-2 mb-3">
                            <label for="rating" class="form-label font-weight-bold text-gray-700">
                                <i class="fas fa-star text-warning mr-1"></i>Đánh giá
                            </label>
                            <select class="form-control selectpicker" name="rating" id="rating"
                                title="Chọn mức đánh giá">
                                <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>
                                    ⭐⭐⭐⭐⭐ (5 sao)
                                </option>
                                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>
                                    ⭐⭐⭐⭐ (4 sao)
                                </option>
                                <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>
                                    ⭐⭐⭐ (3 sao)
                                </option>
                                <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>
                                    ⭐⭐ (2 sao)
                                </option>
                                <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>
                                    ⭐ (1 sao)
                                </option>
                            </select>
                        </div>

                        <!-- Status Column -->
                        <div class="col-md-2 mb-3">
                            <label for="status" class="form-label font-weight-bold text-gray-700">
                                <i class="fas fa-eye text-info mr-1"></i>Trạng thái
                            </label>
                            <select class="form-control selectpicker" name="status" id="status" title="Chọn trạng thái">
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>
                                    Hiển thị
                                </option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>
                                    Đã ẩn
                                </option>
                            </select>
                        </div>

                        <!-- Date Range Columns -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label font-weight-bold text-gray-700">
                                <i class="fas fa-calendar-alt text-info mr-1"></i>Khoảng thời gian
                            </label>
                            <div class="input-daterange input-group">
                                <input type="date" class="form-control" name="date_from" id="date_from"
                                    value="{{ request('date_from') }}" placeholder="Từ ngày">
                                <div class="input-group-append">
                                    <span class="input-group-text bg-light">đến</span>
                                </div>
                                <input type="date" class="form-control" name="date_to" id="date_to"
                                    value="{{ request('date_to') }}" placeholder="Đến ngày">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <div class="w-100">
                                <button type="submit" class="btn btn-primary btn-block mb-2">
                                    <i class="fas fa-filter mr-1"></i> Áp dụng
                                </button>
                                <a href="{{ route('comment.index') }}" class="btn btn-outline-secondary btn-block">
                                    <i class="fas fa-sync-alt mr-1"></i> Đặt lại
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Active Filters -->
        @if (request()->hasAny(['search', 'rating', 'status', 'date_from', 'date_to']))
            <div class="alert alert-light shadow-sm mb-4 border-left-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-filter text-info mr-2"></i>
                        <strong>Bộ lọc đang áp dụng:</strong>

                        @if (request('search'))
                            <span class="badge badge-light border border-info text-info mx-1">
                                Từ khóa: "{{ request('search') }}"
                                <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="text-danger ml-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif

                        @if (request('rating'))
                            <span class="badge badge-light border border-warning text-warning mx-1">
                                {{ request('rating') }} sao
                                <a href="{{ request()->fullUrlWithQuery(['rating' => null]) }}" class="text-danger ml-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif

                        @if (request('status') !== null)
                            <span
                                class="badge badge-light border {{ request('status') == '1' ? 'border-success text-success' : 'border-secondary text-secondary' }} mx-1">
                                {{ request('status') == '1' ? 'Hiển thị' : 'Đã ẩn' }}
                                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="text-danger ml-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif

                        @if (request('date_from') && request('date_to'))
                            <span class="badge badge-light border border-info text-info mx-1">
                                {{ date('d/m/Y', strtotime(request('date_from'))) }} -
                                {{ date('d/m/Y', strtotime(request('date_to'))) }}
                                <a href="{{ request()->fullUrlWithQuery(['date_from' => null, 'date_to' => null]) }}"
                                    class="text-danger ml-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif
                    </div>
                    <a href="{{ route('comment.index') }}" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-times mr-1"></i>Xóa tất cả
                    </a>
                </div>
            </div>
        @endif

        <!-- Data Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list-ol mr-2"></i>Danh sách bình luận
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Tùy chọn:</div>
                            <a class="dropdown-item" href="#"><i class="fas fa-file-export mr-2"></i>Xuất Excel</a>
                            <a class="dropdown-item" href="#"><i class="fas fa-print mr-2"></i>In danh sách</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if ($data->count() > 0)
                    <div class="alert alert-light alert-dismissible fade show mb-3 border-left-info" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle text-info mr-3 fa-2x"></i>
                            <div>
                                Đang hiển thị <strong>{{ $data->firstItem() }} - {{ $data->lastItem() }}</strong>
                                trong tổng số <strong>{{ $data->total() }}</strong> bình luận
                                @if (request()->hasAny(['search', 'rating', 'status', 'date_from', 'date_to']))
                                    (đã lọc)
                                @endif
                            </div>
                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead class="bg-light text-gray-800">
                            <tr>
                                <th width="5%" class="text-center">ID</th>
                                <th width="15%">Khách hàng</th>
                                <th width="15%">Sản phẩm</th>
                                <th width="10%" class="text-center">Đánh giá</th>
                                <th width="25%">Nội dung</th>
                                <th width="10%" class="text-center">Ngày đăng</th>
                                <th width="10%" class="text-center">Trạng thái</th>
                                <th width="10%" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $item)
                                <tr>
                                    <td class="text-center font-weight-bold">{{ $item->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($item->customer)
                                                <img src="{{ $item->customer->image ?? asset('client/img/user.png') }}"
                                                    class="rounded-circle mr-2" width="30" height="30"
                                                    alt="Avatar">
                                                <div>
                                                    <div class="font-weight-bold">{{ $item->customer->name }}</div>
                                                    <small class="text-muted">{{ $item->customer->email }}</small>
                                                </div>
                                            @else
                                                <i class="fas fa-user-circle fa-lg text-muted mr-2"></i>
                                                <div>
                                                    <div class="text-muted">Khách vãng lai</div>
                                                    <small class="text-muted">N/A</small>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if ($item->product)
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item->product->image ?? asset('admin/img/default-product.png') }}"
                                                    class="rounded mr-2" width="30" height="30" alt="Product">
                                                <div class="text-truncate" style="max-width: 150px;"
                                                    title="{{ $item->product->product_name }}">
                                                    {{ $item->product->product_name }}
                                                </div>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-box-open fa-lg text-muted mr-2"></i>
                                                <div class="text-muted">Sản phẩm đã xóa</div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (isset($item->star))
                                            <div class="star-rating">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= $item->star)
                                                        <i class="fas fa-star text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-muted"></i>
                                                    @endif
                                                @endfor
                                                <div class="small text-muted mt-1">{{ $item->star }} sao</div>
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="comment-content" style="max-height: 60px; overflow: hidden;">
                                            {{ $item->content }}
                                        </div>
                                        @if (strlen($item->content) > 100)
                                            <a href="#" class="text-primary small view-more"
                                                data-content="{{ $item->content }}">Xem thêm</a>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="small font-weight-bold">
                                            {{ $item->created_at->format('d/m/Y') }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ $item->created_at->format('H:i') }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if ($item->status == 1)
                                            <span class="badge badge-success badge-pill px-3">
                                                <i class="fas fa-check-circle mr-1"></i>Hiển thị
                                            </span>
                                        @else
                                            <span class="badge badge-secondary badge-pill px-3">
                                                <i class="fas fa-ban mr-1"></i>Đã ẩn
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            @if ($item->status == 1)
                                                <button type="button" class="btn btn-warning btn-sm"
                                                    title="Ẩn bình luận"
                                                    onclick="confirmAction({{ $item->id }}, 'hide', '{{ $item->customer ? $item->customer->name : 'Khách vãng lai' }}')">
                                                    <i class="fa fa-eye-slash"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-success btn-sm"
                                                    title="Hiển thị bình luận"
                                                    onclick="confirmAction({{ $item->id }}, 'show', '{{ $item->customer ? $item->customer->name : 'Khách vãng lai' }}')">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-danger btn-sm" title="Xóa bình luận"
                                                onclick="confirmAction({{ $item->id }}, 'delete', '{{ $item->customer ? $item->customer->name : 'Khách vãng lai' }}')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-comment-slash fa-3x text-gray-400 mb-3"></i>
                                            <h5 class="text-gray-700">Không tìm thấy bình luận nào</h5>
                                            <p class="text-muted">Không có bình luận nào phù hợp với tiêu chí lọc của bạn
                                            </p>
                                            @if (request()->hasAny(['search', 'rating', 'status', 'date_from', 'date_to']))
                                                <a href="{{ route('comment.index') }}" class="btn btn-primary mt-2">
                                                    <i class="fas fa-undo mr-1"></i>Xem tất cả bình luận
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($data->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted small">
                                Hiển thị {{ $data->firstItem() }} - {{ $data->lastItem() }}
                                trong tổng số {{ $data->total() }} bản ghi
                            </div>
                            <div>
                                {{ $data->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <!-- Modal xác nhận -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title font-weight-bold" id="confirmModalLabel">
                        <i class="fas fa-exclamation-triangle text-warning mr-2"></i>Xác nhận thao tác
                    </h5>
                    <button type="button" class="close btn-close-modal-x" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4">
                    <p id="confirmMessage" class="mb-0"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close-modal" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Hủy bỏ
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmBtn">
                        <i class="fas fa-check mr-1"></i>Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Form for Actions -->
    <form id="actionForm" method="POST" style="display: none;">
        @csrf
        @method('PUT')
    </form>

@endsection

@section('js')
    <script>
        function confirmAction(id, action, customerName) {
            let message = '';
            let actionUrl = '';
            let btnClass = 'btn-primary';
            let btnIcon = 'fas fa-check';

            switch (action) {
                case 'hide':
                    message =
                        `Bạn có chắc chắn muốn <strong>ẩn</strong> bình luận của khách hàng "<strong>${customerName}</strong>"?`;
                    actionUrl = "{{ route('comment.update', ':id') }}".replace(':id', id);
                    btnClass = 'btn-warning';
                    btnIcon = 'fas fa-eye-slash';
                    break;
                case 'show':
                    message =
                        `Bạn có chắc chắn muốn <strong>hiển thị</strong> bình luận của khách hàng "<strong>${customerName}</strong>"?`;
                    actionUrl = "{{ route('comment.update', ':id') }}".replace(':id', id);
                    btnClass = 'btn-success';
                    btnIcon = 'fas fa-eye';
                    break;
                case 'delete':
                    message =
                        `Bạn có chắc chắn muốn <strong class="text-danger">xóa vĩnh viễn</strong> bình luận của khách hàng "<strong>${customerName}</strong>"?<br><small class="text-muted">⚠️ Thao tác này không thể hoàn tác!</small>`;
                    actionUrl = "{{ route('comment.destroy', ':id') }}".replace(':id', id);
                    btnClass = 'btn-danger';
                    btnIcon = 'fas fa-trash';
                    document.getElementById('actionForm').innerHTML = '@csrf @method('DELETE')';
                    break;
            }

            document.getElementById('confirmMessage').innerHTML = message;
            document.getElementById('confirmBtn').className = 'btn ' + btnClass;
            document.getElementById('confirmBtn').innerHTML = `<i class="${btnIcon} mr-1"></i>Xác nhận`;
            document.getElementById('actionForm').action = actionUrl;

            if (action !== 'delete') {
                document.getElementById('actionForm').innerHTML = '@csrf @method('PUT')';
            }

            $('#confirmModal').modal('show');
        }

        document.getElementById('confirmBtn').addEventListener('click', function() {
            document.getElementById('actionForm').submit();
        });

        document.querySelector('.btn-close-modal').addEventListener('click', function() {
            $('#confirmModal').modal('hide');
        });

        document.querySelector('.btn-close-modal-x').addEventListener('click', function() {
            $('#confirmModal').modal('hide');
        });

        // Auto-submit form when date changes
        document.getElementById('date_from').addEventListener('change', function() {
            if (this.value && document.getElementById('date_to').value) {
                if (this.value > document.getElementById('date_to').value) {
                    alert('Ngày bắt đầu không thể lớn hơn ngày kết thúc!');
                    this.value = '';
                }
            }
        });

        document.getElementById('date_to').addEventListener('change', function() {
            if (this.value && document.getElementById('date_from').value) {
                if (this.value < document.getElementById('date_from').value) {
                    alert('Ngày kết thúc không thể nhỏ hơn ngày bắt đầu!');
                    this.value = '';
                }
            }
        });
    </script>
@endsection

@section('styles')
    <style>
        /* Custom styles */
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            border: none;
        }

        .card-header {
            border-radius: 0.5rem 0.5rem 0 0 !important;
            padding: 1rem 1.25rem;
        }

        .table {
            font-size: 0.875rem;
        }

        .table th {
            border-top: none;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .star-rating {
            line-height: 1;
        }

        .empty-state {
            padding: 2rem;
            text-align: center;
        }

        .badge-pill {
            padding: 0.5em 1em;
        }

        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
        }

        .text-truncate {
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .comment-content {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .border-top-primary {
            border-top: 4px solid #4e73df !important;
        }

        .border-left-info {
            border-left: 4px solid #36b9cc !important;
        }
    </style>
@endsection
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

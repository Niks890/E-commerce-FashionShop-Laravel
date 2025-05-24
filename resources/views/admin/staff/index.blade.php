@can('managers')
@extends('admin.master')
@section('title', 'Thông tin Nhân viên')

@section('content')
@if (Session::has('success'))
    <div class="alert alert-success alert-dismissible fade show shadow js-div-dissappear" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ Session::get('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-header">
        <div class="row g-3">
            <!-- Form tìm kiếm và lọc -->
            <div class="col-12">
                <form method="GET" action="{{ route('staff.index') }}" class="row g-2 align-items-end">
                    <!-- Tìm kiếm theo tên, phone, email -->
                    <div class="col-md-4">
                        <label class="form-label small">Tìm kiếm</label>
                        <div class="input-group">
                            <input name="search" type="text" class="form-control"
                                   placeholder="Nhập tên, điện thoại, email..."
                                   value="{{ request('search') }}" />
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Lọc theo trạng thái -->
                    <div class="col-md-3">
                        <label class="form-label small">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}"
                                        {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Lọc theo chức vụ -->
                    <div class="col-md-3">
                        <label class="form-label small">Chức vụ</label>
                        <select name="position" class="form-select">
                            <option value="">Tất cả chức vụ</option>
                            @foreach($positions as $position)
                                <option value="{{ $position }}"
                                        {{ request('position') == $position ? 'selected' : '' }}>
                                    {{ $position }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Các nút hành động -->
                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fa fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('staff.index') }}" class="btn btn-danger flex-fill" title="Xóa bộ lọc">
                        Xoá bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <!-- Nút thêm mới -->
            <div class="col-12 text-end">
                <a href="{{ route('staff.create') }}" class="btn btn-success">
                    <i class="fa fa-plus me-1"></i> Thêm mới
                </a>
            </div>
        </div>

        <!-- Hiển thị kết quả tìm kiếm -->
        @if(request()->hasAny(['search', 'status', 'position']))
            <div class="mt-3">
                <small class="text-muted">
                    Kết quả lọc: {{ $data->total() }} nhân viên
                    @if(request('search'))
                        | Từ khóa: <strong>{{ request('search') }}</strong>
                    @endif
                    @if(request('status'))
                        | Trạng thái: <strong>{{ request('status') }}</strong>
                    @endif
                    @if(request('position'))
                        | Chức vụ: <strong>{{ request('position') }}</strong>
                    @endif
                </small>
            </div>
        @endif
    </div>

    <div class="card-body table-responsive">
        @if($data->count() > 0)
            <table class="table table-hover align-middle text-nowrap">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Họ tên</th>
                        <th>Avatar</th>
                        <th>Điện thoại</th>
                        <th>Địa chỉ</th>
                        <th>Email</th>
                        <th>Giới tính</th>
                        <th>Chức vụ</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $model)
                    <tr>
                        <td>{{ $model->id }}</td>
                        <td>{{ $model->name }}</td>
                        <td><img src="{{ $model->avatar }}" alt="" width="45" class="rounded"></td>
                        <td>{{ $model->phone }}</td>
                        <td>{{ $model->address }}</td>
                        <td>{{ $model->email }}</td>
                        <td>{{ $model->sex == 1 ? 'Nam' : 'Nữ' }}</td>
                        <td>{{ $model->position }}</td>
                        <td>
                            <span class="badge
                                @if($model->status == 'Đang làm việc') bg-success
                                @elseif($model->status == 'Đã nghỉ việc') bg-danger
                                @elseif($model->status == 'Tạm nghỉ') bg-warning
                                @else bg-secondary
                                @endif">
                                {{ $model->status }}
                            </span>
                        </td>
                        <td class="text-center">
                            <form method="POST" action="{{ route('staff.destroy', $model->id) }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-secondary btn-detail" title="Chi tiết">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <a href="{{ route('staff.edit', $model->id) }}" class="btn btn-sm btn-primary" title="Sửa">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa không?')" title="Xóa">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $data->links() }}
        @else
            <div class="text-center py-4">
                <i class="fa fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Không tìm thấy nhân viên nào</h5>
                <p class="text-muted">Hãy thử điều chỉnh các tiêu chí tìm kiếm</p>
                <a href="{{ route('staff.index') }}" class="btn btn-outline-primary">
                    <i class="fa fa-refresh me-1"></i> Xem tất cả
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Modal chi tiết --}}
<div class="modal fade" id="staffDetail" tabindex="-1" aria-labelledby="staffDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    Thông tin chi tiết: <span id="staff-info"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <tbody>
                            <tr><th>Mã nhân viên</th><td id="staff-id"></td></tr>
                            <tr><th>Tên nhân viên</th><td id="staff-name"></td></tr>
                            <tr><th>Avatar</th><td id="staff-avatar"><img id="staff-avatar-img" width="100" src="" alt=""></td></tr>
                            <tr><th>Giới tính</th><td id="staff-sex"></td></tr>
                            <tr><th>Địa chỉ</th><td id="staff-address"></td></tr>
                            <tr><th>Số điện thoại</th><td id="staff-phone"></td></tr>
                            <tr><th>Email</th><td id="staff-email"></td></tr>
                            <tr><th>Chức vụ</th><td id="staff-position"></td></tr>
                            <tr><th>Trạng thái</th><td id="staff-status"></td></tr>
                            <tr><th>Ngày thêm</th><td id="staff-createDate"></td></tr>
                            <tr><th>Ngày cập nhật</th><td id="staff-updateDate"></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/message.css') }}">
@endsection

@section('js')
@if (Session::has('success'))
<script src="{{ asset('assets/js/message.js') }}"></script>
@endif

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll(".btn-detail").forEach(button => {
        button.addEventListener("click", async (event) => {
            event.preventDefault();
            const row = button.closest("tr");
            const staffId = row.querySelector("td:first-child").textContent.trim();

            try {
                const res = await fetch(`/api/staff/${staffId}`);
                const result = await res.json();
                if (result.status_code === 200) {
                    const staff = result.data;
                    document.getElementById("staff-info").textContent = staff.name;
                    document.getElementById("staff-id").textContent = staff.id;
                    document.getElementById("staff-name").textContent = staff.name;
                    document.getElementById("staff-avatar-img").src = staff.avatar;
                    document.getElementById("staff-sex").textContent = staff.sex === 1 ? "Nam" : "Nữ";
                    document.getElementById("staff-address").textContent = staff.address;
                    document.getElementById("staff-phone").textContent = staff.phone;
                    document.getElementById("staff-email").textContent = staff.email;
                    document.getElementById("staff-position").textContent = staff.position;
                    document.getElementById("staff-status").textContent = staff.status;
                    document.getElementById("staff-createDate").textContent = new Date(staff.created_at).toLocaleString("vi-VN");
                    document.getElementById("staff-updateDate").textContent = new Date(staff.updated_at).toLocaleString("vi-VN");

                    const modal = new bootstrap.Modal(document.getElementById("staffDetail"));
                    modal.show();
                } else {
                    alert("Không thể lấy dữ liệu chi tiết!");
                }
            } catch (err) {
                alert("Đã có lỗi xảy ra, vui lòng thử lại!");
            }
        });
    });
});
</script>
@endsection
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

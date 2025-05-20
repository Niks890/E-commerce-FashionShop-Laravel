@can('managers')
@extends('admin.master')

@section('title', 'Sửa thông tin')

@section('back-page')
    <a class="text-primary" onclick="window.history.back()" style="cursor: pointer">
        <i class="fas fa-chevron-left ms-3"></i>
        <span class="text-decoration-underline">Quay lại</span>
    </a>
@endsection

@section('content')
    <div class="card shadow-sm rounded p-4">
        <h4 class="mb-4 text-primary fw-bold">Sửa thông tin nhân viên</h4>
        <form method="POST" action="{{ route('staff.update', $staff->id) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Họ tên nhân viên</label>
                    <input type="text" name="name" class="form-control" value="{{ $staff->name }}">
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" value="{{ $staff->phone }}">
                    @error('phone')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $staff->email }}">
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" name="address" class="form-control" value="{{ $staff->address }}">
                    @error('address')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label d-block">Giới tính</label>
                <div class="form-check form-check-inline">
                    <input type="radio" name="sex" value="1" class="form-check-input" id="male" @checked($staff->sex == 1)>
                    <label for="male" class="form-check-label">Nam</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="radio" name="sex" value="0" class="form-check-input" id="female" @checked($staff->sex == 0)>
                    <label for="female" class="form-check-label">Nữ</label>
                </div>
                @error('sex')
                    <div><small class="text-danger">{{ $message }}</small></div>
                @enderror
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Chức vụ</label>
                    <select class="form-select" name="position">
                        <option value="">-- Chọn chức vụ --</option>
                        <option value="Quản lý" @selected($staff->position === "Quản lý")>Quản lý</option>
                        <option value="Nhân viên bán hàng" @selected($staff->position === "Nhân viên bán hàng")>Nhân viên bán hàng</option>
                        <option value="Nhân viên kho" @selected($staff->position === "Nhân viên kho")>Nhân viên kho</option>
                    </select>
                    @error('position')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Trạng thái làm việc</label>
                    <select class="form-select" name="status" required>
                        <option value="Đang làm việc" @selected($staff->status == 'Đang làm việc')>Đang làm việc</option>
                        <option value="Tạm nghỉ" @selected($staff->status == 'Tạm nghỉ')>Tạm nghỉ</option>
                        <option value="Đã nghỉ việc" @selected($staff->status == 'Đã nghỉ việc')>Đã nghỉ việc</option>
                    </select>
                    @error('status')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary px-4">Lưu thông tin</button>
            </div>
        </form>
    </div>
@endsection
@else
    {{ abort(403, 'Bạn không có quyền truy cập trang này') }}
@endcan

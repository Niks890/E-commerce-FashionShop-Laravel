@can('managers')
@extends('admin.master')

@section('title', 'Thêm Nhân viên')

@section('back-page')
    <a class="text-primary" onclick="window.history.back()" style="cursor: pointer">
        <i class="fas fa-chevron-left ms-3"></i>
        <span class="text-decoration-underline">Quay lại</span>
    </a>
@endsection

@section('content')
    <div class="card shadow-sm rounded p-4">
        <form method="POST" action="{{ route('staff.store') }}">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Họ tên nhân viên</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Nhập họ tên">
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" placeholder="Nhập số điện thoại">
                    @error('phone')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Nhập email">
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" name="address" class="form-control" placeholder="Nhập địa chỉ">
                    @error('address')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label d-block">Giới tính</label>
                <div class="form-check form-check-inline">
                    <input type="radio" name="sex" value="1" class="form-check-input" id="male" checked>
                    <label for="male" class="form-check-label">Nam</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="radio" name="sex" value="0" class="form-check-input" id="female">
                    <label for="female" class="form-check-label">Nữ</label>
                </div>
                @error('sex')
                    <div><small class="text-danger">{{ $message }}</small></div>
                @enderror
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tên tài khoản</label>
                    <input type="text" name="username" class="form-control" placeholder="Nhập tên tài khoản">
                    @error('username')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu">
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Chức vụ</label>
                    <select class="form-select" name="position" required>
                        <option value="">-- Chọn chức vụ --</option>
                        <option value="Quản lý">Quản lý</option>
                        <option value="Nhân viên bán hàng">Nhân viên bán hàng</option>
                        <option value="Nhân viên kho">Nhân viên kho</option>
                        <option value="Nhân viên giao hàng">Nhân viên giao hàng</option>
                    </select>
                    @error('position')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Trạng thái làm việc</label>
                    <select class="form-select" name="status" required>
                        <option value="Đang làm việc">Đang làm việc</option>
                        <option value="Tạm nghỉ">Tạm nghỉ</option>
                        <option value="Đã nghỉ việc">Đã nghỉ việc</option>
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

@section('js')
<form id="staffForm" method="POST" action="{{ route('staff.store') }}">
    @csrf
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Họ tên nhân viên</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Nhập họ tên">
            <small class="text-danger error-message" id="error-name"></small>
        </div>
        <div class="col-md-6">
            <label class="form-label">Số điện thoại</label>
            <input type="text" name="phone" id="phone" class="form-control" placeholder="Nhập số điện thoại">
            <small class="text-danger error-message" id="error-phone"></small>
        </div>
    </div>
    <!-- Các trường khác tương tự, nhớ thêm id và thẻ error-message -->
    <div class="text-end">
        <button type="submit" class="btn btn-primary px-4">Lưu thông tin</button>
    </div>
</form>
@endsection
@else
    {{ abort(403, 'Bạn không có quyền truy cập trang này') }}
@endcan

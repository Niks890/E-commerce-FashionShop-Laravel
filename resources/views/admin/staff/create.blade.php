@can('managers')
@extends('admin.master')

@section('title', 'Thêm Nhân viên')

@section('back-page')
<div class="d-flex align-items-center">
    <button class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 shadow-sm back-btn"
        onclick="window.history.back()" style="transition: all 0.3s ease; border: 2px solid #007bff;">
        <i class="fas fa-arrow-left me-2"></i>
        <span class="fw-semibold">Quay lại</span>
    </button>
</div>
@endsection

@section('content')
<div class="card shadow rounded-4 p-4 border-0" style="background-color: #fdfdfd;">
    <form method="POST" action="{{ route('staff.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="form-label fw-semibold text-center">Ảnh đại diện</label>
            <div class="avatar-upload">
                <div class="avatar-edit mb-2">
                    <input type="file" name="avatar" id="avatar" accept="image/*" class="d-none">
                    <label for="avatar" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-camera me-2"></i>Chọn ảnh
                    </label>
                </div>
                <div class="avatar-preview" id="avatar-preview"
                    style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;200&quot; height=&quot;200&quot; viewBox=&quot;0 0 200 200&quot;><rect width=&quot;200&quot; height=&quot;200&quot; fill=&quot;%23f8f9fa&quot;/><text x=&quot;50%&quot; y=&quot;50%&quot; font-family=&quot;Arial&quot; font-size=&quot;14&quot; fill=&quot;%236c757d&quot; text-anchor=&quot;middle&quot; dominant-baseline=&quot;middle&quot;>Ảnh đại diện</text></svg>');">
                </div>
                @error('avatar')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="name" id="name" class="form-control" placeholder="Họ tên">
                    <label for="name">Họ tên nhân viên</label>
                </div>
                @error('name')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="phone" class="form-control" placeholder="Số điện thoại"  pattern="(0[3|5|7|8|9])+([0-9]{8})">
                    <label>Số điện thoại</label>
                </div>
                @error('phone')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="email" name="email" class="form-control" placeholder="Email">
                    <label>Email</label>
                </div>
                @error('email')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="address" class="form-control" placeholder="Địa chỉ">
                    <label>Địa chỉ</label>
                </div>
                @error('address')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label d-block">Giới tính</label>
            <div class="btn-group" role="group">
                <input type="radio" class="btn-check" name="sex" value="1" id="male" checked>
                <label class="btn btn-outline-primary" for="male">Nam</label>

                <input type="radio" class="btn-check" name="sex" value="0" id="female">
                <label class="btn btn-outline-primary" for="female">Nữ</label>
            </div>
            @error('sex')<div><small class="text-danger">{{ $message }}</small></div>@enderror
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="username" class="form-control" placeholder="Tên tài khoản">
                    <label>Tên tài khoản</label>
                </div>
                @error('username')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <input type="password" name="password" class="form-control" placeholder="Mật khẩu">
                    <label>Mật khẩu</label>
                </div>
                @error('password')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Chức vụ</label>
                <select class="form-select" name="position" required>
                    <option value="">-- Chọn chức vụ --</option>
                    <option value="Quản lý">Quản lý</option>
                    <option value="Nhân viên bán hàng">Nhân viên bán hàng</option>
                    <option value="Nhân viên kho">Nhân viên kho</option>
                    <option value="Nhân viên giao hàng">Nhân viên giao hàng</option>
                </select>
                @error('position')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Trạng thái làm việc</label>
                <select class="form-select" name="status" required>
                    <option value="Đang làm việc">Đang làm việc</option>
                    <option value="Tạm nghỉ">Tạm nghỉ</option>
                    <option value="Đã nghỉ việc">Đã nghỉ việc</option>
                </select>
                @error('status')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
        </div>

        <div class="text-end mt-4 align-content-center d-flex justify-content-center">
            <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill shadow-sm">
                <i class="fas fa-save me-2"></i> Lưu thông tin
            </button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const avatarInput = document.getElementById('avatar');
        const preview = document.getElementById('avatar-preview');

        avatarInput?.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.style.backgroundImage = `url('${e.target.result}')`;
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.backgroundImage =
                    `url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect width="200" height="200" fill="%23f8f9fa"/><text x="50%" y="50%" font-family="Arial" font-size="14" fill="%236c757d" text-anchor="middle" dominant-baseline="middle">Ảnh đại diện</text></svg>')`;
            }
        });
    });
</script>
@endsection

<style>
    .avatar-upload {
        position: relative;
        display: inline-block;
        max-width: 200px;
    }

    .avatar-preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 3px solid #dee2e6;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        transition: 0.3s;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .avatar-preview:hover {
        transform: scale(1.05);
        border-color: #007bff;
    }

    .avatar-upload label:hover {
        background-color: #007bff;
        color: white;
    }

    .form-floating label {
        padding: 0.5rem 0.75rem;
    }
</style>
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này') }}
@endcan

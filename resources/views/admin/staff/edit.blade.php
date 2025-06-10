@can('managers')
@extends('admin.master')

@section('title', 'Sửa thông tin nhân viên')

@section('back-page')
     <div class="d-flex align-items-center">
        <button class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 shadow-sm back-btn"
                onclick="window.history.back()"
                style="transition: all 0.3s ease; border: 2px solid #007bff;">
            <i class="fas fa-arrow-left me-2"></i>
            <span class="fw-semibold">Quay lại</span>
        </button>
    </div>

    <style>
        .back-btn:hover {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-color: #0056b3 !important;
            color: white !important;
            transform: translateX(-3px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3) !important;
        }

        .back-btn:active {
            transform: translateX(-1px);
        }

        .back-btn i {
            transition: transform 0.3s ease;
        }

        .back-btn:hover i {
            transform: translateX(-2px);
        }
    </style>
@endsection

@section('content')
    <div class="card shadow-sm rounded p-4">
        <h4 class="mb-4 text-primary fw-bold">Sửa thông tin nhân viên</h4>
        <form method="POST" action="{{ route('staff.update', $staff->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">


            <div class="mb-4">
            <label class="form-label fw-semibold text-center">Ảnh đại diện</label>
            <div class="avatar-upload">
                <div class="avatar-edit mb-2">
                    <input type="file" name="avatar" id="avatar" accept="image/*" class="d-none">
                    <input type="hidden" name="old_avatar" value="{{ $staff->avatar }}">
                    <label for="avatar" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-camera me-2"></i>Chọn ảnh
                    </label>
                </div>
                <div class="avatar-preview" id="avatar-preview"
                    style="background-image: url('{{ $staff->avatar ? asset($staff->avatar) : "data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;200&quot; height=&quot;200&quot; viewBox=&quot;0 0 200 200&quot;><rect width=&quot;200&quot; height=&quot;200&quot; fill=&quot;%23f8f9fa&quot;/><text x=&quot;50%&quot; y=&quot;50%&quot; font-family=&quot;Arial&quot; font-size=&quot;14&quot; fill=&quot;%236c757d&quot; text-anchor=&quot;middle&quot; dominant-baseline=&quot;middle&quot;>Ảnh đại diện</text></svg>" }}');">
                </div>
                @error('avatar')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>
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
                        <option value="Quản lý kho" @selected($staff->position === "Quản lý kho")>Quản lý kho</option>
                        <option value="Nhân viên kho" @selected($staff->position === "Nhân viên kho")>Nhân viên kho</option>
                        <option value="Nhân viên giao hàng" @selected($staff->position === "Nhân viên giao hàng")>Nhân viên giao hàng</option>
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

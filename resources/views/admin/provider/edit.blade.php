@can('warehouse workers')
@extends('admin.master')
@section('title', 'Sửa thông tin')

@section('back-page')
    <div class="d-flex align-items-center mb-3">
        <button class="btn btn-outline-primary rounded-pill px-3 py-2 shadow-sm"
            onclick="window.history.back()" style="transition: all 0.3s ease; border: 2px solid #007bff;">
            <i class="fas fa-arrow-left me-2"></i>
            <span class="fw-semibold">Quay lại</span>
        </button>
    </div>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white fw-bold">
            <i class="fas fa-edit me-2"></i> Cập nhật nhà cung cấp
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('provider.update', $provider->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên nhà cung cấp</label>
                    <input type="text" name="name" class="form-control" value="{{ $provider->name }}" placeholder="Nhập tên nhà cung cấp...">
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Địa chỉ</label>
                    <input type="text" name="address" class="form-control" value="{{ $provider->address }}" placeholder="Nhập địa chỉ...">
                    @error('address')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" value="{{ $provider->phone }}" placeholder="Nhập số điện thoại...">
                    @error('phone')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success shadow-sm px-4">
                        <i class="fas fa-save me-2"></i> Lưu thông tin
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@else
    {{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

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
                    <input type="text" name="phone" id="phoneInput" class="form-control" value="{{ $provider->phone }}" placeholder="Nhập số điện thoại...">
                    <div id="phoneError" class="text-danger small"></div>
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
@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('phoneInput');
            const phoneError = document.getElementById('phoneError');
            const phoneRegex = /^(0|\+84)[3|5|7|8|9][0-9]{8}$/; // Regex cho số điện thoại Việt Nam

            phoneInput.addEventListener('input', function() {
                const phoneNumber = phoneInput.value.trim();
                if (phoneNumber === '') {
                    phoneInput.classList.remove('is-invalid');
                    phoneError.textContent = '';
                } else if (!phoneRegex.test(phoneNumber)) {
                    phoneInput.classList.add('is-invalid');
                    phoneError.textContent = 'Số điện thoại không đúng định dạng Việt Nam.';
                } else {
                    phoneInput.classList.remove('is-invalid');
                    phoneError.textContent = '';
                }
            });

            // Optional: Prevent form submission if client-side validation fails
            const providerForm = document.getElementById('providerForm');
            providerForm.addEventListener('submit', function(event) {
                const phoneNumber = phoneInput.value.trim();
                if (phoneNumber === '' || !phoneRegex.test(phoneNumber)) {
                    event.preventDefault(); // Ngăn chặn gửi form
                    if (phoneNumber === '') {
                        phoneInput.classList.add('is-invalid');
                        phoneError.textContent = 'Số điện thoại là bắt buộc.';
                    }
                }
            });
        });
    </script>
@endsection
@else
    {{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

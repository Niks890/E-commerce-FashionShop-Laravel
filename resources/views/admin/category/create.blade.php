@can('salers')
    @extends('admin.master')
    @section('title', 'Thêm Danh mục')

@section('back-page')
    <div class="d-flex align-items-center">
        <button class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 shadow-sm back-btn"
            onclick="window.history.back()" style="transition: all 0.3s ease; border: 2px solid #007bff;">
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
    <div class="container mt-4" style="max-width: 600px">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Thêm danh mục mới</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('category.store') }}">
                    @csrf

                    {{-- Tên danh mục --}}
                    <div class="form-group mb-3">
                        <label for="name">Tên danh mục:</label>
                        <input type="text" name="name" id="name" class="form-control"
                            placeholder="Nhập tên danh mục" value="{{ old('name') }}">
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Danh mục cha --}}
                    {{-- <div class="form-group mb-3">
                        <label for="parent_id">Thuộc danh mục cha (nếu có):</label>
                        <select name="parent_id" id="parent_id" class="form-control">
                            <option value="">-- Không có (là danh mục cha) --</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('parent_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                        </select>
                        @error('parent_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div> --}}

                    {{-- Trạng thái --}}
                    <div class="form-group mb-4">
                        <label>Trạng thái:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="status1" value="1"
                                {{ old('status', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status1">Hiển thị</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="status0" value="0"
                                {{ old('status') == '0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status0">Ẩn</label>
                        </div>
                        @error('status')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Nút submit --}}
                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save me-1"></i> Lưu thông tin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #3f80ea 0%, #1a56db 100%) !important;
            border-radius: 0 !important;
        }

        .card {
            border-radius: 12px;
            overflow: hidden;
            border: none;
        }

        .form-control:focus {
            border-color: #3f80ea;
            box-shadow: 0 0 0 0.25rem rgba(63, 128, 234, 0.25);
        }

        .btn-primary-gradient {
            background: linear-gradient(135deg, #3f80ea 0%, #1a56db 100%);
            border: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(63, 128, 234, 0.4);
        }

        .btn-primary-gradient:active {
            transform: translateY(0);
        }

        .btn-primary-gradient::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
            transition: all 0.5s ease;
            opacity: 0;
        }

        .btn-primary-gradient:hover::after {
            opacity: 1;
            left: 100%;
        }

        .form-floating label {
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
@endsection
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

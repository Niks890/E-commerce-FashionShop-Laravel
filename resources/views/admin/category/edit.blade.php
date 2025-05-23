@can('salers')
@extends('admin.master')
@section('title', 'Sửa danh mục')

@section('back-page')
<div class="d-flex align-items-center mb-3">
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
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">Chỉnh sửa danh mục</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('category.update', $data->id) }}">
                @csrf
                @method('PUT')

                {{-- Tên loại --}}
                <div class="form-group mb-3">
                    <label for="name">Tên danh mục:</label>
                    <input type="text" name="name" id="name" class="form-control"
                           value="{{ old('name', $data->category_name) }}" placeholder="Nhập tên danh mục">
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Danh mục cha --}}
                {{-- <div class="form-group mb-3">
                    <label for="parent_id">Danh mục cha:</label>
                    <select name="parent_id" id="parent_id" class="form-control">
                        <option value="">-- Không có (danh mục gốc) --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $data->parent_id == $category->id ? 'selected' : '' }}>
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
                            {{ $data->status == 1 ? 'checked' : '' }}>
                        <label class="form-check-label" for="status1">Hiển thị</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="status0" value="0"
                            {{ $data->status == 0 ? 'checked' : '' }}>
                        <label class="form-check-label" for="status0">Ẩn</label>
                    </div>
                    @error('status')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@else
    {{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

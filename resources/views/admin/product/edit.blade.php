@can('salers')
@extends('admin.master')
@section('title', 'Sửa Thông Tin Sản phẩm')

@section('back-page')
<a class="text-primary d-flex align-items-center gap-2" style="cursor:pointer" onclick="window.history.back()">
    <i class="fas fa-chevron-left fs-5"></i>
    <span class="text-decoration-underline fw-semibold">Quay lại</span>
</a>
@endsection

@section('content')
<form method="POST" action="{{ route('product.update', $product->id) }}" enctype="multipart/form-data" class="p-4 bg-white rounded shadow-sm">
    @csrf @method('PUT')

    <div class="row g-3 mb-4">
        <div class="col-md-5">
            <label class="form-label fw-semibold">Tên sản phẩm:</label>
            <input type="text" name="name" class="form-control form-control-lg" value="{{ $product->product_name }}">
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Giá niêm yết:</label>
            <input type="number" name="price" class="form-control form-control-lg" value="{{ $product->price }}">
            @error('price')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="button" class="btn btn-success w-100 btn-update-size shadow-sm">
                <i class="fas fa-edit me-2"></i> Cập nhật kích cỡ
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-5">
            <label class="form-label fw-semibold">Thương hiệu:</label>
            <input type="text" name="brand" class="form-control form-control-lg" value="{{ $product->brand }}" disabled>
        </div>
        <div class="col-md-7">
            <label class="form-label fw-semibold">Chương trình khuyến mãi:</label>
            <select class="form-select form-select-lg" name="discount_id">
                <option value="">-- Chọn chương trình khuyến mãi --</option>
                @foreach ($discounts as $discount)
                    <option value="{{ $discount->id }}" @selected($discount->id == $product->discount_id)>{{ $discount->name }}</option>
                @endforeach
            </select>
            @error('discount_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <div class="row g-3 mb-4 align-items-center">
        <div class="col-md-5">
            <label class="form-label fw-semibold">Danh mục:</label>
            <select class="form-select form-select-lg" name="category_id" disabled>
                @foreach ($cats as $cat)
                    <option value="{{ $cat->id }}" @selected($cat->id == $product->category_id)>{{ $cat->category_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-7">
            <label class="form-label fw-semibold">Hình ảnh sản phẩm:</label>
            <input type="file" name="image" accept="image/*" class="form-control form-control-lg fileInput">
            <input type="hidden" name="image_path" value="{{ $product->image }}">
            <div class="mt-3 d-flex justify-content-center">
                <img src="uploads/{{ $product->image }}" alt="Preview Image" class="previewImg rounded shadow-sm" style="max-height: 150px; object-fit: contain;">
            </div>
            @error('image')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label fw-semibold">Tag:</label>
        <input type="text" data-role="tagsinput" name="product_tags" class="form-control form-control-lg" value="{{ $product->tags }}">
        @error('product_tags')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Chất liệu:</label>
            <input type="text" name="material" class="form-control form-control-lg" value="{{ $product->material }}">
            @error('material')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Màu sắc:</label>
            <input type="text" name="color" class="form-control form-control-lg" value="{{ $productVariants[0]->color }}" disabled>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-7">
            <label class="form-label fw-semibold">Mô tả chi tiết:</label>
            <textarea name="description" class="form-control form-control-lg" rows="5" placeholder="Mô tả sản phẩm...">{{ $product->description }}</textarea>
            @error('description')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="col-md-5">
            <label class="form-label fw-semibold">Mô tả ngắn:</label>
            <textarea name="short_description" class="form-control form-control-lg" rows="5" placeholder="Mô tả ngắn...">{{ $product->short_description }}</textarea>
            @error('short_description')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <div class="mb-4 d-flex gap-4">
        <div class="form-check form-check-inline">
            <input type="radio" name="status" value="1" class="form-check-input" id="statusShow" @checked($product->status == 1)>
            <label for="statusShow" class="form-check-label fw-semibold">Hiển thị</label>
        </div>
        <div class="form-check form-check-inline">
            <input type="radio" name="status" value="0" class="form-check-input" id="statusHide" @checked($product->status == 0)>
            <label for="statusHide" class="form-check-label fw-semibold">Ẩn</label>
        </div>
        @error('status')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Modal cập nhật size -->
    <div id="modal-update-size" class="modal fade" tabindex="-1" aria-labelledby="modalUpdateSizeLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="modalUpdateSizeLabel">Cập nhật thông tin cho từng kích cỡ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <form method="POST" action="{{ route('product.update', $product->id) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        @foreach ($productVariants as $productVariant)
                            <div class="mb-4 p-3 rounded border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-semibold">{{ $productVariant->size }} - {{ $productVariant->color }}</h6>
                                </div>
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-6">
                                        <label class="form-label">Hình ảnh:</label>
                                        <input type="file" name="image_variant[{{ $productVariant->id }}]" accept="image/*" class="form-control form-control-sm fileInput">
                                        <input type="hidden" name="image_path_variant[{{ $productVariant->id }}]" value="{{ $productVariant->image }}">
                                        <img src="{{ asset('uploads/' . $productVariant->image) }}" alt="" class="mt-2 rounded shadow-sm" style="height: 50px; object-fit: contain;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Giá:</label>
                                        <input type="number" name="price_variant[{{ $productVariant->id }}]" class="form-control form-control-sm" value="{{ $productVariant->price }}" placeholder="Nhập giá cho kích cỡ này...">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary btn-lg w-100">Lưu thông tin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-lg w-100 mt-4 shadow-sm">Lưu thông tin</button>
</form>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-tagsinput.css') }}" />
<style>
    .bootstrap-tagsinput {
        width: 100%;
        padding: 0.625rem 1rem;
        font-size: 1.125rem;
        border-radius: 0.5rem;
        border: 1px solid #ced4da;
        min-height: 48px;
    }
    .bootstrap-tagsinput .tag {
        background-color: #0d6efd;
        color: white;
        padding: 0.25em 0.75em;
        border-radius: 0.4rem;
        margin-right: 0.25em;
    }
    /* Ẩn phần preview ảnh mặc định nếu ảnh chưa load */
    .previewImg {
        display: block;
    }
</style>
@endsection

@section('js')
<script src="{{ asset('assets/js/plugin/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $(".fileInput").on("change", function(e) {
            const file = e.target.files[0];
            const validTypes = ["image/jpg", "image/jpeg", "image/png", "image/gif", "image/webp", "image/avif"];

            if (file && validTypes.includes(file.type)) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    $(e.target).closest('div').find(".previewImg").attr("src", event.target.result).show();
                };
                reader.readAsDataURL(file);
            } else {
                $(e.target).val("");
                $(e.target).closest('div').find(".previewImg").hide();
                alert('Vui lòng chọn file ảnh hợp lệ (jpg, jpeg, png, gif, webp, avif)');
            }
        });

        // Modal bootstrap 5 mở bằng JS
        const modalUpdateSize = new bootstrap.Modal(document.getElementById('modal-update-size'));
        $('.btn-update-size').on('click', function() {
            modalUpdateSize.show();
        });
    });
</script>
@endsection

@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

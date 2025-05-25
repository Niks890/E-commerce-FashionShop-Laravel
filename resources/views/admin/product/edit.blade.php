{{-- @php
    dd($productVariants);
@endphp --}}
@can('salers')
    @extends('admin.master')
    @section('title', 'Sửa Thông Tin Sản phẩm')

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
    <form method="POST" action="{{ route('product.update', $product->id) }}" enctype="multipart/form-data"
        class="p-4 bg-white rounded shadow-sm">
        @csrf @method('PUT')

        <div class="row g-3 mb-4">
            <div class="col-md-5">
                <label class="form-label fw-semibold">Tên sản phẩm:</label>
                <input type="text" name="name" class="form-control form-control-lg"
                    value="{{ $product->product_name }}">
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Giá niêm yết(vnđ):</label>
                <input type="number" name="price" class="form-control form-control-lg" value="{{ $product->price }}">
                @error('price')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-success w-100 btn-update-size shadow-sm">
                    <i class="fas fa-edit me-2"></i> Quản lý biến thể sản phẩm
                </button>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-5">
                <label class="form-label fw-semibold">Thương hiệu:</label>
                <input type="text" name="brand" class="form-control form-control-lg" value="{{ $product->brand }}"
                    disabled>
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
                <select class="form-select form-select-lg" name="category_id">
                    @foreach ($cats as $cat)
                        <option value="{{ $cat->id }}" @selected($cat->id == $product->category_id)>{{ $cat->category_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-7">
                <label class="form-label fw-semibold">Hình ảnh gốc sản phẩm:</label>
                <input type="file" name="image" accept="image/*" class="form-control form-control-lg fileInput">
                <input type="hidden" name="image_path" value="{{ $product->image }}">
                <div class="mt-3 d-flex justify-content-center">
                    <img src="{{ $product->image }}" alt="Preview Image" class="previewImg rounded shadow-sm"
                        style="max-height: 150px; object-fit: contain;">
                </div>
                @error('image')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Tag:</label>
            <input type="text" data-role="tagsinput" name="product_tags" class="form-control form-control-lg"
                value="{{ $product->tags }}">
            @error('product_tags')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Chất liệu sản phẩm:</label>
                <input type="text" name="material" class="form-control form-control-lg"
                    value="{{ $product->material }}">
                @error('material')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Những màu sắc hiện có của sản phẩm:</label>
                <input type="text" name="color" class="form-control form-control-lg"
                    value="{{ $productVariants->pluck('color')->unique()->implode(', ') }}" disabled>
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
                <input type="radio" name="status" value="1" class="form-check-input" id="statusShow"
                    @checked($product->status == 1)>
                <label for="statusShow" class="form-check-label fw-semibold">Hiển thị</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" name="status" value="0" class="form-check-input" id="statusHide"
                    @checked($product->status == 0)>
                <label for="statusHide" class="form-check-label fw-semibold">Ẩn</label>
            </div>
            @error('status')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-4 shadow-sm">Lưu thông tin</button>

        <!-- Modal cập nhật size -->
        <div id="modal-update-size" class="modal fade" tabindex="-1" aria-labelledby="modalUpdateSizeLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content rounded-4 shadow-lg">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold" id="modalUpdateSizeLabel">Cập nhật thông tin cho từng biến thể
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    @csrf
                    {{-- <div class="modal-body">
                        @foreach ($productVariants as $productVariant)
                            <div class="mb-4 p-3 rounded border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-semibold">{{ $productVariant->size }} -
                                        {{ $productVariant->color }}</h6>
                                </div>
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-6">
                                        <label class="form-label">Hình ảnh biến thể:</label>
                                        <input type="file" name="image_variant[{{ $productVariant->id }}]"
                                            accept="image/*" class="form-control form-control-sm fileInput">
                                        <input type="hidden" name="image_path_variant[{{ $productVariant->id }}]"
                                            value="{{ $productVariant->image }}">
                                        <img src="{{ $productVariant->image }}" alt=""
                                            class="mt-2 rounded shadow-sm" style="height: 50px; object-fit: contain;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Hình ảnh biến thể:</label>
                                        <input type="file" name="image_variant[{{ $productVariant->id }}][]"
                                            accept="image/*" class="form-control form-control-sm fileInput" multiple>
                                        <div class="mt-2">
                                            @foreach ($productVariant->ImageVariants as $image)
                                                <img src="{{ $image->image_path }}" alt=""
                                                    class="me-2 rounded shadow-sm"
                                                    style="height: 50px; object-fit: contain;">
                                                <input type="hidden" name="existing_images[{{ $productVariant->id }}][]"
                                                    value="{{ $image->id }}">
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Giá:</label>
                                        <input type="number" name="price_variant[{{ $productVariant->id }}]"
                                            class="form-control form-control-sm"
                                            value="{{ number_format($productVariant->price, 0, ',', '.') }}"
                                            placeholder="Nhập giá cho kích cỡ này...">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div> --}}
                    <div class="modal-body">
                        @foreach ($productVariants as $productVariant)
                            <div class="mb-4 p-3 rounded border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-semibold">{{ $productVariant->size }} -
                                        {{ $productVariant->color }}</h6>
                                </div>
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-6">
                                        <label class="form-label">Hình ảnh biến thể:</label>
                                        {{-- Input cho phép chọn nhiều file --}}
                                        <input type="file" name="image_variant[{{ $productVariant->id }}][]"
                                            accept="image/*" class="form-control form-control-sm fileInput mb-2" multiple>

                                        {{-- Hiển thị ảnh cũ --}}
                                        <div class="mt-2 d-flex flex-wrap border p-2 rounded" style="min-height: 60px;">
                                            @if ($productVariant->ImageVariants->isNotEmpty())
                                                @foreach ($productVariant->ImageVariants as $image)
                                                    <div class="position-relative me-2 mb-2">
                                                        <img src="{{ $image->url }}" alt="Variant Image"
                                                            class="rounded shadow-sm"
                                                            style="height: 50px; width: 50px; object-fit: cover;">
                                                        {{-- TODO: Thêm nút xóa nếu cần (yêu cầu JS và xử lý backend) --}}
                                                        {{-- <button type="button" class="btn btn-danger btn-sm position-absolute top-0 start-100 translate-middle-y rounded-circle p-1 remove-image" data-id="{{ $image->id }}">&times;</button> --}}
                                                    </div>
                                                @endforeach
                                            @else
                                                <small class="text-muted">Chưa có ảnh cho biến thể này.</small>
                                            @endif
                                        </div>
                                        {{-- Container để JS hiển thị preview ảnh mới --}}
                                        <div class="mt-2 preview-container-{{ $productVariant->id }} d-flex flex-wrap">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Giá (vnđ):</label>
                                        <input type="text" name="price_variant[{{ $productVariant->id }}]"
                                            class="form-control form-control-sm"
                                            value="{{ number_format($productVariant->price, 0, ',', '.') }}"
                                            placeholder="Nhập giá...">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer border-0">
                        <input type="submit" class="btn btn-primary btn-lg w-100" value="Lưu thông tin">
                    </div>
                </div>
            </div>
        </div>
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

        .preview-container .img-thumbnail {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .preview-container .img-thumbnail:hover {
            transform: scale(1.05);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .remove-image {
            padding: 0.1rem 0.25rem;
            font-size: 0.7rem;
        }
    </style>
@endsection

@section('js')
    {{-- <script src="{{ asset('assets/js/plugin/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".fileInput").on("change", function(e) {
                const file = e.target.files[0];
                const validTypes = ["image/jpg", "image/jpeg", "image/png", "image/gif", "image/webp",
                    "image/avif"
                ];

                if (file && validTypes.includes(file.type)) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        $(e.target).closest('div').find(".previewImg").attr("src", event.target.result)
                            .show();
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
    </script> --}}

@section('js')
    <script src="{{ asset('assets/js/plugin/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Hàm kiểm tra file hợp lệ
            function isValidImage(file) {
                const validTypes = ["image/jpg", "image/jpeg", "image/png", "image/gif", "image/webp",
                    "image/avif"
                ];
                return file && validTypes.includes(file.type);
            }

            // Xử lý preview cho ảnh chính (đã có)
            $(".fileInput:not([multiple])").on("change", function(e) {
                const file = e.target.files[0];
                const previewImg = $(e.target).closest('div').find(".previewImg");

                if (isValidImage(file)) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        previewImg.attr("src", event.target.result).show();
                    };
                    reader.readAsDataURL(file);
                } else {
                    $(e.target).val("");
                    previewImg.hide();
                    if (file) alert('Vui lòng chọn file ảnh hợp lệ (jpg, jpeg, png, gif, webp, avif)');
                }
            });

            // Xử lý preview cho ảnh biến thể (multiple)
            $(".fileInput[multiple]").on("change", function(e) {
                const files = e.target.files;
                const variantId = $(this).attr('name').match(/\[(\d+)\]/)[
                    1]; // Lấy ID biến thể từ tên input
                const previewContainer = $(`.preview-container-${variantId}`);
                previewContainer.empty(); // Xóa preview cũ

                if (files.length > 0) {
                    $.each(files, function(index, file) {
                        if (isValidImage(file)) {
                            const reader = new FileReader();
                            reader.onload = (event) => {
                                const imgElement = $('<img>')
                                    .attr('src', event.target.result)
                                    .addClass('me-2 mb-2 rounded shadow-sm')
                                    .css({
                                        'height': '50px',
                                        'width': '50px',
                                        'object-fit': 'cover'
                                    });
                                previewContainer.append(imgElement);
                            };
                            reader.readAsDataURL(file);
                        } else {
                            alert('Tệp ' + file.name +
                                ' không hợp lệ. Vui lòng chọn file ảnh hợp lệ.');
                            $(e.target).val(""); // Xóa lựa chọn file nếu có file không hợp lệ
                            previewContainer.empty();
                            return false; // Dừng vòng lặp
                        }
                    });
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

{{-- @php
    dd($lastestInventoryPrice);
@endphp --}}
@can('salers')
    @extends('admin.master')
    @section('title', 'Sửa Thông Tin Sản phẩm')

@section('back-page')
    <div class="d-flex align-items-center">
        <button class="btn btn-outline-primary btn-sm rounded-pill px-4 py-2 shadow-sm back-btn"
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
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4) !important;
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
    <div class="container-fluid px-0">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                    <div class="card-body py-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-white bg-opacity-20 me-3">
                                <i class="fas fa-edit fs-4"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 fw-bold">Chỉnh sửa thông tin sản phẩm</h4>
                                <p class="mb-0 opacity-90">Cập nhật thông tin chi tiết cho sản phẩm của bạn</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('product.update', $product->id) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="row g-4">
                <!-- Left Column - Main Information -->
                <div class="col-lg-8">
                    <!-- Basic Information Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title mb-0 fw-bold text-primary">
                                <i class="fas fa-info-circle me-2"></i>Thông tin cơ bản
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="fas fa-tag me-2 text-primary"></i>Tên sản phẩm
                                    </label>
                                    <input type="text" name="name" class="form-control form-control-lg border-2"
                                        value="{{ $product->product_name }}" placeholder="Nhập tên sản phẩm">
                                    @error('name')
                                        <div class="text-danger mt-1"><i
                                                class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="fas fa-building me-2 text-primary"></i>Thương hiệu
                                    </label>
                                    <input type="text" name="brand"
                                        class="form-control form-control-lg bg-light border-2" value="{{ $product->brand }}"
                                        disabled>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="fas fa-layer-group me-2 text-primary"></i>Danh mục
                                    </label>
                                    <select class="form-select form-select-lg border-2" name="category_id">
                                        @foreach ($cats as $cat)
                                            <option value="{{ $cat->id }}" @selected($cat->id == $product->category_id)>
                                                {{ $cat->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Information Card -->
                    {{-- <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title mb-0 fw-bold text-success">
                                <i class="fas fa-money-bill-wave me-2"></i>Thông tin giá cả
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="price-info-card bg-light p-3 rounded-3 mb-3">
                                        <label class="form-label fw-semibold text-muted mb-1">Giá nhập gần nhất</label>
                                        <div class="fs-5 fw-bold text-info">
                                            <i class="fas fa-arrow-down me-2"></i>
                                            {{ number_format($lastestInventoryPrice->price, 0, ',', '.') }} VNĐ
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="fas fa-price-tag me-2 text-success"></i>Giá niêm yết (VNĐ)
                                    </label>
                                    <input type="text" name="price" class="form-control form-control-lg border-2"
                                        value="{{ $product->price }}" placeholder="0"
                                        onkeypress="return event.key !== '-' && event.key !== 'e'"
                                        onpaste="handlePricePaste(event)" oninput="formatPriceInput(this)">
                                    @error('price')
                                        <div class="text-danger mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title mb-0 fw-bold text-success">
                                <i class="fas fa-money-bill-wave me-2"></i>Thông tin giá cả
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="price-info-card bg-light p-3 rounded-3 mb-3">
                                        <label class="form-label fw-semibold text-muted mb-1">Giá nhập gần nhất</label>
                                        <div class="fs-5 fw-bold text-info">
                                            <i class="fas fa-arrow-down me-2"></i>
                                            {{ number_format($lastestInventoryPrice->price, 0, ',', '.') }} VNĐ
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="fas fa-price-tag me-2 text-success"></i>Giá niêm yết (VNĐ)
                                    </label>
                                    <input type="text" name="price" class="form-control form-control-lg border-2"
                                        value="{{ $product->price }}" placeholder="0"
                                        onkeypress="return event.key !== '-' && event.key !== 'e'"
                                        onpaste="handlePricePaste(event)" oninput="formatPriceInput(this)">
                                    @error('price')
                                        <div class="text-danger mt-1"><i
                                                class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Hiển thị lịch sử giá sản phẩm chính -->
                                <div class="col-12">
                                    <div class="price-history-card mt-3">
                                        <h6 class="fw-semibold text-muted mb-2">
                                            <i class="fas fa-history me-2"></i>Lịch sử thay đổi giá
                                        </h6>
                                        <div class="history-list">
                                            @forelse($priceHistory as $history)
                                                <div class="history-item d-flex justify-content-between py-2 border-bottom">
                                                    <div>
                                                        <span class="text-muted">{{ $history['changed_at'] }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-decoration-line-through text-danger me-2">
                                                            {{ number_format($history['old_price'], 0, ',', '.') }} VNĐ
                                                        </span>
                                                        <i class="fas fa-arrow-right text-muted me-2"></i>
                                                        <span class="text-success fw-bold">
                                                            {{ number_format($history['new_price'], 0, ',', '.') }} VNĐ
                                                        </span>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-muted py-2">Chưa có lịch sử thay đổi giá</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Details Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title mb-0 fw-bold text-info">
                                <i class="fas fa-clipboard-list me-2"></i>Chi tiết sản phẩm
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="fas fa-cube me-2 text-info"></i>Chất liệu
                                    </label>
                                    <input type="text" name="material" class="form-control form-control-lg border-2"
                                        value="{{ $product->material }}" placeholder="Nhập chất liệu">
                                    @error('material')
                                        <div class="text-danger mt-1"><i
                                                class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="fas fa-palette me-2 text-info"></i>Màu sắc hiện có
                                    </label>
                                    <input type="text" name="color"
                                        class="form-control form-control-lg bg-light border-2"
                                        value="{{ $productVariants->pluck('color')->unique()->implode(', ') }}" disabled>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">
                                    <i class="fas fa-tags me-2 text-info"></i>Tags sản phẩm
                                </label>
                                <input type="text" data-role="tagsinput" name="product_tags" class="form-control"
                                    value="{{ $product->tags }}">
                                @error('product_tags')
                                    <div class="text-danger mt-1"><i
                                            class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3">
                                <div class="col-md-7">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="fas fa-align-left me-2 text-info"></i>Mô tả chi tiết
                                    </label>
                                    <textarea name="description" class="form-control border-2" rows="5"
                                        placeholder="Nhập mô tả chi tiết sản phẩm...">{{ $product->description }}</textarea>
                                    @error('description')
                                        <div class="text-danger mt-1"><i
                                                class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="fas fa-comment me-2 text-info"></i>Mô tả ngắn
                                    </label>
                                    <textarea name="short_description" class="form-control border-2" rows="5" placeholder="Nhập mô tả ngắn...">{{ $product->short_description }}</textarea>
                                    @error('short_description')
                                        <div class="text-danger mt-1"><i
                                                class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Image & Settings -->
                <div class="col-lg-4">
                    <!-- Product Image Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title mb-0 fw-bold text-warning">
                                <i class="fas fa-image me-2"></i>Hình ảnh sản phẩm
                            </h5>
                        </div>
                        <div class="card-body p-4 text-center">
                            <div class="image-upload-container">
                                <div class="current-image mb-3">
                                    <img src="{{ $product->image }}" alt="Product Image"
                                        class="previewImg img-fluid rounded-3 shadow-sm border"
                                        style="max-height: 200px; object-fit: cover;">
                                </div>
                                <div class="upload-controls">
                                    <label class="btn btn-outline-primary btn-lg w-100 mb-2">
                                        <i class="fas fa-cloud-upload-alt me-2"></i>Chọn ảnh mới
                                        <input type="file" name="image" accept="image/*" class="d-none fileInput">
                                    </label>
                                    <input type="hidden" name="image_path" value="{{ $product->image }}">
                                    <small class="text-muted">JPG, PNG, GIF tối đa 5MB</small>
                                </div>
                            </div>
                            @error('image')
                                <div class="text-danger mt-2"><i
                                        class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Discount Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title mb-0 fw-bold text-danger">
                                <i class="fas fa-percent me-2"></i>Khuyến mãi
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <label class="form-label fw-semibold text-dark">Chương trình khuyến mãi</label>
                            <select class="form-select form-select-lg border-2" name="discount_id">
                                <option value="">-- Không áp dụng --</option>
                                @foreach ($discounts as $discount)
                                    <option value="{{ $discount->id }}" @selected($discount->id == $product->discount_id)>
                                        {{ $discount->name }} - Giảm {{ $discount->percent_discount * 100 }}%
                                    </option>
                                @endforeach
                            </select>
                            @error('discount_id')
                                <div class="text-danger mt-1"><i
                                        class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Status Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title mb-0 fw-bold text-secondary">
                                <i class="fas fa-toggle-on me-2"></i>Trạng thái
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="status-options">
                                <div class="form-check mb-3 p-3 bg-light rounded-3">
                                    <input type="radio" name="status" value="1" class="form-check-input"
                                        id="statusShow" @checked($product->status == 1)>
                                    <label for="statusShow" class="form-check-label fw-semibold text-success">
                                        <i class="fas fa-eye me-2"></i>Hiển thị trên kệ hàng
                                    </label>
                                </div>
                                <div class="form-check p-3 bg-light rounded-3">
                                    <input type="radio" name="status" value="0" class="form-check-input"
                                        id="statusHide" @checked($product->status == 0)>
                                    <label for="statusHide" class="form-check-label fw-semibold text-danger">
                                        <i class="fas fa-eye-slash me-2"></i>Ẩn khỏi kệ hàng
                                    </label>
                                </div>
                            </div>
                            @error('status')
                                <div class="text-danger mt-2"><i
                                        class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Variant Management Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4 text-center">
                            <button type="button" class="btn btn-success btn-lg w-100 btn-update-size shadow-sm">
                                <i class="fas fa-cogs me-2"></i>Quản lý biến thể sản phẩm
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex gap-3 justify-content-end">
                                <button type="button" class="btn btn-outline-secondary btn-lg px-4"
                                    onclick="window.history.back()">
                                    <i class="fas fa-times me-2"></i>Hủy bỏ
                                </button>
                                <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                                    <i class="fas fa-save me-2"></i>Lưu thông tin
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal cập nhật biến thể -->
            <div id="modal-update-size" class="modal fade" tabindex="-1" aria-labelledby="modalUpdateSizeLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-gradient-primary text-white border-0">
                            <h5 class="modal-title fw-bold" id="modalUpdateSizeLabel">
                                <i class="fas fa-cogs me-2"></i>Quản lý biến thể sản phẩm
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        @csrf
                        <div class="modal-body p-4">
                            <div class="row g-4">
                                @foreach ($productVariants as $productVariant)
                                    <div class="col-md-6">
                                        <div class="variant-card border-0 shadow-sm rounded-4 p-4">
                                            <div class="variant-header mb-3">
                                                <h6 class="fw-bold text-primary mb-1">
                                                    <i class="fas fa-tshirt me-2"></i>{{ $productVariant->size }} -
                                                    {{ $productVariant->color }}
                                                </h6>
                                                <div class="variant-price-input">
                                                    <label class="form-label fw-semibold text-dark">Giá biến thể
                                                        (VNĐ):</label>
                                                    <input type="text" name="price_variant[{{ $productVariant->id }}]"
                                                        class="form-control form-control-lg border-2"
                                                        value="{{ $productVariant->price }}"
                                                        onkeypress="return event.key !== '-' && event.key !== 'e'"
                                                        onpaste="handlePricePaste(event)"
                                                        oninput="formatPriceInput(this)">
                                                </div>

                                                <!-- Hiển thị lịch sử giá biến thể -->
                                                <div class="variant-price-history mt-3">
                                                    <h6 class="fw-semibold text-muted mb-2 small">
                                                        <i class="fas fa-history me-1"></i>Lịch sử giá
                                                    </h6>
                                                    <div class="history-list small">
                                                        @forelse($variantPriceHistories[$productVariant->id] as $history)
                                                            <div
                                                                class="history-item d-flex justify-content-between py-1 border-bottom">
                                                                <div>
                                                                    <span
                                                                        class="text-muted">{{ $history['changed_at'] }}</span>
                                                                </div>
                                                                <div>
                                                                    <span
                                                                        class="text-decoration-line-through text-danger me-1">
                                                                        {{ number_format($history['old_price'], 0, ',', '.') }}
                                                                    </span>
                                                                    <i class="fas fa-arrow-right text-muted me-1"></i>
                                                                    <span class="text-success fw-bold">
                                                                        {{ number_format($history['new_price'], 0, ',', '.') }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="text-muted py-1">Chưa có lịch sử thay đổi giá</div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="variant-images-section">
                                                <label class="form-label fw-semibold text-dark mb-3">
                                                    <i class="fas fa-images me-2"></i>Ảnh biến thể
                                                    ({{ $productVariant->ImageVariants->count() }}/5)
                                                </label>

                                                <div class="images-grid mb-3"
                                                    id="variant-images-{{ $productVariant->id }}">
                                                    @foreach ($productVariant->ImageVariants as $index => $image)
                                                        <div class="image-item" data-index="{{ $index }}">
                                                            <img src="{{ $image->url }}" class="variant-image">
                                                            <button type="button"
                                                                class="remove-btn remove-existing-image">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                            <input type="hidden"
                                                                name="existing_images[{{ $productVariant->id }}][]"
                                                                value="{{ $image->id }}">
                                                        </div>
                                                    @endforeach
                                                </div>

                                                @if ($productVariant->ImageVariants->count() < 5)
                                                    <div class="upload-zone">
                                                        <label class="upload-area">
                                                            <div class="upload-content">
                                                                <i
                                                                    class="fas fa-cloud-upload-alt fs-2 text-primary mb-2"></i>
                                                                <div class="fw-semibold">Kéo thả ảnh vào đây</div>
                                                                <div class="text-muted small">hoặc click để chọn</div>
                                                                <div class="text-primary small mt-1">
                                                                    Tối đa
                                                                    {{ 5 - $productVariant->ImageVariants->count() }} ảnh
                                                                </div>
                                                            </div>
                                                            <input type="file"
                                                                name="image_variant[{{ $productVariant->id }}][]"
                                                                accept="image/*" class="d-none variant-file-input"
                                                                data-variant-id="{{ $productVariant->id }}" multiple
                                                                {{ $productVariant->ImageVariants->count() >= 5 ? 'disabled' : '' }}>
                                                        </label>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer border-0 bg-light">
                            <button type="button" class="btn btn-outline-secondary btn-lg px-4" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Đóng
                            </button>
                            <input type="submit" class="btn btn-primary btn-lg px-5" value="💾 Lưu thông tin">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-tagsinput.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/product-edit.css') }}">
@endsection

@section('js')
    <script src="{{ asset('assets/js/plugin/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Hàm kiểm tra file hợp lệ
            function isValidImage(file) {
                const validTypes = ["image/jpg", "image/jpeg", "image/png", "image/gif", "image/webp",
                    "image/avif"
                ];
                const maxSize = 5 * 1024 * 1024; // 5MB

                if (!file || !validTypes.includes(file.type)) {
                    return {
                        valid: false,
                        message: 'Vui lòng chọn file ảnh hợp lệ (JPG, PNG, GIF, WebP, AVIF)'
                    };
                }

                if (file.size > maxSize) {
                    return {
                        valid: false,
                        message: 'Kích thước file không được vượt quá 5MB'
                    };
                }

                return {
                    valid: true
                };
            }

            // Xử lý preview cho ảnh chính với animation
            $(".fileInput:not([multiple])").on("change", function(e) {
                const file = e.target.files[0];
                const previewImg = $(e.target).closest('.card-body').find(".previewImg");

                const validation = isValidImage(file);
                if (!validation.valid) {
                    $(e.target).val("");
                    alert(validation.message);
                    return;
                }

                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        previewImg.fadeOut(200, function() {
                            $(this).attr("src", event.target.result).fadeIn(200);
                        });
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Xử lý xóa ảnh hiện có với animation
            $(document).on('click', '.remove-existing-image', function() {
                const imageItem = $(this).closest('.image-item');
                imageItem.fadeOut(300, function() {
                    $(this).remove();
                    updateUploadButtonStatus();
                });
            });

            // Xử lý upload ảnh mới với validation
            $(document).on('change', '.variant-file-input', function(e) {
                const variantId = $(this).data('variant-id');
                const files = Array.from(e.target.files);
                const container = $(`#variant-images-${variantId}`);
                const currentCount = container.children().length;

                // Validate số lượng file
                if (currentCount + files.length > 5) {
                    alert('Mỗi biến thể chỉ được tối đa 5 ảnh!');
                    $(this).val('');
                    return;
                }

                // Validate từng file
                for (let file of files) {
                    const validation = isValidImage(file);
                    if (!validation.valid) {
                        alert(validation.message);
                        $(this).val('');
                        return;
                    }
                }

                // Process files
                files.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const newIndex = currentCount + index;
                        const newImage = $(`
                            <div class="image-item" data-index="${newIndex}" style="opacity: 0;">
                                <img src="${e.target.result}" class="variant-image">
                                <button type="button" class="remove-btn remove-new-image">
                                    <i class="fas fa-times"></i>
                                </button>
                                <input type="hidden" name="new_images[${variantId}][]" value="${newIndex}">
                            </div>
                        `);
                        container.append(newImage);
                        newImage.animate({
                            opacity: 1
                        }, 300);
                    };
                    reader.readAsDataURL(file);
                });

                setTimeout(updateUploadButtonStatus, 500);
            });

            // Xử lý xóa ảnh mới
            $(document).on('click', '.remove-new-image', function() {
                const imageItem = $(this).closest('.image-item');
                imageItem.fadeOut(300, function() {
                    $(this).remove();
                    updateUploadButtonStatus();
                });
            });

            // Enhanced drag and drop với visual feedback
            $('.upload-area').on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('drag-over');
            });

            $('.upload-area').on('dragleave', function(e) {
                e.preventDefault();
                if (!$(this).is(':hover')) {
                    $(this).removeClass('drag-over');
                }
            });

            $('.upload-area').on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('drag-over');
                const input = $(this).find('.variant-file-input')[0];
                const files = e.originalEvent.dataTransfer.files;

                // Create new FileList object
                const dt = new DataTransfer();
                for (let file of files) {
                    dt.items.add(file);
                }
                input.files = dt.files;
                $(input).trigger('change');
            });

            // Cập nhật trạng thái nút upload với animation
            function updateUploadButtonStatus() {
                $('.variant-images-section').each(function() {
                    const variantId = $(this).find('.variant-file-input').data('variant-id');
                    const currentCount = $(`#variant-images-${variantId}`).children().length;
                    const uploadZone = $(this).find('.upload-zone');

                    if (currentCount >= 5) {
                        uploadZone.fadeOut(300);
                    } else {
                        uploadZone.fadeIn(300);
                        uploadZone.find('.text-primary').text(`Tối đa ${5 - currentCount} ảnh`);
                    }
                });
            }

            // Modal với animation
            const modalUpdateSize = new bootstrap.Modal(document.getElementById('modal-update-size'));
            $('.btn-update-size').on('click', function() {
                modalUpdateSize.show();
                // Add entrance animation to variant cards
                setTimeout(() => {
                    $('.variant-card').each(function(index) {
                        $(this).css({
                            'animation': `fadeInUp 0.6s ease-out ${index * 0.1}s both`
                        });
                    });
                }, 150);
            });

            // Form validation với visual feedback
            $('form').on('submit', function(e) {
                let hasErrors = false;

                // Validate required fields
                $(this).find('input[required], select[required], textarea[required]').each(function() {
                    if (!$(this).val().trim()) {
                        hasErrors = true;
                        $(this).addClass('is-invalid');
                        $(this).on('input change', function() {
                            $(this).removeClass('is-invalid');
                        });
                    }
                });

                if (hasErrors) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: $('.is-invalid').first().offset().top - 100
                    }, 500);
                    return false;
                }

                // Show loading state
                const submitBtn = $(this).find('button[type="submit"], input[type="submit"]');
                const originalText = submitBtn.html() || submitBtn.val();

                submitBtn.prop('disabled', true);
                if (submitBtn.is('button')) {
                    submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Đang lưu...');
                } else {
                    submitBtn.val('Đang lưu...');
                }
            });

            // Auto-resize textareas
            $('textarea').on('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            // Initialize upload status
            updateUploadButtonStatus();
        });
    </script>

    <script>
        // Enhanced price formatting functions
        function handlePricePaste(e) {
            e.preventDefault();
            const pasteData = (e.clipboardData || window.clipboardData).getData('text');
            const cleanValue = pasteData.replace(/[^0-9]/g, '');

            if (document.queryCommandSupported && document.queryCommandSupported('insertText')) {
                document.execCommand('insertText', false, cleanValue);
            } else {
                const target = e.target;
                const start = target.selectionStart;
                const end = target.selectionEnd;
                target.value = target.value.substring(0, start) + cleanValue + target.value.substring(end);
                target.selectionStart = target.selectionEnd = start + cleanValue.length;
            }

            // Trigger formatting
            formatPriceInput(e.target);
        }

        function formatPriceInput(input) {
            let value = input.value.replace(/[^0-9]/g, '');
            input.dataset.rawValue = value;

            if (value.length > 0) {
                const numberValue = parseInt(value, 10);
                if (!isNaN(numberValue)) {
                    input.value = numberValue.toLocaleString('vi-VN');

                    // Add visual feedback
                    input.style.color = '#28a745';
                    setTimeout(() => {
                        input.style.color = '';
                    }, 200);
                } else {
                    input.value = '';
                }
            } else {
                input.value = '';
            }
        }

        // Enhanced form submission handling
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Convert formatted prices back to raw values
                    document.querySelectorAll('input[name="price"], input[name^="price_variant"]').forEach(
                        input => {
                            if (input.dataset.rawValue) {
                                input.value = input.dataset.rawValue;
                            } else {
                                input.value = input.value.replace(/[^0-9]/g, '');
                            }
                        });
                });
            }

            // Initialize price formatting for existing values
            document.querySelectorAll('input[name="price"], input[name^="price_variant"]').forEach(input => {
                if (input.value) {
                    formatPriceInput(input);
                }

                // Add real-time validation
                input.addEventListener('input', function() {
                    if (this.value && !this.value.match(/^[\d,\s]+$/)) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });
            });

            // Enhanced form validation messages
            const inputs = document.querySelectorAll('.form-control, .form-select');
            inputs.forEach(input => {
                input.addEventListener('invalid', function(e) {
                    e.preventDefault();
                    this.classList.add('is-invalid');

                    // Custom validation messages
                    const errorDiv = this.parentNode.querySelector('.invalid-feedback') ||
                        document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.innerHTML =
                        `<i class="fas fa-exclamation-circle me-1"></i>${this.validationMessage}`;

                    if (!this.parentNode.querySelector('.invalid-feedback')) {
                        this.parentNode.appendChild(errorDiv);
                    }
                });

                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                    const errorDiv = this.parentNode.querySelector('.invalid-feedback');
                    if (errorDiv) {
                        errorDiv.remove();
                    }
                });
            });
        });
    </script>
@endsection
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

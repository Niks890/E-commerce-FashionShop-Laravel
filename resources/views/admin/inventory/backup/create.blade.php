@extends('admin.master')
@section('title', 'Tạo phiếu nhập hàng')
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
    <div class="container-fluid">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-gradient-primary text-white rounded-top-4">
                <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Tạo phiếu nhập hàng mới</h4>
            </div>
            <div class="card-body p-4">
                <form id="formCreateInventory" method="POST" action="{{ route('inventory.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ auth()->user()->id - 1 }}">

                    <!-- Thông tin nhà cung cấp -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3"><i class="fas fa-truck me-2"></i>Thông tin nhà cung cấp</h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nhà cung cấp <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg border-2" name="provider_id" required>
                                <option value="">-- Chọn nhà cung cấp --</option>
                                @foreach ($providers as $provider)
                                    <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                @endforeach
                            </select>
                            @error('provider_id')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Danh sách sản phẩm -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="text-primary mb-0">
                                    <i class="fas fa-boxes me-2"></i>Danh sách sản phẩm cần nhập
                                    <span class="text-danger">*</span>
                                </h5>
                                <button type="button" id="add-product-btn"
                                    class="btn btn-outline-primary btn-sm rounded-pill">
                                    <i class="fas fa-plus me-1"></i>Thêm sản phẩm
                                </button>
                            </div>

                            <div id="products-container">
                                <!-- Product item template -->
                                <div class="product-item mb-4">
                                    <div class="card border-2 border-primary rounded-3 shadow-sm">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-primary">
                                                <i class="fas fa-box me-2"></i>
                                                Sản phẩm #<span class="product-number">1</span>
                                            </h6>
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm rounded-pill remove-product-btn"
                                                style="display: none;">
                                                <i class="fas fa-trash me-1"></i>Xóa
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <!-- Thông tin cơ bản sản phẩm -->
                                            <div class="row mb-3">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-bold">Tên sản phẩm <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="products[0][product_name]"
                                                        class="form-control product-name border-2"
                                                        placeholder="Nhập tên sản phẩm..." required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-bold">Hình ảnh <span
                                                            class="text-danger">*</span></label>
                                                    <input type="file" name="products[0][image]"
                                                        class="form-control product-image border-2" accept="image/*"
                                                        required>
                                                    <div class="preview-container mt-3 text-center">
                                                        <img class="img-thumbnail rounded-3 shadow d-none preview-img"
                                                            src="" alt=""
                                                            style="max-width: 200px; max-height: 200px; object-fit: cover;">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Phân loại sản phẩm -->
                                            <div class="row mb-3">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-bold">Danh mục <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select form-select-lg border-2"
                                                        name="products[0][category_id]" required>
                                                        <option value="">-- Chọn danh mục --</option>
                                                        @foreach ($cats as $cat)
                                                            <option value="{{ $cat->id }}">{{ $cat->category_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-bold">Thương hiệu <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="products[0][brand_name]"
                                                        class="form-control brand-name border-2"
                                                        placeholder="Nhập tên thương hiệu..." required>
                                                </div>
                                            </div>

                                            <!-- Giá sản phẩm -->
                                            <div class="row mb-3">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-bold">Giá nhập <span
                                                            class="text-danger">*</span></label>
                                                    <div class="input-group input-group-lg">
                                                        <span class="input-group-text bg-light border-2">
                                                            <i class="fas fa-money-bill-wave text-success"></i>
                                                        </span>
                                                        <input type="number" name="products[0][price]"
                                                            class="form-control price-input border-2" placeholder="0"
                                                            min="0" step="10000" required>
                                                        <span class="input-group-text bg-light border-2">VNĐ</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Biến thể sản phẩm -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h6 class="text-primary mb-0">
                                                            <i class="fas fa-palette me-2"></i>Biến thể sản phẩm
                                                            <span class="text-danger">*</span>
                                                        </h6>
                                                        <button type="button"
                                                            class="btn btn-outline-primary btn-sm rounded-pill add-variant-btn">
                                                            <i class="fas fa-plus me-1"></i>Thêm biến thể
                                                        </button>
                                                    </div>

                                                    <div class="variants-container">
                                                        <!-- Variant template -->
                                                        <div class="variant-item mb-3">
                                                            <div
                                                                class="card border-2 border-secondary rounded-3 shadow-sm">
                                                                <div
                                                                    class="card-header bg-light d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0 text-secondary">
                                                                        <i class="fas fa-circle me-2"
                                                                            style="color: #6c757d;"></i>
                                                                        Biến thể #<span class="variant-number">1</span>
                                                                    </h6>
                                                                    <button type="button"
                                                                        class="btn btn-outline-danger btn-sm rounded-pill remove-variant-btn">
                                                                        <i class="fas fa-trash me-1"></i>Xóa
                                                                    </button>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-4 mb-3">
                                                                            <label class="form-label fw-bold">Màu sắc <span
                                                                                    class="text-danger">*</span></label>
                                                                            <div class="color-selection-container">
                                                                                <input type="hidden"
                                                                                    name="products[0][variants][0][color]"
                                                                                    class="selected-color-input">
                                                                                <div
                                                                                    class="color-display-area border-2 rounded-3 p-3 bg-light">
                                                                                    <!-- Hiển thị màu đã chọn -->
                                                                                    <div class="selected-colors-display">
                                                                                        <div
                                                                                            class="text-muted text-center">
                                                                                            <i
                                                                                                class="fas fa-palette me-2"></i>
                                                                                            Chưa chọn màu
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- Nút mở modal -->
                                                                                    <div class="text-center mt-2">
                                                                                        <button type="button"
                                                                                            class="btn btn-outline-primary btn-sm open-color-modal"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#colorModal">
                                                                                            <i
                                                                                                class="fas fa-palette me-1"></i>Chọn
                                                                                            màu
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4 mb-3">
                                                                            <label class="form-label fw-bold">Kích cỡ <span
                                                                                    class="text-danger">*</span></label>
                                                                            <select
                                                                                class="form-select form-select-lg border-2"
                                                                                name="products[0][variants][0][size]"
                                                                                required>
                                                                                <option value="">-- Chọn size --
                                                                                </option>
                                                                                <option value="XS">XS</option>
                                                                                <option value="S">S</option>
                                                                                <option value="M">M</option>
                                                                                <option value="L">L</option>
                                                                                <option value="XL">XL</option>
                                                                                <option value="XXL">XXL</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-4 mb-3">
                                                                            <label class="form-label fw-bold">Số lượng
                                                                                <span class="text-danger">*</span></label>
                                                                            <input type="number"
                                                                                name="products[0][variants][0][quantity]"
                                                                                class="form-control quantity-input border-2"
                                                                                placeholder="0" min="1" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nút submit -->
                    <div class="row">
                        <div class="col-12 text-center">
                            <hr class="my-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow">
                                <i class="fas fa-save me-2"></i>Tạo phiếu nhập
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Color Selection Modal -->
    <div class="modal fade" id="colorModal" tabindex="-1" aria-labelledby="colorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="colorModalLabel">
                        <i class="fas fa-palette me-2"></i>Chọn màu sắc
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Màu sắc có sẵn -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-swatchbook me-2"></i>Màu sắc có sẵn
                        </h6>
                        <div class="row" id="predefined-colors">
                            <!-- Màu sắc cơ bản -->
                            <div class="col-6 col-md-4 col-lg-3 mb-3">
                                <div class="color-option" data-color="Đỏ" data-hex="#dc3545">
                                    <div class="color-preview" style="background-color: #dc3545;"></div>
                                    <span class="color-name">Đỏ</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3 mb-3">
                                <div class="color-option" data-color="Xanh dương" data-hex="#0d6efd">
                                    <div class="color-preview" style="background-color: #0d6efd;"></div>
                                    <span class="color-name">Xanh dương</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3 mb-3">
                                <div class="color-option" data-color="Xanh lá" data-hex="#198754">
                                    <div class="color-preview" style="background-color: #198754;"></div>
                                    <span class="color-name">Xanh lá</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3 mb-3">
                                <div class="color-option" data-color="Vàng" data-hex="#ffc107">
                                    <div class="color-preview" style="background-color: #ffc107;"></div>
                                    <span class="color-name">Vàng</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3 mb-3">
                                <div class="color-option" data-color="Cam" data-hex="#fd7e14">
                                    <div class="color-preview" style="background-color: #fd7e14;"></div>
                                    <span class="color-name">Cam</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3 mb-3">
                                <div class="color-option" data-color="Tím" data-hex="#6f42c1">
                                    <div class="color-preview" style="background-color: #6f42c1;"></div>
                                    <span class="color-name">Tím</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3 mb-3">
                                <div class="color-option" data-color="Hồng" data-hex="#d63384">
                                    <div class="color-preview" style="background-color: #d63384;"></div>
                                    <span class="color-name">Hồng</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3 mb-3">
                                <div class="color-option" data-color="Đen" data-hex="#000000">
                                    <div class="color-preview" style="background-color: #000000;"></div>
                                    <span class="color-name">Đen</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3 mb-3">
                                <div class="color-option" data-color="Trắng" data-hex="#ffffff">
                                    <div class="color-preview"
                                        style="background-color: #ffffff; border: 1px solid #dee2e6;"></div>
                                    <span class="color-name">Trắng</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3 mb-3">
                                <div class="color-option" data-color="Xám" data-hex="#6c757d">
                                    <div class="color-preview" style="background-color: #6c757d;"></div>
                                    <span class="color-name">Xám</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3 mb-3">
                                <div class="color-option" data-color="Nâu" data-hex="#8b4513">
                                    <div class="color-preview" style="background-color: #8b4513;"></div>
                                    <span class="color-name">Nâu</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3 mb-3">
                                <div class="color-option" data-color="Bạc" data-hex="#c0c0c0">
                                    <div class="color-preview" style="background-color: #c0c0c0;"></div>
                                    <span class="color-name">Bạc</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thêm màu tùy chỉnh -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-plus-circle me-2"></i>Thêm màu tùy chỉnh
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tên màu</label>
                                <input type="text" id="customColorName" class="form-control"
                                    placeholder="VD: Xanh navy, Đỏ đô...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Chọn màu</label>
                                <div class="d-flex align-items-center">
                                    <input type="color" id="customColorPicker"
                                        class="form-control form-control-color me-2" value="#000000">
                                    <button type="button" id="addCustomColor" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i>Thêm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Màu sắc tùy chỉnh đã thêm -->
                    <div id="custom-colors-section" style="display: none;">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-heart me-2"></i>Màu sắc tùy chỉnh
                        </h6>
                        <div class="row" id="custom-colors">
                            <!-- Màu tùy chỉnh sẽ được thêm vào đây -->
                        </div>
                    </div>

                    <!-- Màu đã chọn -->
                    <div class="selected-color-preview mt-4 p-3 bg-light rounded-3" style="display: none;">
                        <h6 class="fw-bold mb-2">
                            <i class="fas fa-check-circle me-2 text-success"></i>Màu đã chọn
                        </h6>
                        <div class="d-flex align-items-center">
                            <div class="selected-color-circle me-3"></div>
                            <span class="selected-color-text"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Hủy
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmColorSelection">
                        <i class="fas fa-check me-1"></i>Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .bg-gradient-primary {
            background-color: #007bff;
            background-image: linear-gradient(180deg, #007bff, #0069d9);
        }

        /* Style cho color picker */
        .color-option {
            display: flex;
            align-items: center;
            padding: 8px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .color-option:hover {
            background-color: #f8f9fa;
        }

        .color-option.selected {
            background-color: #e7f1ff;
            border: 1px solid #0d6efd;
        }

        .color-preview {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
            border: 1px solid #dee2e6;
        }

        .color-name {
            font-weight: 500;
        }

        /* Style cho các card sản phẩm và biến thể */
        .product-item .card-header {
            transition: all 0.3s ease;
        }

        .variant-item .card-header {
            transition: all 0.3s ease;
        }

        /* Hiệu ứng khi hover */
        .product-item:hover .card-header {
            background-color: #f8f9fa !important;
        }

        .variant-item:hover .card-header {
            background-color: #f8f9fa !important;
        }

        /* Style cho nút */
        .back-btn:hover {
            background-color: #007bff !important;
            color: white !important;
        }

        /* Style cho preview ảnh */
        .preview-img {
            transition: all 0.3s ease;
        }

        /* Style cho selected color preview */
        .selected-color-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 1px solid #dee2e6;
        }
    </style>
@endsection


@section('js')
    <script>
        // Validate form trước khi submit
        $('#formCreateInventory').on('submit', function(e) {
            let isValid = true;
            let errorMessages = [];

            // Duyệt qua từng sản phẩm
            $('.product-item').each(function(productIndex) {
                const productItem = $(this);
                const variants = productItem.find('.variant-item');
                const variantCombinations = new Set();

                // Duyệt qua từng biến thể của sản phẩm
                variants.each(function() {
                    const variantItem = $(this);

                    // Validate màu sắc đã được chọn
                    const colorInput = variantItem.find('.selected-color-input');
                    const colorValue = colorInput.val().trim();

                    if (!colorValue) {
                        isValid = false;
                        variantItem.find('.color-display-area').addClass('border-danger');
                        errorMessages.push(
                            `Biến thể #${variantItem.find('.variant-number').text()} của sản phẩm #${productIndex + 1}: Vui lòng chọn màu sắc`
                        );
                    } else {
                        variantItem.find('.color-display-area').removeClass('border-danger');
                    }

                    // Validate không nhập số lượng âm
                    const quantityInput = variantItem.find('.quantity-input');
                    const quantityValue = parseInt(quantityInput.val());

                    if (quantityValue < 1) {
                        isValid = false;
                        quantityInput.addClass('is-invalid');
                        errorMessages.push(
                            `Biến thể #${variantItem.find('.variant-number').text()} của sản phẩm #${productIndex + 1}: Số lượng phải lớn hơn 0`
                        );
                    } else {
                        quantityInput.removeClass('is-invalid');
                    }

                    // Validate không nhập giá âm
                    const priceInput = productItem.find('.price-input');
                    const priceValue = parseInt(priceInput.val());

                    if (priceValue < 0) {
                        isValid = false;
                        priceInput.addClass('is-invalid');
                        errorMessages.push(`Sản phẩm #${productIndex + 1}: Giá nhập không được âm`);
                    } else {
                        priceInput.removeClass('is-invalid');
                    }

                    // Validate combination màu + size không trùng lặp
                    const sizeValue = variantItem.find('select[name$="[size]"]').val();
                    const combination = `${sizeValue}-${colorValue.toUpperCase()}`;

                    if (variantCombinations.has(combination)) {
                        isValid = false;
                        variantItem.find('.color-display-area').addClass('border-danger');
                        variantItem.find('select[name$="[size]"]').addClass('is-invalid');
                        errorMessages.push(
                            `Sản phẩm #${productIndex + 1}: Biến thể ${combination} đã bị trùng lặp`
                        );
                    } else {
                        variantCombinations.add(combination);
                        variantItem.find('.color-display-area').removeClass('border-danger');
                        variantItem.find('select[name$="[size]"]').removeClass('is-invalid');
                    }
                });
            });

            // Nếu có lỗi thì hiển thị và ngăn form submit
            if (!isValid) {
                e.preventDefault();

                // Tạo thông báo lỗi
                let errorMessage = 'Vui lòng sửa các lỗi sau:\n';
                errorMessages.forEach((msg, index) => {
                    errorMessage += `\n${index + 1}. ${msg}`;
                });

                alert(errorMessage);
            }
        });

        // Thêm validate real-time cho các trường
        $(document).on('input', '.quantity-input', function() {
            const value = parseInt($(this).val());
            if (value < 1) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        $(document).on('input', '.price-input', function() {
            const value = parseInt($(this).val());
            if (value < 0) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            let productCount = 1;
            let variantCounts = [1]; // Mảng lưu số lượng biến thể của từng sản phẩm
            let currentColorTarget = null; // Lưu trữ target hiện tại cho color selection
            let selectedColor = null; // Lưu trữ màu đã chọn
            let customColors = []; // Lưu trữ màu tùy chỉnh

            // Color Modal Management
            $(document).on('click', '.open-color-modal', function() {
                currentColorTarget = $(this).closest('.color-selection-container');
                selectedColor = null;

                // Reset modal state
                $('.color-option').removeClass('selected');
                $('.selected-color-preview').hide();
                $('#confirmColorSelection').prop('disabled', true);
            });

            // Chọn màu từ danh sách có sẵn
            $(document).on('click', '.color-option', function() {
                $('.color-option').removeClass('selected');
                $(this).addClass('selected');

                selectedColor = {
                    name: $(this).data('color'),
                    hex: $(this).data('hex')
                };

                updateSelectedColorPreview();
                $('#confirmColorSelection').prop('disabled', false);
            });

            // Thêm màu tùy chỉnh
            $('#addCustomColor').click(function() {
                const colorName = $('#customColorName').val().trim();
                const colorHex = $('#customColorPicker').val();

                if (!colorName) {
                    alert('Vui lòng nhập tên màu!');
                    return;
                }

                // Kiểm tra trùng lặp
                const existingColor = customColors.find(c => c.name.toLowerCase() === colorName
                    .toLowerCase());
                if (existingColor) {
                    alert('Màu này đã tồn tại!');
                    return;
                }

                // Thêm màu mới
                const newColor = {
                    name: colorName,
                    hex: colorHex
                };

                customColors.push(newColor);
                addCustomColorToModal(newColor);

                // Reset form
                $('#customColorName').val('');
                $('#customColorPicker').val('#000000');

                // Hiển thị section màu tùy chỉnh
                $('#custom-colors-section').show();
            });

            // Thêm màu tùy chỉnh vào modal
            function addCustomColorToModal(color) {
                const colorElement = `
                    <div class="col-6 col-md-4 col-lg-3 mb-3">
                        <div class="color-option" data-color="${color.name}" data-hex="${color.hex}">
                            <div class="color-preview" style="background-color: ${color.hex};"></div>
                            <span class="color-name">${color.name}</span>
                        </div>
                    </div>
                `;
                $('#custom-colors').append(colorElement);
            }

            // Cập nhật preview màu đã chọn
            function updateSelectedColorPreview() {
                if (selectedColor) {
                    $('.selected-color-preview').show();
                    $('.selected-color-circle').css('background-color', selectedColor.hex);
                    $('.selected-color-text').text(`${selectedColor.name} (${selectedColor.hex})`);
                }
            }

            // Xác nhận chọn màu
            $('#confirmColorSelection').click(function() {
                if (selectedColor && currentColorTarget) {
                    // Cập nhật input ẩn với giá trị màu
                    currentColorTarget.find('.selected-color-input').val(selectedColor.name);

                    // Cập nhật hiển thị màu đã chọn
                    const displayArea = currentColorTarget.find('.selected-colors-display');
                    displayArea.html(`
                        <div class="d-flex align-items-center">
                            <div class="color-circle me-2" style="background-color: ${selectedColor.hex}; width: 20px; height: 20px; border-radius: 50%; border: 1px solid #dee2e6;"></div>
                            <span>${selectedColor.name}</span>
                        </div>
                    `);

                    // Đóng modal
                    $('#colorModal').modal('hide');
                }
            });

            // Thêm sản phẩm mới
            $('#add-product-btn').click(function() {
                productCount++;
                variantCounts.push(1); // Thêm entry mới với 1 biến thể

                const newProduct = $('.product-item').first().clone();
                const newIndex = productCount - 1;

                // Cập nhật các thuộc tính name và reset giá trị
                newProduct.find('[name]').each(function() {
                    const name = $(this).attr('name').replace(/products\[\d+\]/,
                        `products[${newIndex}]`);
                    $(this).attr('name', name);

                    if ($(this).is('input:not([type=hidden]), select')) {
                        $(this).val('');
                    }

                    if ($(this).is('input[type=file]')) {
                        $(this).val('');
                        $(this).siblings('.preview-container').find('.preview-img').addClass(
                            'd-none').attr('src', '');
                    }
                });

                // Cập nhật số thứ tự sản phẩm
                newProduct.find('.product-number').text(productCount);

                // Reset các biến thể, chỉ giữ lại 1 biến thể mẫu
                const variantsContainer = newProduct.find('.variants-container');
                variantsContainer.empty();

                // Thêm lại biến thể mẫu với index mới
                const newVariant = $('.variant-item').first().clone();
                newVariant.find('[name]').each(function() {
                    const name = $(this).attr('name')
                        .replace(/products\[\d+\]/, `products[${newIndex}]`)
                        .replace(/variants\[\d+\]/, `variants[0]`);
                    $(this).attr('name', name);

                    if ($(this).is('input:not([type=hidden]), select')) {
                        $(this).val('');
                    }
                });

                newVariant.find('.variant-number').text(1);
                variantsContainer.append(newVariant);

                // Hiển thị nút xóa sản phẩm
                newProduct.find('.remove-product-btn').show();

                // Thêm vào container
                $('#products-container').append(newProduct);

                // Cuộn đến sản phẩm mới
                $('html, body').animate({
                    scrollTop: newProduct.offset().top - 100
                }, 500);
            });

            // Thêm biến thể mới
            $(document).on('click', '.add-variant-btn', function() {
                const productItem = $(this).closest('.product-item');
                const productIndex = $('#products-container .product-item').index(productItem);
                const variantsContainer = productItem.find('.variants-container');
                const variantCount = variantsContainer.children().length;

                // Clone biến thể đầu tiên
                const newVariant = variantsContainer.children().first().clone();

                // Cập nhật các thuộc tính name
                newVariant.find('[name]').each(function() {
                    const name = $(this).attr('name')
                        .replace(/variants\[\d+\]/, `variants[${variantCount}]`);
                    $(this).attr('name', name);

                    if ($(this).is('input:not([type=hidden]), select')) {
                        $(this).val('');
                    }
                });

                // Cập nhật số thứ tự biến thể
                newVariant.find('.variant-number').text(variantCount + 1);

                // Reset màu sắc đã chọn
                newVariant.find('.selected-color-input').val('');
                newVariant.find('.selected-colors-display').html(`
                    <div class="text-muted text-center">
                        <i class="fas fa-palette me-2"></i>
                        Chưa chọn màu
                    </div>
                `);

                // Thêm vào container
                variantsContainer.append(newVariant);

                // Cuộn đến biến thể mới
                $('html, body').animate({
                    scrollTop: newVariant.offset().top - 100
                }, 500);
            });

            // Xóa biến thể
            $(document).on('click', '.remove-variant-btn', function() {
                const variantItem = $(this).closest('.variant-item');
                const variantsContainer = variantItem.parent();

                // Chỉ cho phép xóa nếu còn nhiều hơn 1 biến thể
                if (variantsContainer.children().length > 1) {
                    variantItem.remove();

                    // Cập nhật lại số thứ tự các biến thể còn lại
                    variantsContainer.children().each(function(index) {
                        const variant = $(this);
                        variant.find('.variant-number').text(index + 1);

                        // Cập nhật các thuộc tính name
                        variant.find('[name]').each(function() {
                            const name = $(this).attr('name')
                                .replace(/variants\[\d+\]/, `variants[${index}]`);
                            $(this).attr('name', name);
                        });
                    });
                } else {
                    alert('Mỗi sản phẩm phải có ít nhất 1 biến thể!');
                }
            });

            // Xóa sản phẩm
            $(document).on('click', '.remove-product-btn', function() {
                const productItem = $(this).closest('.product-item');

                // Chỉ cho phép xóa nếu còn nhiều hơn 1 sản phẩm
                if ($('#products-container .product-item').length > 1) {
                    productItem.remove();

                    // Cập nhật lại số thứ tự các sản phẩm còn lại
                    $('#products-container .product-item').each(function(index) {
                        const product = $(this);
                        product.find('.product-number').text(index + 1);

                        // Cập nhật các thuộc tính name
                        product.find('[name]').each(function() {
                            const name = $(this).attr('name')
                                .replace(/products\[\d+\]/, `products[${index}]`);
                            $(this).attr('name', name);

                            // Cập nhật index cho các biến thể
                            if ($(this).attr('name').includes('variants')) {
                                const newName = $(this).attr('name')
                                    .replace(/products\[\d+\]/, `products[${index}]`);
                                $(this).attr('name', newName);
                            }
                        });
                    });
                } else {
                    alert('Phải có ít nhất 1 sản phẩm trong phiếu nhập!');
                }
            });

            // Xem trước hình ảnh khi chọn file
            $(document).on('change', '.product-image', function() {
                const input = $(this);
                const preview = input.siblings('.preview-container').find('.preview-img');
                const file = input[0].files[0];

                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.removeClass('d-none').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(file);
                } else {
                    preview.addClass('d-none').attr('src', '');
                }
            });

            $(document).on('input', '.price-input', function() {
                let value = $(this).val();
                // Kiểm tra nếu giá trị không phải là số hợp lệ
                if (isNaN(value) || value < 0) {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
        });
    </script>
@endsection

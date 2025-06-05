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
                                                                            <input type="text"
                                                                                name="products[0][variants][0][color]"
                                                                                class="form-control color-input border-2"
                                                                                placeholder="VD: Đỏ, Xanh dương..."
                                                                                required>
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

                    // Validate màu sắc chỉ được nhập 1 màu
                    const colorInput = variantItem.find('.color-input');
                    const colorValue = colorInput.val().trim();

                    if (colorValue.includes(',')) {
                        isValid = false;
                        colorInput.addClass('is-invalid');
                        errorMessages.push(
                            `Biến thể #${variantItem.find('.variant-number').text()} của sản phẩm #${productIndex + 1}: Chỉ được nhập 1 màu duy nhất`
                        );
                    } else {
                        colorInput.removeClass('is-invalid');
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
                        colorInput.addClass('is-invalid');
                        variantItem.find('select[name$="[size]"]').addClass('is-invalid');
                        errorMessages.push(
                            `Sản phẩm #${productIndex + 1}: Biến thể ${combination} đã bị trùng lặp`
                        );
                    } else {
                        variantCombinations.add(combination);
                        colorInput.removeClass('is-invalid');
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

        $(document).on('input', '.color-input', function() {
            const value = $(this).val().trim();
            if (value.includes(',')) {
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

            // Thêm sản phẩm mới
            $('#add-product-btn').click(function() {
                const newProductIndex = productCount;
                variantCounts.push(1); // Thêm một mục mới với 1 biến thể

                const newProduct = $(`
            <div class="product-item mb-4">
                <div class="card border-2 border-primary rounded-3 shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-primary">
                            <i class="fas fa-box me-2"></i>
                            Sản phẩm #<span class="product-number">${newProductIndex + 1}</span>
                        </h6>
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill remove-product-btn">
                            <i class="fas fa-trash me-1"></i>Xóa
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Thông tin cơ bản sản phẩm -->
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tên sản phẩм <span class="text-danger">*</span></label>
                                <input type="text" name="products[${newProductIndex}][product_name]" class="form-control product-name border-2"
                                       placeholder="Nhập tên sản phẩm..." required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Hình ảnh <span class="text-danger">*</span></label>
                                <input type="file" name="products[${newProductIndex}][image]" class="form-control product-image border-2"
                                       accept="image/*" required>
                                <div class="preview-container mt-3 text-center">
                                    <img class="img-thumbnail rounded-3 shadow d-none preview-img" src="" alt=""
                                         style="max-width: 200px; max-height: 200px; object-fit: cover;">
                                </div>
                            </div>
                        </div>

                        <!-- Phân loại sản phẩm -->
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg border-2" name="products[${newProductIndex}][category_id]" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach ($cats as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Thương hiệu <span class="text-danger">*</span></label>
                                <input type="text" name="products[${newProductIndex}][brand_name]" class="form-control brand-name border-2"
                                       placeholder="Nhập tên thương hiệu..." required>
                            </div>
                        </div>

                        <!-- Giá sản phẩm -->
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Giá nhập <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-2">
                                        <i class="fas fa-money-bill-wave text-success"></i>
                                    </span>
                                    <input type="number" name="products[${newProductIndex}][price]" class="form-control price-input border-2"
                                           placeholder="0" min="0" step="1000" required>
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
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill add-variant-btn">
                                        <i class="fas fa-plus me-1"></i>Thêm biến thể
                                    </button>
                                </div>

                                <div class="variants-container">
                                    <!-- Variant template -->
                                    <div class="variant-item mb-3">
                                        <div class="card border-2 border-secondary rounded-3 shadow-sm">
                                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 text-secondary">
                                                    <i class="fas fa-circle me-2" style="color: #6c757d;"></i>
                                                    Biến thể #<span class="variant-number">1</span>
                                                </h6>
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill remove-variant-btn">
                                                    <i class="fas fa-trash me-1"></i>Xóa
                                                </button>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label fw-bold">Màu sắc <span class="text-danger">*</span></label>
                                                        <input type="text" name="products[${newProductIndex}][variants][0][color]" class="form-control color-input border-2"
                                                               placeholder="VD: Đỏ, Xanh dương..." required>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label fw-bold">Kích cỡ <span class="text-danger">*</span></label>
                                                        <select class="form-select form-select-lg border-2" name="products[${newProductIndex}][variants][0][size]" required>
                                                            <option value="">-- Chọn size --</option>
                                                            <option value="XS">XS</option>
                                                            <option value="S">S</option>
                                                            <option value="M">M</option>
                                                            <option value="L">L</option>
                                                            <option value="XL">XL</option>
                                                            <option value="XXL">XXL</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label fw-bold">Số lượng <span class="text-danger">*</span></label>
                                                        <input type="number" name="products[${newProductIndex}][variants][0][quantity]" class="form-control quantity-input border-2"
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
        `);

                $('#products-container').append(newProduct);
                productCount++;

                // Ẩn nút xóa nếu chỉ có 1 sản phẩm
                if (productCount > 1) {
                    $('.remove-product-btn').show();
                }

                // Xử lý preview ảnh cho sản phẩm mới
                newProduct.find('.product-image').change(function(e) {
                    const file = e.target.files[0];
                    const previewImg = $(this).closest('.product-item').find('.preview-img');
                    const validTypes = ["image/jpg", "image/jpeg", "image/png", "image/gif",
                        "image/webp", "image/avif"
                    ];

                    if (file && validTypes.includes(file.type)) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.attr('src', e.target.result).removeClass('d-none').hide()
                                .fadeIn(300);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        previewImg.fadeOut(300, function() {
                            $(this).addClass('d-none');
                        });
                    }
                });
            });

            // Xóa sản phẩm
            $(document).on('click', '.remove-product-btn', function() {
                const productItem = $(this).closest('.product-item');
                const index = productItem.index();

                productItem.slideUp(300, function() {
                    $(this).remove();
                    variantCounts.splice(index, 1);
                    productCount--;

                    // Cập nhật lại số thứ tự sản phẩm
                    $('.product-item').each(function(i) {
                        $(this).find('.product-number').text(i + 1);

                        // Cập nhật lại name attribute
                        $(this).find('[name^="products["]').each(function() {
                            const name = $(this).attr('name').replace(
                                /products\[\d+\]/g, `products[${i}]`);
                            $(this).attr('name', name);
                        });
                    });

                    // Ẩn nút xóa nếu chỉ còn 1 sản phẩm
                    if (productCount <= 1) {
                        $('.remove-product-btn').hide();
                    }
                });
            });

            // Thêm biến thể mới
            $(document).on('click', '.add-variant-btn', function() {
                const productItem = $(this).closest('.product-item');
                const productIndex = productItem.index();
                const variantsContainer = productItem.find('.variants-container');
                const variantCount = productItem.find('.variant-item').length;

                const newVariant = $(`
            <div class="variant-item mb-3">
                <div class="card border-2 border-secondary rounded-3 shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-secondary">
                            <i class="fas fa-circle me-2" style="color: #6c757d;"></i>
                            Biến thể #<span class="variant-number">${variantCount + 1}</span>
                        </h6>
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill remove-variant-btn">
                            <i class="fas fa-trash me-1"></i>Xóa
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Màu sắc <span class="text-danger">*</span></label>
                                <input type="text" name="products[${productIndex}][variants][${variantCount}][color]" class="form-control color-input border-2"
                                       placeholder="VD: Đỏ, Xanh dương..." required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Kích cỡ <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg border-2" name="products[${productIndex}][variants][${variantCount}][size]" required>
                                    <option value="">-- Chọn size --</option>
                                    <option value="XS">XS</option>
                                    <option value="S">S</option>
                                    <option value="M">M</option>
                                    <option value="L">L</option>
                                    <option value="XL">XL</option>
                                    <option value="XXL">XXL</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Số lượng <span class="text-danger">*</span></label>
                                <input type="number" name="products[${productIndex}][variants][${variantCount}][quantity]" class="form-control quantity-input border-2"
                                       placeholder="0" min="1" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);

                variantsContainer.append(newVariant);
                variantCounts[productIndex]++;
            });

            // Xóa biến thể
            $(document).on('click', '.remove-variant-btn', function() {
                const variantItem = $(this).closest('.variant-item');
                const variantsContainer = variantItem.closest('.variants-container');

                variantItem.slideUp(300, function() {
                    $(this).remove();

                    // Cập nhật lại số thứ tự biến thể
                    variantsContainer.find('.variant-item').each(function(i) {
                        $(this).find('.variant-number').text(i + 1);

                        // Cập nhật lại name attribute
                        const productItem = $(this).closest('.product-item');
                        const productIndex = productItem.index();

                        $(this).find('[name^="products["]').each(function() {
                            const name = $(this).attr('name').replace(
                                /variants\[\d+\]/g, `variants[${i}]`);
                            $(this).attr('name', name);
                        });
                    });
                });
            });

            // Xử lý preview ảnh
            $(document).on('change', '.product-image', function(e) {
                const file = e.target.files[0];
                const previewImg = $(this).closest('.product-item').find('.preview-img');
                const validTypes = ["image/jpg", "image/jpeg", "image/png", "image/gif", "image/webp",
                    "image/avif"
                ];

                if (file) {
                    if (validTypes.includes(file.type)) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.attr('src', e.target.result).removeClass('d-none').hide().fadeIn(
                                300);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        $(this).val("");
                        previewImg.fadeOut(300);
                        alert("Vui lòng chọn file ảnh hợp lệ (JPG, PNG, GIF, WebP, AVIF)!");
                    }
                }
            });

            // Validate form trước khi submit
            $('#formCreateInventory').on('submit', function(e) {
                let isValid = true;

                // Kiểm tra mỗi sản phẩm có ít nhất 1 biến thể
                $('.product-item').each(function() {
                    const variantCount = $(this).find('.variant-item').length;
                    if (variantCount === 0) {
                        isValid = false;
                        showNotification('error', 'Mỗi sản phẩm phải có ít nhất 1 biến thể!');
                        return false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    return;
                }

                showNotification('success', 'Đang lưu phiếu nhập...');
            });

            // Hàm hiển thị thông báo
            function showNotification(type, message) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';

                const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed"
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="${icon} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);

                $('body').append(notification);

                setTimeout(function() {
                    notification.alert('close');
                }, 3000);
            }
        });
    </script>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .alert {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .position-fixed.alert {
            animation: slideInRight 0.3s ease-out;
        }
    </style>
@endsection

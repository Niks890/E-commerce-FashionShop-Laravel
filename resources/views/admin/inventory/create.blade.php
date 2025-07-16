@can('warehouse workers')
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

                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3"><i class="fas fa-truck me-2"></i>Thông tin chung nhà cung cấp và
                                ghi chú</h5>
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ghi chú<span class="text-danger">*</span></label>
                            <textarea class="form-control" name="note_inventory" cols="60" rows="5" required></textarea>
                            @error('note_inventory')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

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
                                                            src="" alt="Xem trước ảnh"
                                                            style="max-width: 200px; max-height: 200px; object-fit: cover;">
                                                    </div>
                                                </div>
                                            </div>

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

                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h6 class="text-primary mb-0">
                                                            <i class="fas fa-palette me-2"></i>Biến thể sản phẩm
                                                            <span class="text-danger">*</span>
                                                        </h6>
                                                        <button type="button"
                                                            class="btn btn-outline-primary btn-sm rounded-pill add-variant-btn">
                                                            <i class="fas fa-plus me-1"></i>Thêm màu
                                                        </button>
                                                    </div>

                                                    <div class="variants-container">
                                                        <div class="variant-item mb-3">
                                                            <div class="card border-2 border-secondary rounded-3 shadow-sm">
                                                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                                    <div class="w-50">
                                                                         <label class="form-label fw-bold mb-0">Màu sắc #<span class="variant-number">1</span><span class="text-danger">*</span></label>
                                                                         <input type="text" name="products[0][variants][0][color]" class="form-control color-input border-2" placeholder="VD: Chọn hoặc nhập màu..." required>
                                                                    </div>
                                                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill remove-variant-btn">
                                                                        <i class="fas fa-trash me-1"></i>Xóa màu
                                                                    </button>
                                                                </div>
                                                                <div class="card-body">
                                                                    <h6 class="text-secondary fw-bold mb-3"><i class="fas fa-ruler-combined me-2"></i>Size & Số lượng cho màu này</h6>
                                                                    <div class="size-quantity-container">
                                                                        <div class="size-quantity-item row align-items-center mb-2">
                                                                            <div class="col-md-5">
                                                                                <select class="form-select form-select-lg border-2 size-select" name="products[0][variants][0][details][0][size]" required>
                                                                                    <option value="">-- Chọn size --</option>
                                                                                    <option value="XS">XS</option>
                                                                                    <option value="S">S</option>
                                                                                    <option value="M">M</option>
                                                                                    <option value="L">L</option>
                                                                                    <option value="XL">XL</option>
                                                                                    <option value="XXL">XXL</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-5">
                                                                                <input type="number" name="products[0][variants][0][details][0][quantity]" class="form-control quantity-input border-2" placeholder="Số lượng" min="1" required>
                                                                            </div>
                                                                            <div class="col-md-2 text-end">
                                                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill remove-size-btn" style="display: none;">
                                                                                    <i class="fas fa-times"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill mt-2 add-size-btn">
                                                                        <i class="fas fa-plus me-1"></i>Thêm size
                                                                    </button>
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



    {{-- modal chọn màu --}}
    <div class="modal fade color-modal" id="colorSelectionModal" tabindex="-1"
        aria-labelledby="colorSelectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="colorSelectionModalLabel">
                        <i class="fas fa-palette me-2"></i>Chọn màu sắc
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-2">Thêm màu mới</label>
                        <div class="color-input-group">
                            <input type="text" id="newColorInput" class="form-control form-control-lg"
                                placeholder="Nhập tên màu mới...">
                            <button class="btn btn-primary btn-sm btn-add-color" id="addColorBtn">
                                <i class="fas fa-plus me-1"></i>Thêm
                            </button>
                        </div>
                        <small class="text-muted">Ví dụ: Đỏ cam, Xanh ngọc, Tím lavender...</small>
                        <div class="invalid-color" id="duplicateColorError">
                            <i class="fas fa-exclamation-circle me-1"></i>Màu này đã tồn tại
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Bảng màu phổ biến</h6>
                        <div class="color-palette" id="colorPalette">
                            </div>
                    </div>

                    <div class="selected-color-preview">
                        <h6 class="fw-bold mb-0 me-3">Màu đã chọn:</h6>
                        <div id="selectedColorContainer">
                            <span class="selected-color-badge d-none" id="selectedColorBadge">
                                <span id="selectedColorText"></span>
                                <button class="btn-close btn-close-white ms-2" id="removeSelectedColor"></button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Hủy
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" id="confirmColorSelection">
                        <i class="fas fa-check me-1"></i>Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <style>
        .color-modal .modal-content {
            border-radius: 12px;
            overflow: hidden;
        }

        .color-modal .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: none;
            padding: 1.2rem 1.5rem;
        }

        .color-modal .modal-title {
            font-weight: 600;
            font-size: 1.25rem;
        }

        .color-modal .modal-body {
            padding: 1.5rem;
        }

        .color-modal .modal-footer {
            border-top: none;
            padding: 1rem 1.5rem;
            background-color: #f8f9fa;
        }

        .color-palette {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }

        .color-option {
            height: 40px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 500;
            color: white;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .color-option:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .color-option.selected {
            transform: scale(1.05);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.8), 0 0 0 6px var(--selected-color);
        }

        .color-option .remove-color {
            position: absolute;
            top: 2px;
            right: 2px;
            opacity: 0;
            transition: opacity 0.2s;
            font-size: 10px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 50%;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .color-option:hover .remove-color {
            opacity: 1;
        }

        .selected-color-preview {
            display: flex;
            align-items: center;
            margin-top: 15px;
            padding: 10px;
            border-radius: 8px;
            background-color: #f8f9fa;
        }

        .selected-color-badge {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            color: white;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .color-input-group {
            position: relative;
        }

        .color-input-group .btn-add-color {
            position: absolute;
            right: 5px;
            top: 5px;
            border-radius: 20px;
            padding: 5px 12px;
            font-size: 12px;
        }

        .invalid-color {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

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
    </style>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            let productCount = 1;

            // ===================================
            // SẢN PHẨM
            // ===================================

            // Thêm sản phẩm mới
            $('#add-product-btn').click(function() {
                const newProductIndex = productCount;
                const newProduct = $(`
                    <div class="product-item mb-4" style="display:none;">
                        <div class="card border-2 border-primary rounded-3 shadow-sm">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-primary"><i class="fas fa-box me-2"></i>Sản phẩm #<span class="product-number">${newProductIndex + 1}</span></h6>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill remove-product-btn"><i class="fas fa-trash me-1"></i>Xóa</button>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                                        <input type="text" name="products[${newProductIndex}][product_name]" class="form-control product-name border-2" placeholder="Nhập tên sản phẩm..." required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Hình ảnh <span class="text-danger">*</span></label>
                                        <input type="file" name="products[${newProductIndex}][image]" class="form-control product-image border-2" accept="image/*" required>
                                        <div class="preview-container mt-3 text-center">
                                            <img class="img-thumbnail rounded-3 shadow d-none preview-img" src="" alt="Xem trước ảnh" style="max-width: 200px; max-height: 200px; object-fit: cover;">
                                        </div>
                                    </div>
                                </div>
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
                                        <input type="text" name="products[${newProductIndex}][brand_name]" class="form-control brand-name border-2" placeholder="Nhập tên thương hiệu..." required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Giá nhập <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text bg-light border-2"><i class="fas fa-money-bill-wave text-success"></i></span>
                                            <input type="number" name="products[${newProductIndex}][price]" class="form-control price-input border-2" placeholder="0" min="0" step="10000" required>
                                            <span class="input-group-text bg-light border-2">VNĐ</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="text-primary mb-0"><i class="fas fa-palette me-2"></i>Biến thể sản phẩm <span class="text-danger">*</span></h6>
                                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill add-variant-btn"><i class="fas fa-plus me-1"></i>Thêm màu</button>
                                        </div>
                                        <div class="variants-container">
                                            <div class="variant-item mb-3">
                                                <div class="card border-2 border-secondary rounded-3 shadow-sm">
                                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                         <div class="w-50">
                                                            <label class="form-label fw-bold mb-0">Màu sắc #<span class="variant-number">1</span><span class="text-danger">*</span></label>
                                                            <input type="text" name="products[${newProductIndex}][variants][0][color]" class="form-control color-input border-2" placeholder="VD: Chọn hoặc nhập màu..." required>
                                                         </div>
                                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill remove-variant-btn"><i class="fas fa-trash me-1"></i>Xóa màu</button>
                                                    </div>
                                                    <div class="card-body">
                                                        <h6 class="text-secondary fw-bold mb-3"><i class="fas fa-ruler-combined me-2"></i>Size & Số lượng cho màu này</h6>
                                                        <div class="size-quantity-container">
                                                            <div class="size-quantity-item row align-items-center mb-2">
                                                                <div class="col-md-5">
                                                                    <select class="form-select form-select-lg border-2 size-select" name="products[${newProductIndex}][variants][0][details][0][size]" required>
                                                                        <option value="">-- Chọn size --</option>
                                                                        <option value="XS">XS</option><option value="S">S</option><option value="M">M</option><option value="L">L</option><option value="XL">XL</option><option value="XXL">XXL</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="number" name="products[${newProductIndex}][variants][0][details][0][quantity]" class="form-control quantity-input border-2" placeholder="Số lượng" min="1" required>
                                                                </div>
                                                                <div class="col-md-2 text-end">
                                                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill remove-size-btn" style="display: none;"><i class="fas fa-times"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill mt-2 add-size-btn"><i class="fas fa-plus me-1"></i>Thêm size</button>
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
                newProduct.slideDown(300);
                productCount++;

                updateRemoveProductButtons();
            });

            // Xóa sản phẩm
            $(document).on('click', '.remove-product-btn', function() {
                const productItem = $(this).closest('.product-item');
                productItem.slideUp(300, function() {
                    $(this).remove();
                    productCount--;
                    updateProductIndexes();
                    updateRemoveProductButtons();
                });
            });

            // Cập nhật lại index của sản phẩm
            function updateProductIndexes() {
                $('.product-item').each(function(productIndex) {
                    $(this).find('.product-number').text(productIndex + 1);
                    $(this).find('[name^="products["]').each(function() {
                        const name = $(this).attr('name').replace(/products\[\d+\]/, `products[${productIndex}]`);
                        $(this).attr('name', name);
                    });
                });
            }

            // Ẩn/hiện nút xóa sản phẩm
            function updateRemoveProductButtons() {
                if ($('.product-item').length <= 1) {
                    $('.remove-product-btn').hide();
                } else {
                    $('.remove-product-btn').show();
                }
            }


            // ===================================
            // BIẾN THỂ (MÀU)
            // ===================================

            // Thêm biến thể (màu) mới
            $(document).on('click', '.add-variant-btn', function() {
                const productItem = $(this).closest('.product-item');
                const productIndex = productItem.index();
                const variantsContainer = productItem.find('.variants-container');
                const variantCount = variantsContainer.find('.variant-item').length;

                const newVariant = $(`
                    <div class="variant-item mb-3" style="display:none;">
                        <div class="card border-2 border-secondary rounded-3 shadow-sm">
                             <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                 <div class="w-50">
                                    <label class="form-label fw-bold mb-0">Màu sắc #<span class="variant-number">${variantCount + 1}</span><span class="text-danger">*</span></label>
                                    <input type="text" name="products[${productIndex}][variants][${variantCount}][color]" class="form-control color-input border-2" placeholder="VD: Chọn hoặc nhập màu..." required>
                                 </div>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill remove-variant-btn"><i class="fas fa-trash me-1"></i>Xóa màu</button>
                            </div>
                            <div class="card-body">
                                <h6 class="text-secondary fw-bold mb-3"><i class="fas fa-ruler-combined me-2"></i>Size & Số lượng cho màu này</h6>
                                <div class="size-quantity-container">
                                    <div class="size-quantity-item row align-items-center mb-2">
                                        <div class="col-md-5">
                                            <select class="form-select form-select-lg border-2 size-select" name="products[${productIndex}][variants][${variantCount}][details][0][size]" required>
                                                <option value="">-- Chọn size --</option>
                                                <option value="XS">XS</option><option value="S">S</option><option value="M">M</option><option value="L">L</option><option value="XL">XL</option><option value="XXL">XXL</option>
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="number" name="products[${productIndex}][variants][${variantCount}][details][0][quantity]" class="form-control quantity-input border-2" placeholder="Số lượng" min="1" required>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill remove-size-btn" style="display: none;"><i class="fas fa-times"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill mt-2 add-size-btn"><i class="fas fa-plus me-1"></i>Thêm size</button>
                            </div>
                        </div>
                    </div>
                `);

                variantsContainer.append(newVariant);
                newVariant.slideDown(300);
                updateRemoveVariantButtons(variantsContainer);
            });

            // Xóa biến thể (màu)
            $(document).on('click', '.remove-variant-btn', function() {
                const variantItem = $(this).closest('.variant-item');
                const variantsContainer = variantItem.closest('.variants-container');
                variantItem.slideUp(300, function() {
                    $(this).remove();
                    updateVariantIndexes(variantsContainer);
                    updateRemoveVariantButtons(variantsContainer);
                });
            });

            // Cập nhật lại index của biến thể
            function updateVariantIndexes(variantsContainer) {
                 const productIndex = variantsContainer.closest('.product-item').index();
                 variantsContainer.find('.variant-item').each(function(variantIndex) {
                    $(this).find('.variant-number').text(variantIndex + 1);
                    $(this).find('[name*="[variants]"]').each(function() {
                        const name = $(this).attr('name').replace(/variants\[\d+\]/, `variants[${variantIndex}]`);
                        $(this).attr('name', name);
                    });
                });
            }

            // Ẩn/hiện nút xóa biến thể
            function updateRemoveVariantButtons(variantsContainer) {
                 if (variantsContainer.find('.variant-item').length <= 1) {
                    variantsContainer.find('.remove-variant-btn').hide();
                } else {
                    variantsContainer.find('.remove-variant-btn').show();
                }
            }


            // ===================================
            // SIZE & SỐ LƯỢNG
            // ===================================

            // Thêm size & số lượng
            $(document).on('click', '.add-size-btn', function() {
                const sizeContainer = $(this).prev('.size-quantity-container');
                const productIndex = $(this).closest('.product-item').index();
                const variantIndex = $(this).closest('.variant-item').index();
                const sizeCount = sizeContainer.find('.size-quantity-item').length;

                const newSizeItem = $(`
                    <div class="size-quantity-item row align-items-center mb-2" style="display:none;">
                        <div class="col-md-5">
                             <select class="form-select form-select-lg border-2 size-select" name="products[${productIndex}][variants][${variantIndex}][details][${sizeCount}][size]" required>
                                <option value="">-- Chọn size --</option>
                                <option value="XS">XS</option><option value="S">S</option><option value="M">M</option><option value="L">L</option><option value="XL">XL</option><option value="XXL">XXL</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="number" name="products[${productIndex}][variants][${variantIndex}][details][${sizeCount}][quantity]" class="form-control quantity-input border-2" placeholder="Số lượng" min="1" required>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill remove-size-btn"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                `);

                sizeContainer.append(newSizeItem);
                newSizeItem.slideDown(300);
                updateRemoveSizeButtons(sizeContainer);
            });

             // Xóa size & số lượng
            $(document).on('click', '.remove-size-btn', function() {
                const sizeItem = $(this).closest('.size-quantity-item');
                const sizeContainer = sizeItem.closest('.size-quantity-container');
                sizeItem.slideUp(300, function() {
                    $(this).remove();
                    updateSizeIndexes(sizeContainer);
                    updateRemoveSizeButtons(sizeContainer);
                });
            });

            // Cập nhật lại index của size
            function updateSizeIndexes(sizeContainer) {
                const productIndex = sizeContainer.closest('.product-item').index();
                const variantIndex = sizeContainer.closest('.variant-item').index();

                sizeContainer.find('.size-quantity-item').each(function(sizeIndex) {
                    $(this).find('[name*="[details]"]').each(function() {
                        const name = $(this).attr('name').replace(/details\[\d+\]/, `details[${sizeIndex}]`);
                        $(this).attr('name', name);
                    });
                });
            }

            // Ẩn/hiện nút xóa size
            function updateRemoveSizeButtons(sizeContainer) {
                if (sizeContainer.find('.size-quantity-item').length <= 1) {
                    sizeContainer.find('.remove-size-btn').hide();
                } else {
                    sizeContainer.find('.remove-size-btn').show();
                }
            }


            // ===================================
            // XỬ LÝ CHUNG & VALIDATE
            // ===================================

            // Xử lý preview ảnh
            $(document).on('change', '.product-image', function(e) {
                const file = e.target.files[0];
                const previewImg = $(this).closest('.row').find('.preview-img');
                const validTypes = ["image/jpg", "image/jpeg", "image/png", "image/gif", "image/webp", "image/avif"];
                if (file && validTypes.includes(file.type)) {
                    const reader = new FileReader();
                    reader.onload = (event) => previewImg.attr('src', event.target.result).removeClass('d-none').hide().fadeIn(300);
                    reader.readAsDataURL(file);
                } else {
                    previewImg.fadeOut(300, () => $(this).addClass('d-none'));
                }
            });

            // Validate form trước khi submit
            $('#formCreateInventory').on('submit', function(e) {
                let isValid = true;
                let errorMessages = [];

                // Kiểm tra mỗi sản phẩm
                $('.product-item').each(function(productIndex) {
                    const productItem = $(this);

                    // 1. Phải có ít nhất 1 màu
                    if (productItem.find('.variant-item').length === 0) {
                        isValid = false;
                        errorMessages.push(`Sản phẩm #${productIndex + 1}: Phải có ít nhất 1 màu.`);
                    }

                    const seenColors = new Set();
                    // Kiểm tra mỗi màu (variant)
                    productItem.find('.variant-item').each(function(variantIndex) {
                        const variantItem = $(this);
                        const colorInput = variantItem.find('.color-input');
                        const colorValue = colorInput.val().trim().toLowerCase();

                        // 2. Màu không được để trống và không được trùng
                        if (!colorValue) {
                           isValid = false;
                           colorInput.addClass('is-invalid');
                           errorMessages.push(`Sản phẩm #${productIndex + 1}, Màu #${variantIndex + 1}: Tên màu không được để trống.`);
                        } else if (seenColors.has(colorValue)) {
                           isValid = false;
                           colorInput.addClass('is-invalid');
                           errorMessages.push(`Sản phẩm #${productIndex + 1}: Màu "${colorInput.val()}" bị lặp lại.`);
                        } else {
                           seenColors.add(colorValue);
                           colorInput.removeClass('is-invalid');
                        }

                        // 3. Mỗi màu phải có ít nhất 1 size
                        if (variantItem.find('.size-quantity-item').length === 0) {
                            isValid = false;
                            errorMessages.push(`Sản phẩm #${productIndex + 1}, Màu "${colorInput.val() || '#' + (variantIndex + 1)}": Phải có ít nhất 1 size.`);
                        }

                        const seenSizes = new Set();
                        // Kiểm tra mỗi cặp size-số lượng
                        variantItem.find('.size-quantity-item').each(function(sizeIndex) {
                            const sizeItem = $(this);
                            const sizeSelect = sizeItem.find('.size-select');
                            const sizeValue = sizeSelect.val();

                            // 4. Size không được trùng trong cùng 1 màu
                            if (seenSizes.has(sizeValue)) {
                                isValid = false;
                                sizeSelect.addClass('is-invalid');
                                errorMessages.push(`Sản phẩm #${productIndex + 1}, Màu "${colorInput.val() || '#' + (variantIndex + 1)}": Size "${sizeValue}" bị lặp lại.`);
                            } else if(sizeValue) {
                                seenSizes.add(sizeValue);
                                sizeSelect.removeClass('is-invalid');
                            }
                        });
                    });
                });

                if (!isValid) {
                    e.preventDefault();
                     // Build error string
                    let errorMessage = 'Vui lòng sửa các lỗi sau:\n\n';
                    errorMessages.forEach((msg, index) => {
                        errorMessage += `${index + 1}. ${msg}\n`;
                    });
                    alert(errorMessage);
                }
            });
        });
    </script>

    <script>
        // Color Selection Modal Logic
        $(document).ready(function() {
            const commonColors = [{ name: 'Đỏ', hex: '#e63946' }, { name: 'Xanh dương', hex: '#1d3557' }, { name: 'Xanh lá', hex: '#2a9d8f' }, { name: 'Vàng', hex: '#ffd166' }, { name: 'Đen', hex: '#212529' }, { name: 'Trắng', hex: '#f8f9fa', textColor: '#212529' }, { name: 'Hồng', hex: '#ffafcc' }, { name: 'Tím', hex: '#7b2cbf' }, { name: 'Cam', hex: '#fb8500' }, { name: 'Xám', hex: '#6c757d' }, { name: 'Nâu', hex: '#6d4c41' }, { name: 'Be', hex: '#f5ebe0', textColor: '#6d4c41' } ];
            let currentColorInput = null;
            let selectedColor = null;
            let allColors = [...commonColors];
            const colorPalette = $('#colorPalette');
            commonColors.forEach(color => colorPalette.append(createColorOption(color)));

            $(document).on('click', '.color-input', function() {
                currentColorInput = $(this);
                selectedColor = currentColorInput.val().trim();
                updateSelectedColorDisplay();
                $('#colorSelectionModal').modal('show');
                $('#newColorInput').val('');
                $('#duplicateColorError').hide();
                $('.color-option').removeClass('selected');
                if (selectedColor) {
                    $(`.color-option[data-color="${selectedColor}"]`).addClass('selected');
                }
            });

            $(document).on('click', '.color-option', function() {
                $('.color-option').removeClass('selected');
                $(this).addClass('selected');
                selectedColor = $(this).data('color');
                updateSelectedColorDisplay();
            });

            $('#addColorBtn').click(function() {
                const newColorName = $('#newColorInput').val().trim();
                if (!newColorName) return;
                const isDuplicate = allColors.some(color => color.name.toLowerCase() === newColorName.toLowerCase());
                if (isDuplicate) {
                    $('#duplicateColorError').show();
                    return;
                }
                $('#duplicateColorError').hide();
                const newColor = { name: newColorName, hex: getRandomColor(), isCustom: true };
                allColors.push(newColor);
                colorPalette.append(createColorOption(newColor));
                $('#newColorInput').val('');
                $('.color-option').removeClass('selected');
                $(`.color-option[data-color="${newColor.name}"]`).addClass('selected');
                selectedColor = newColor.name;
                updateSelectedColorDisplay();
            });

            $(document).on('click', '.remove-color', function(e) {
                e.stopPropagation();
                const colorOption = $(this).closest('.color-option');
                const colorName = colorOption.data('color');
                allColors = allColors.filter(color => color.name !== colorName);
                if (selectedColor === colorName) {
                    selectedColor = null;
                    updateSelectedColorDisplay();
                }
                colorOption.remove();
            });

            $('#removeSelectedColor').click(function() {
                selectedColor = null;
                updateSelectedColorDisplay();
                $('.color-option').removeClass('selected');
            });

            $('#confirmColorSelection').click(function() {
                if (currentColorInput) {
                    currentColorInput.val(selectedColor || '').trigger('input'); // Cập nhật và trigger input event
                    $('#colorSelectionModal').modal('hide');
                }
            });

            function createColorOption(color) {
                const textColor = color.textColor || (isLightColor(color.hex) ? '#333' : '#fff');
                return `<div class="color-option" data-color="${color.name}" style="background-color: ${color.hex}; color: ${textColor};" title="${color.name}">${color.name}${color.isCustom ? '<span class="remove-color"><i class="fas fa-times"></i></span>' : ''}</div>`;
            }

            function updateSelectedColorDisplay() {
                const selectedColorBadge = $('#selectedColorBadge');
                const selectedColorText = $('#selectedColorText');
                if (selectedColor) {
                    const colorObj = allColors.find(c => c.name === selectedColor);
                    if (colorObj) {
                        const textColor = colorObj.textColor || (isLightColor(colorObj.hex) ? '#333' : '#fff');
                        selectedColorBadge.removeClass('d-none').css('background-color', colorObj.hex).css('color', textColor);
                        selectedColorText.text(selectedColor);
                        return;
                    }
                }
                selectedColorBadge.addClass('d-none');
            }

            function isLightColor(hex) {
                const r = parseInt(hex.substr(1, 2), 16), g = parseInt(hex.substr(3, 2), 16), b = parseInt(hex.substr(5, 2), 16);
                return (r * 299 + g * 587 + b * 114) / 1000 > 155;
            }
            function getRandomColor() { return '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0'); }
        });
    </script>
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan
@endsection


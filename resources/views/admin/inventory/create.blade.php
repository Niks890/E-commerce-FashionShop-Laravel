@can('warehouse workers')
@extends('admin.master')
@section('title', 'Thêm Phiếu nhập')
@section('back-page')
     <div class="d-flex align-items-center">
        <button class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 shadow-sm back-btn"
                onclick="window.history.back()"
                style="transition: all 0.3s ease; border: 2px solid #007bff;">
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
    <div class="container-fluid">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-gradient-primary text-white rounded-top-4">
                <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Thêm sản phẩm mới</h4>
            </div>
            <div class="card-body p-4">
                <form id="formCreateInventory" method="POST" action="{{ route('inventory.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ auth()->user()->id - 1 }}">

                    <!-- Thông tin cơ bản -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Thông tin cơ bản</h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" name="product_name" class="form-control form-control-lg border-2"
                                   placeholder="Nhập tên sản phẩm..." required>
                            @error('product_name')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Hình ảnh <span class="text-danger">*</span></label>
                            <input type="file" name="image" id="fileInput" class="form-control form-control-lg border-2"
                                   accept="image/*" required>
                            @error('image')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                            <div id="preview" class="mt-3 text-center">
                                <img class="img-thumbnail rounded-3 shadow d-none" id="previewImg" src="" alt=""
                                     style="max-width: 200px; max-height: 200px; object-fit: cover;">
                            </div>
                        </div>
                    </div>

                    <!-- Phân loại và nhà cung cấp -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3"><i class="fas fa-tags me-2"></i>Phân loại sản phẩm</h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg border-2" name="category_id" required>
                                <option value="">-- Chọn danh mục --</option>
                                @foreach ($cats as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                                </div>
                            @enderror
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

                    <!-- Giá và thương hiệu -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3"><i class="fas fa-dollar-sign me-2"></i>Thông tin giá và thương hiệu</h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Giá nhập <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-2">
                                    <i class="fas fa-money-bill-wave text-success"></i>
                                </span>
                                <input type="number" name="price" id="priceInput" class="form-control border-2"
                                       placeholder="0" min="0" step="1000" required>
                                <span class="input-group-text bg-light border-2">VNĐ</span>
                            </div>
                            @error('price')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Thương hiệu</label>
                            <input type="text" name="brand_name" id="brand_name" class="form-control form-control-lg border-2"
                                   placeholder="Nhập tên thương hiệu...">
                            @error('brand_name')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Biến thể sản phẩm -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="text-primary mb-0">
                                    <i class="fas fa-palette me-2"></i>Biến thể sản phẩm
                                    <span class="text-danger">*</span>
                                </h5>
                                <button type="button" id="add-color-btn" class="btn btn-outline-primary btn-sm rounded-pill">
                                    <i class="fas fa-plus me-1"></i>Thêm màu khác
                                </button>
                            </div>

                            <div id="color-variants-container">
                                <div class="color-variant-item mb-3">
                                    <div class="card border-2 border-primary rounded-3 shadow-sm">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-primary">
                                                <i class="fas fa-circle me-2" style="color: #6c757d;"></i>
                                                Màu #<span class="color-number">1</span>
                                            </h6>
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill remove-color-btn" style="display: none;">
                                                <i class="fas fa-trash me-1"></i>Xóa
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label fw-bold">Tên màu <span class="text-danger">*</span></label>
                                                    <input type="text" name="colors[]" class="form-control color-input border-2"
                                                           placeholder="VD: Đỏ, Xanh dương..." required>
                                                    <div class="color-error text-danger mt-1" style="display: none;">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        <small>Màu này đã tồn tại!</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-8 mb-3">
                                                    <label class="form-label fw-bold">Kích cỡ và số lượng <span class="text-danger">*</span></label>
                                                    <select class="form-control size-select border-2" name="sizes[0][]" multiple="multiple">
                                                        <option value="XS">XS</option>
                                                        <option value="S">S</option>
                                                        <option value="M">M</option>
                                                        <option value="L">L</option>
                                                        <option value="XL">XL</option>
                                                        <option value="XXL">XXL</option>
                                                    </select>
                                                    <input type="hidden" name="quantities[0]" class="quantities-input">
                                                    <small class="text-muted mt-1 d-block">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Chọn size và nhập số lượng cho từng size
                                                    </small>
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

    <!-- Modal nhập số lượng -->
    <div class="modal fade" id="modal-quantity" tabindex="-1" aria-labelledby="modal-quantity-label" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header bg-gradient-primary text-white border-0">
                    <h5 class="modal-title fw-bold" id="modal-quantity-label">
                        <i class="fas fa-warehouse me-2"></i>Nhập số lượng
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <i class="fas fa-boxes text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <label for="quantity_variant" class="form-label fw-bold">Số lượng:</label>
                    <input id="quantity_variant" class="form-control form-control-lg text-center border-2"
                           type="number" name="quantity_variant" value="1" min="1" placeholder="Nhập số lượng...">
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-success btn-lg px-4 rounded-pill btn-quantity-submit" data-bs-dismiss="modal">
                        <i class="fas fa-check me-2"></i>Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/plugin/select2/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Xử lý preview ảnh với hiệu ứng
            $("#fileInput").change(function(e) {
                var file = e.target.files[0];
                var validTypes = ["image/jpg", "image/jpeg", "image/png", "image/gif", "image/webp", "image/avif"];

                if (file) {
                    if (validTypes.includes(file.type)) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $("#previewImg").attr("src", e.target.result);
                            $("#previewImg").removeClass('d-none').hide().fadeIn(300);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        $(this).val("");
                        $("#previewImg").fadeOut(300);
                        alert("Vui lòng chọn file ảnh hợp lệ (JPG, PNG, GIF, WebP, AVIF)!");
                    }
                }
            });

            // Biến lưu trữ thông tin size và số lượng
            let colorVariants = {};
            let currentColorIndex = 0;
            let currentSize = null;

            // Khởi tạo Select2 với style đẹp hơn
            function initSelect2(element) {
                element.select2({
                    placeholder: "👕 Chọn kích cỡ...",
                    allowClear: true,
                    width: '100%',
                    templateSelection: function (selection) {
                        const colorIndex = $(selection.element).closest('.color-variant-item').index();
                        if (colorVariants[colorIndex] && colorVariants[colorIndex][selection.id]) {
                            return `${selection.id} (${colorVariants[colorIndex][selection.id]} sp)`;
                        }
                        return selection.text;
                    },
                    templateResult: function (result) {
                        if (!result.id) {
                            return result.text;
                        }
                        return $(`<span><i class="fas fa-tshirt me-2"></i>${result.text}</span>`);
                    }
                });
            }

            initSelect2($('.size-select'));

            // Thêm màu mới với animation
            $('#add-color-btn').click(function() {
                const newIndex = $('.color-variant-item').length;
                const colorNumber = newIndex + 1;

                const newItem = $(`
                    <div class="color-variant-item mb-3" style="display: none;">
                        <div class="card border-2 border-primary rounded-3 shadow-sm">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-circle me-2" style="color: #6c757d;"></i>
                                    Màu #<span class="color-number">${colorNumber}</span>
                                </h6>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill remove-color-btn">
                                    <i class="fas fa-trash me-1"></i>Xóa
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Tên màu <span class="text-danger">*</span></label>
                                        <input type="text" name="colors[]" class="form-control color-input border-2"
                                               placeholder="VD: Đỏ, Xanh dương..." required>
                                        <div class="color-error text-danger mt-1" style="display: none;">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            <small>Màu này đã tồn tại!</small>
                                        </div>
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label fw-bold">Kích cỡ và số lượng <span class="text-danger">*</span></label>
                                        <select class="form-control size-select border-2" name="sizes[${newIndex}][]" multiple="multiple">
                                            <option value="XS">XS</option>
                                            <option value="S">S</option>
                                            <option value="M">M</option>
                                            <option value="L">L</option>
                                            <option value="XL">XL</option>
                                            <option value="XXL">XXL</option>
                                        </select>
                                        <input type="hidden" name="quantities[${newIndex}]" class="quantities-input">
                                        <small class="text-muted mt-1 d-block">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Chọn size và nhập số lượng cho từng size
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                $('#color-variants-container').append(newItem);
                newItem.slideDown(300);

                // Khởi tạo Select2 cho size mới
                initSelect2(newItem.find('.size-select'));

                // Hiển thị nút xóa cho tất cả các màu
                $('.remove-color-btn').show();

                // Update color numbers
                updateColorNumbers();
            });

            // Cập nhật số thứ tự màu
            function updateColorNumbers() {
                $('.color-variant-item').each(function(index) {
                    $(this).find('.color-number').text(index + 1);
                });
            }

            // Validate màu trùng khi nhập (realtime)
            $(document).on('input', '.color-input', function() {
                validateColorDuplicate($(this));
            });

            function validateColorDuplicate(input) {
                const currentVal = input.val().trim().toLowerCase();
                const errorDiv = input.siblings('.color-error');

                if (!currentVal) {
                    errorDiv.hide();
                    input.removeClass('is-invalid');
                    return true;
                }

                let duplicate = false;
                $('.color-input').not(input).each(function() {
                    if ($(this).val().trim().toLowerCase() === currentVal) {
                        duplicate = true;
                        return false;
                    }
                });

                if (duplicate) {
                    errorDiv.show();
                    input.addClass('is-invalid');
                    return false;
                } else {
                    errorDiv.hide();
                    input.removeClass('is-invalid');
                    return true;
                }
            }

            // Xử lý khi chọn size
            $(document).on('select2:select', '.size-select', function(e) {
                const colorIndex = $(this).closest('.color-variant-item').index();
                currentColorIndex = colorIndex;
                currentSize = e.params.data.id;

                if (!colorVariants[colorIndex]) {
                    colorVariants[colorIndex] = {};
                }

                const colorName = $(this).closest('.color-variant-item').find('.color-input').val() || 'Chưa đặt tên';
                $('#modal-quantity-label').html(`<i class="fas fa-warehouse me-2"></i>Size ${currentSize} - Màu: ${colorName}`);
                $('#quantity_variant').val(colorVariants[colorIndex][currentSize] || '1');
                $('#modal-quantity').modal('show');

                // Focus vào input số lượng
                $('#modal-quantity').on('shown.bs.modal', function() {
                    $('#quantity_variant').focus().select();
                });
            });

            // Xử lý khi nhấn OK trong modal
            $('.btn-quantity-submit').click(function() {
                const quantity = $('#quantity_variant').val();

                if (quantity && quantity > 0) {
                    colorVariants[currentColorIndex][currentSize] = quantity;
                    $(`.color-variant-item:eq(${currentColorIndex}) .size-select`).trigger('change');
                    updateQuantitiesInputs();

                    // Hiển thị thông báo thành công
                    showNotification('success', `Đã cập nhật ${quantity} sản phẩm cho size ${currentSize}!`);
                } else {
                    showNotification('error', 'Vui lòng nhập số lượng hợp lệ!');
                    return false;
                }
            });

            // Xóa màu với animation
            $(document).on('click', '.remove-color-btn', function() {
                const item = $(this).closest('.color-variant-item');
                const index = item.index();

                item.slideUp(300, function() {
                    // Xóa dữ liệu trong colorVariants
                    delete colorVariants[index];

                    // Đánh lại index cho các màu còn lại
                    const newColorVariants = {};
                    let newIndex = 0;

                    $('.color-variant-item').each(function(i) {
                        if (i !== index) {
                            newColorVariants[newIndex] = colorVariants[i] || {};
                            newIndex++;
                        }
                    });

                    colorVariants = newColorVariants;
                    $(this).remove();

                    // Cập nhật lại name attribute và index
                    $('.color-variant-item').each(function(i) {
                        $(this).find('.size-select').attr('name', `sizes[${i}][]`);
                        $(this).find('.quantities-input').attr('name', `quantities[${i}]`);
                    });

                    // Ẩn nút xóa nếu chỉ còn 1 màu
                    if ($('.color-variant-item').length === 1) {
                        $('.remove-color-btn').hide();
                    }

                    updateQuantitiesInputs();
                    updateColorNumbers();
                });
            });

            // Validate form trước khi submit
            $('#formCreateInventory').on('submit', function(e) {
                let isValid = true;

                // Validate màu trùng
                $('.color-input').each(function() {
                    if (!validateColorDuplicate($(this))) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    showNotification('error', 'Vui lòng kiểm tra lại các màu bị trùng lặp!');
                    e.preventDefault();
                    return;
                }

                updateQuantitiesInputs();

                // Validate ít nhất 1 màu có size và số lượng
                let hasValidVariant = false;
                for (const colorIndex in colorVariants) {
                    if (Object.keys(colorVariants[colorIndex]).length > 0) {
                        hasValidVariant = true;
                        break;
                    }
                }

                if (!hasValidVariant) {
                    showNotification('error', 'Vui lòng nhập ít nhất một màu với size và số lượng!');
                    e.preventDefault();
                    return;
                }

                // Validate tên màu không được để trống
                let emptyColor = false;
                $('.color-input').each(function() {
                    if (!$(this).val().trim()) {
                        emptyColor = true;
                        $(this).addClass('is-invalid');
                    }
                });

                if (emptyColor) {
                    showNotification('error', 'Vui lòng nhập tên cho tất cả các màu!');
                    e.preventDefault();
                    return;
                }

                showNotification('success', 'Đang lưu sản phẩm...');
            });

            // Hàm cập nhật hidden input
            function updateQuantitiesInputs() {
                $('.quantities-input').each(function() {
                    const colorIndex = $(this).closest('.color-variant-item').index();
                    const quantities = [];

                    if (colorVariants[colorIndex]) {
                        for (const size in colorVariants[colorIndex]) {
                            quantities.push(`${size}-${colorVariants[colorIndex][size]}`);
                        }
                    }

                    $(this).val(quantities.join(','));
                });
            }

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

            // Format giá tiền
            $('#priceInput').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                if (value) {
                    $(this).val(parseInt(value));
                }
            });

            // Enter key trong modal quantity
            $('#quantity_variant').on('keypress', function(e) {
                if (e.which === 13) {
                    $('.btn-quantity-submit').click();
                }
            });
        });
    </script>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }

        .form-control:focus, .form-select:focus {
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

        .color-input.is-invalid {
            border-color: #dc3545;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6.4.4.4-.4'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .select2-container--default .select2-selection--multiple {
            border: 2px solid #dee2e6 !important;
            border-radius: 0.375rem !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #007bff !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
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
@else
    {{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

@extends('admin.master')

@section('content')
<div class="container-fluid">
    <form method="POST" action="{{ route('inventory.post_add_extra') }}" id="addExtraForm">
        @csrf
        <input type="hidden" name="inventory_id" value="{{ $originalInventory->id }}">

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5>Nhập thêm cho phiếu: #{{ $originalInventory->id }}</h5>
                <p class="mb-0">Nhà cung cấp: {{ $originalInventory->provider->name }}</p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="productSearch" class="form-control" placeholder="Tìm kiếm sản phẩm...">
                </div>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                    <i class="fas fa-plus me-2"></i>Chọn sản phẩm
                </button>
            </div>
        </div>

        <!-- Selected Products List -->
        <div class="selected-products-container card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Sản phẩm sẽ nhập thêm</h5>
            </div>
            <div class="card-body" id="selectedProductsList">
                <p class="text-muted mb-0">Chưa có sản phẩm nào được chọn</p>
            </div>
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <span>Tổng giá trị:</span>
                    <strong id="totalValue">0 ₫</strong>
                </div>
            </div>
        </div>

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                <i class="fas fa-save me-2"></i>Xác nhận nhập thêm
            </button>
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Hủy bỏ
            </a>
        </div>
    </form>
</div>

<!-- Product Selection Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Chọn sản phẩm nhập thêm</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row" id="productListContainer">
                    @foreach($products as $product)
                    <div class="col-md-6 mb-3 product-item" data-id="{{ $product->id }}">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="form-check flex-grow-1">
                                        <input class="form-check-input product-checkbox" type="checkbox"
                                               id="product-{{ $product->id }}"
                                               data-name="{{ $product->name }}"
                                               data-code="{{ $product->code }}">
                                        <label class="form-check-label fw-bold" for="product-{{ $product->id }}">
                                            {{ $product->name }} <small class="text-muted">({{ $product->code }})</small>
                                        </label>
                                    </div>
                                    <div class="ps-3">
                                        @if($product->image)
                                        <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}"
                                             class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                        <div class="no-image bg-light d-flex align-items-center justify-content-center"
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Product Variants -->
                                <div class="variants-container mt-3 ms-4" style="display: none;">
                                    @foreach($product->productVariants->groupBy('color') as $color => $variants)
                                    <div class="variant-item mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input variant-checkbox" type="checkbox"
                                                   data-color="{{ $color }}"
                                                   data-product-id="{{ $product->id }}">
                                            <label class="form-check-label d-flex align-items-center">
                                                <span class="color-indicator me-2"
                                                      style="background-color: {{ $color }}; width: 16px; height: 16px; border-radius: 3px;"></span>
                                                {{ $color }}
                                            </label>
                                        </div>

                                        <!-- Sizes for each color -->
                                        <div class="sizes-container ms-4 mt-2" style="display: none;">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Size</th>
                                                            <th>Tồn kho</th>
                                                            <th>Số lượng nhập thêm</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($product->productVariants->groupBy('size') as $size => $items)
                                                        <tr>
                                                            <td>{{ $size }}</td>
                                                            <td>{{ $items->sum('stock') }}</td>
                                                            <td>
                                                                <input type="number" class="form-control form-control-sm quantity-input"
                                                                       min="1" value="0"
                                                                       data-product-id="{{ $product->id }}"
                                                                       data-color="{{ $color }}"
                                                                       data-size="{{ $size }}">
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="confirmSelection">
                    <i class="fas fa-check me-2"></i>Xác nhận chọn
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Templates -->
<template id="selectedProductTemplate">
    <div class="selected-product mb-3 p-3 border rounded" data-product-id="">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
                <img src="" class="product-image me-2" style="width: 40px; height: 40px; object-fit: cover;">
                <div>
                    <h6 class="mb-0 product-name fw-bold"></h6>
                    <small class="text-muted product-code"></small>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-danger remove-product">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="variants-list mt-2">
            <!-- Variants will be added here -->
        </div>

        <div class="row mt-3">
            <div class="col-md-4">
                <label class="form-label">Giá nhập (₫)</label>
                <input type="number" class="form-control price-input" name="selected_products[][price]" min="1" required>
                <input type="hidden" name="selected_products[][product_id]" class="product-id-input">
            </div>
            <div class="col-md-4">
                <label class="form-label">Thành tiền</label>
                <div class="form-control-plaintext product-total">0 ₫</div>
            </div>
        </div>
    </div>
</template>

<template id="variantTemplate">
    <div class="variant-item mb-2 p-2 bg-light rounded" data-color="">
        <div class="d-flex align-items-center mb-2">
            <span class="color-indicator me-2"
                  style="width: 14px; height: 14px; border-radius: 2px;"></span>
            <span class="variant-color fw-bold"></span>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0 sizes-table">
                <thead class="table-light">
                    <tr>
                        <th>Size</th>
                        <th>Số lượng</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Sizes will be added here -->
                </tbody>
            </table>
        </div>
    </div>
</template>

<template id="sizeTemplate">
    <tr>
        <td class="size-name align-middle"></td>
        <td class="size-quantity align-middle"></td>
    </tr>
</template>

@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
    <style>
        .color-indicator {
    display: inline-block;
    width: 16px;
    height: 16px;
    border-radius: 3px;
    border: 1px solid #dee2e6;
}

.selected-product {
    background-color: #f8f9fa;
    transition: all 0.2s;
}

.selected-product:hover {
    background-color: #e9ecef;
}

.variant-item {
    background-color: white;
}

.no-image {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #dee2e6;
    border-radius: 4px;
}

.quantity-input {
    max-width: 100px;
}

.price-input {
    max-width: 150px;
}
    </style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Toggle variants when product is selected
    $('.product-checkbox').change(function() {
        const productItem = $(this).closest('.product-item');
        const variantsContainer = productItem.find('.variants-container');

        if ($(this).is(':checked')) {
            variantsContainer.slideDown();
        } else {
            variantsContainer.slideUp();
            productItem.find('.variant-checkbox').prop('checked', false).trigger('change');
        }
    });

    // Toggle sizes when variant is selected
    $(document).on('change', '.variant-checkbox', function() {
        const sizesContainer = $(this).closest('.variant-item').find('.sizes-container');

        if ($(this).is(':checked')) {
            sizesContainer.slideDown();
        } else {
            sizesContainer.slideUp();
            sizesContainer.find('.quantity-input').val(0);
        }
    });

    // Confirm product selection
    $('#confirmSelection').click(function() {
        const selectedProducts = [];

        $('.product-checkbox:checked').each(function() {
            const productId = $(this).closest('.product-item').data('id');
            const productName = $(this).data('name');
            const productCode = $(this).data('code');
            const productImage = $(this).closest('.product-item').find('img').attr('src') || '';
            const variants = [];

            $(this).closest('.product-item').find('.variant-checkbox:checked').each(function() {
                const color = $(this).data('color');
                const sizes = [];

                $(this).closest('.variant-item').find('.quantity-input').each(function() {
                    const size = $(this).data('size');
                    const quantity = parseInt($(this).val()) || 0;

                    if (quantity > 0) {
                        sizes.push({
                            size: size,
                            quantity: quantity
                        });
                    }
                });

                if (sizes.length > 0) {
                    variants.push({
                        color: color,
                        sizes: sizes
                    });
                }
            });

            if (variants.length > 0) {
                selectedProducts.push({
                    product_id: productId,
                    name: productName,
                    code: productCode,
                    image: productImage,
                    variants: variants
                });
            }
        });

        if (selectedProducts.length > 0) {
            renderSelectedProducts(selectedProducts);
            $('#submitBtn').prop('disabled', false);
            $('#productModal').modal('hide');
            calculateTotal();
        } else {
            alert('Vui lòng chọn ít nhất một sản phẩm và số lượng nhập thêm');
        }
    });

    // Render selected products
    function renderSelectedProducts(products) {
        const $container = $('#selectedProductsList');
        $container.empty();

        if (products.length === 0) {
            $container.html('<p class="text-muted">Chưa có sản phẩm nào được chọn</p>');
            $('#submitBtn').prop('disabled', true);
            $('#totalValue').text('0 ₫');
            return;
        }

        products.forEach((product, productIndex) => {
            const $template = $('#selectedProductTemplate').html();
            const $productElement = $($template);

            $productElement.attr('data-product-id', product.product_id);
            $productElement.find('.product-name').text(product.name);
            $productElement.find('.product-code').text(product.code);
            $productElement.find('.product-image').attr('src', product.image || '');
            $productElement.find('.product-id-input').attr('name', `selected_products[${productIndex}][product_id]`).val(product.product_id);
            $productElement.find('.price-input').attr('name', `selected_products[${productIndex}][price]`);

            const $variantsList = $productElement.find('.variants-list');

            product.variants.forEach((variant, variantIndex) => {
                const $variantTemplate = $('#variantTemplate').html();
                const $variantElement = $($variantTemplate);

                $variantElement.attr('data-color', variant.color);
                $variantElement.find('.variant-color').text(variant.color);
                $variantElement.find('.color-indicator').css('background-color', variant.color);

                // Add hidden input for variant
                $variantElement.append(`<input type="hidden" name="selected_products[${productIndex}][variants][${variantIndex}][color]" value="${variant.color}">`);

                const $sizesTable = $variantElement.find('.sizes-table tbody');
                let variantTotalQty = 0;

                variant.sizes.forEach((size, sizeIndex) => {
                    const $sizeTemplate = $('#sizeTemplate').html();
                    const $sizeElement = $($sizeTemplate);

                    $sizeElement.find('.size-name').text(size.size);
                    $sizeElement.find('.size-quantity').text(size.quantity);

                    // Add hidden inputs for size
                    $sizeElement.append(`
                        <input type="hidden" name="selected_products[${productIndex}][variants][${variantIndex}][sizes][${sizeIndex}][size]" value="${size.size}">
                        <input type="hidden" name="selected_products[${productIndex}][variants][${variantIndex}][sizes][${sizeIndex}][quantity]" value="${size.quantity}">
                    `);

                    $sizesTable.append($sizeElement);
                    variantTotalQty += parseInt(size.quantity);
                });

                $variantsList.append($variantElement);
            });

            // Handle price change
            $productElement.find('.price-input').on('input', function() {
                const price = parseFloat($(this).val()) || 0;
                const totalQty = product.variants.reduce((sum, variant) => {
                    return sum + variant.sizes.reduce((sum, size) => sum + parseInt(size.quantity), 0);
                }, 0);

                $productElement.find('.product-total').text((price * totalQty).toLocaleString('vi-VN') + ' ₫');
                calculateTotal();
            });

            // Handle product removal
            $productElement.find('.remove-product').click(function() {
                $(this).closest('.selected-product').remove();
                if ($container.find('.selected-product').length === 0) {
                    $container.html('<p class="text-muted">Chưa có sản phẩm nào được chọn</p>');
                    $('#submitBtn').prop('disabled', true);
                }
                calculateTotal();
            });

            $container.append($productElement);
        });
    }

    // Calculate total value
    function calculateTotal() {
        let total = 0;

        $('.selected-product').each(function() {
            const price = parseFloat($(this).find('.price-input').val()) || 0;
            const qty = $(this).find('.size-quantity').toArray().reduce((sum, el) => {
                return sum + parseInt($(el).text());
            }, 0);

            total += price * qty;
        });

        $('#totalValue').text(total.toLocaleString('vi-VN') + ' ₫');
    }

    // Product search
    $('#productSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();

        if (searchTerm.length === 0) {
            $('.product-item').show();
            return;
        }

        $('.product-item').each(function() {
            const productName = $(this).find('.form-check-label').text().toLowerCase();
            const productCode = $(this).find('.form-check-label').data('code').toLowerCase();

            if (productName.includes(searchTerm) || productCode.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
</script>
@endsection

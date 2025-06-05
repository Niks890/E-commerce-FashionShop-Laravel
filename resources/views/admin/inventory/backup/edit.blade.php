@can('warehouse workers')
    @extends('admin.master')
    @section('title', 'Chỉnh sửa phiếu nhập')
@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa phiếu nhập #{{ $inventory->id }}</h1>
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </a>
        </div>

        {{-- Alert messages --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- <form action="{{ route('inventory.update', $inventory->id) }}" method="POST" enctype="multipart/form-data" id="edit-inventory-form"> --}}
            @csrf
            @method('PUT')

            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Thông tin phiếu nhập</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="provider_id" class="form-label">Nhà cung cấp <span class="text-danger">*</span></label>
                                <select name="provider_id" id="provider_id" class="form-select" required>
                                    <option value="">Chọn nhà cung cấp</option>
                                    @foreach($providers as $provider)
                                        <option value="{{ $provider->id }}" {{ $inventory->provider_id == $provider->id ? 'selected' : '' }}>
                                            {{ $provider->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Trạng thái</label>
                                <div class="form-control-plaintext">
                                    <span class="badge bg-warning">{{ ucfirst($inventory->status) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Products Section --}}
            <div class="card shadow mt-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-box me-2"></i>Danh sách sản phẩm</h5>
                    <button type="button" class="btn btn-light btn-sm" id="add-product-btn">
                        <i class="fas fa-plus me-1"></i>Thêm sản phẩm
                    </button>
                </div>
                <div class="card-body">
                    <div id="products-container">
                        @foreach($inventory->inventoryDetails->groupBy('product_id') as $productId => $details)
                            @php
                                $product = $details->first()->product;
                                $variants = $details;
                            @endphp
                            <div class="product-item border rounded p-3 mb-3" data-product-index="{{ $loop->index }}">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="text-primary mb-0">Sản phẩm {{ $loop->iteration }}</h6>
                                    <button type="button" class="btn btn-danger btn-sm remove-product-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <input type="hidden" name="products[{{ $loop->index }}][id]" value="{{ $product->id }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                            <input type="text" name="products[{{ $loop->index }}][product_name]"
                                                   class="form-control" value="{{ $product->product_name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Thương hiệu <span class="text-danger">*</span></label>
                                            <input type="text" name="products[{{ $loop->index }}][brand_name]"
                                                   class="form-control" value="{{ $product->brand }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                                            <select name="products[{{ $loop->index }}][category_id]" class="form-select" required>
                                                @foreach($cats as $cat)
                                                    <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Giá nhập <span class="text-danger">*</span></label>
                                            <input type="number" name="products[{{ $loop->index }}][price]"
                                                   class="form-control" value="{{ $product->price }}" min="1" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Hình ảnh hiện tại</label>
                                            <div>
                                                <img src="{{ $product->image }}" alt="Current image"
                                                     class="img-thumbnail" style="max-width: 100px;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Thay đổi hình ảnh</label>
                                            <input type="file" name="products[{{ $loop->index }}][image]"
                                                   class="form-control" accept="image/*">
                                            <small class="text-muted">Để trống nếu không muốn thay đổi</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Variants Section --}}
                                <div class="variants-section mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="text-info mb-0">Biến thể sản phẩm</h6>
                                        <button type="button" class="btn btn-info btn-sm add-variant-btn">
                                            <i class="fas fa-plus me-1"></i>Thêm biến thể
                                        </button>
                                    </div>

                                    <div class="variants-container">
                                        @foreach($variants as $variantIndex => $detail)
                                            <div class="variant-item border rounded p-2 mb-2" data-variant-index="{{ $variantIndex }}">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <small class="text-muted">Biến thể {{ $variantIndex + 1 }}</small>
                                                    <button type="button" class="btn btn-danger btn-sm remove-variant-btn">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>

                                                <input type="hidden" name="products[{{ $loop->parent->index }}][variants][{{ $variantIndex }}][id]"
                                                       value="{{ $detail->productVariant->id }}">

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="text" name="products[{{ $loop->parent->index }}][variants][{{ $variantIndex }}][color]"
                                                               class="form-control" placeholder="Màu sắc"
                                                               value="{{ $detail->productVariant->color }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select name="products[{{ $loop->parent->index }}][variants][{{ $variantIndex }}][size]"
                                                                class="form-select" required>
                                                            <option value="">Chọn size</option>
                                                            @foreach(['S', 'M', 'L', 'XL', 'XXL'] as $size)
                                                                <option value="{{ $size }}" {{ $detail->productVariant->size == $size ? 'selected' : '' }}>
                                                                    {{ $size }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" name="products[{{ $loop->parent->index }}][variants][{{ $variantIndex }}][quantity]"
                                                               class="form-control" placeholder="Số lượng"
                                                               value="{{ $detail->quantity }}" min="1" required>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Hidden fields for deleted items --}}
            <input type="hidden" name="deleted_products" id="deleted-products" value="">
            <input type="hidden" name="deleted_variants" id="deleted-variants" value="">

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-1"></i>Cập nhật phiếu nhập
                </button>
                {{-- <a href="{{ route('inventory.show', $inventory) }}" class="btn btn-secondary btn-lg ms-2">
                    <i class="fas fa-times me-1"></i>Hủy
                </a> --}}
            </div>
        </form>
    </div>

    {{-- Template for new product --}}
    <div id="product-template" style="display: none;">
        <div class="product-item border rounded p-3 mb-3" data-product-index="__PRODUCT_INDEX__">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="text-primary mb-0">Sản phẩm mới</h6>
                <button type="button" class="btn btn-danger btn-sm remove-product-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" name="products[__PRODUCT_INDEX__][product_name]" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Thương hiệu <span class="text-danger">*</span></label>
                        <input type="text" name="products[__PRODUCT_INDEX__][brand_name]" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                        <select name="products[__PRODUCT_INDEX__][category_id]" class="form-select" required>
                            <option value="">Chọn danh mục</option>
                            @foreach($cats as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Giá nhập <span class="text-danger">*</span></label>
                        <input type="number" name="products[__PRODUCT_INDEX__][price]" class="form-control" min="1" required>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Hình ảnh <span class="text-danger">*</span></label>
                        <input type="file" name="products[__PRODUCT_INDEX__][image]" class="form-control" accept="image/*" required>
                    </div>
                </div>
            </div>

            <div class="variants-section mt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-info mb-0">Biến thể sản phẩm</h6>
                    <button type="button" class="btn btn-info btn-sm add-variant-btn">
                        <i class="fas fa-plus me-1"></i>Thêm biến thể
                    </button>
                </div>

                <div class="variants-container">
                    <div class="variant-item border rounded p-2 mb-2" data-variant-index="0">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Biến thể 1</small>
                            <button type="button" class="btn btn-danger btn-sm remove-variant-btn">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="products[__PRODUCT_INDEX__][variants][0][color]"
                                       class="form-control" placeholder="Màu sắc" required>
                            </div>
                            <div class="col-md-4">
                                <select name="products[__PRODUCT_INDEX__][variants][0][size]" class="form-select" required>
                                    <option value="">Chọn size</option>
                                    @foreach(['S', 'M', 'L', 'XL', 'XXL'] as $size)
                                        <option value="{{ $size }}">{{ $size }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="products[__PRODUCT_INDEX__][variants][0][quantity]"
                                       class="form-control" placeholder="Số lượng" min="1" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Template for new variant --}}
    <div id="variant-template" style="display: none;">
        <div class="variant-item border rounded p-2 mb-2" data-variant-index="__VARIANT_INDEX__">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <small class="text-muted">Biến thể __VARIANT_NUMBER__</small>
                <button type="button" class="btn btn-danger btn-sm remove-variant-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="products[__PRODUCT_INDEX__][variants][__VARIANT_INDEX__][color]"
                           class="form-control" placeholder="Màu sắc" required>
                </div>
                <div class="col-md-4">
                    <select name="products[__PRODUCT_INDEX__][variants][__VARIANT_INDEX__][size]" class="form-select" required>
                        <option value="">Chọn size</option>
                        @foreach(['S', 'M', 'L', 'XL', 'XXL'] as $size)
                            <option value="{{ $size }}">{{ $size }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" name="products[__PRODUCT_INDEX__][variants][__VARIANT_INDEX__][quantity]"
                           class="form-control" placeholder="Số lượng" min="1" required>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    let productIndex = {{ $inventory->inventoryDetails->groupBy('product_id')->count() }};
    let deletedProducts = [];
    let deletedVariants = [];

    // Add new product
    $('#add-product-btn').click(function() {
        let template = $('#product-template').html();
        template = template.replace(/__PRODUCT_INDEX__/g, productIndex);
        $('#products-container').append(template);
        productIndex++;
    });

    // Remove product
    $(document).on('click', '.remove-product-btn', function() {
        let productItem = $(this).closest('.product-item');
        let productId = productItem.find('input[name*="[id]"]').val();

        if (productId) {
            deletedProducts.push(productId);
            $('#deleted-products').val(JSON.stringify(deletedProducts));
        }

        productItem.remove();
    });

    // Add new variant
    $(document).on('click', '.add-variant-btn', function() {
        let productItem = $(this).closest('.product-item');
        let productIdx = productItem.data('product-index');
        let variantsContainer = productItem.find('.variants-container');
        let variantCount = variantsContainer.find('.variant-item').length;

        let template = $('#variant-template').html();
        template = template.replace(/__PRODUCT_INDEX__/g, productIdx);
        template = template.replace(/__VARIANT_INDEX__/g, variantCount);
        template = template.replace(/__VARIANT_NUMBER__/g, variantCount + 1);

        variantsContainer.append(template);
    });

    // Remove variant
    $(document).on('click', '.remove-variant-btn', function() {
        let variantItem = $(this).closest('.variant-item');
        let variantId = variantItem.find('input[name*="[id]"]').val();

        if (variantId) {
            deletedVariants.push(variantId);
            $('#deleted-variants').val(JSON.stringify(deletedVariants));
        }

        variantItem.remove();
    });

    // Form validation
    $('#edit-inventory-form').submit(function(e) {
        let hasProducts = $('#products-container .product-item').length > 0;
        if (!hasProducts) {
            e.preventDefault();
            alert('Vui lòng thêm ít nhất một sản phẩm!');
            return false;
        }

        // Check each product has at least one variant
        let allProductsHaveVariants = true;
        $('#products-container .product-item').each(function() {
            let variantCount = $(this).find('.variant-item').length;
            if (variantCount === 0) {
                allProductsHaveVariants = false;
                return false;
            }
        });

        if (!allProductsHaveVariants) {
            e.preventDefault();
            alert('Mỗi sản phẩm phải có ít nhất một biến thể!');
            return false;
        }
    });
});
</script>
@endsection
@endcan

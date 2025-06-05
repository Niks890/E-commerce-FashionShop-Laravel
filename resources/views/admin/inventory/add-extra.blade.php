@can('warehouse workers')
    @extends('admin.master')
    @section('title', 'Tạo phiếu nhập thêm sản phẩm đã có trong kho')
@section('back-page')
    <div class="d-flex align-items-center mb-3">
        <button class="btn btn-outline-primary rounded-pill px-3 py-2 shadow-sm" onclick="window.history.back()"
            style="transition: all 0.3s ease; border: 2px solid #007bff;">
            <i class="fas fa-arrow-left me-2"></i>
            <span class="fw-semibold">Quay lại</span>
        </button>
    </div>
@endsection
@section('content')
    <form id="formCreateInventory" method="POST" action="{{ route('inventory.post_add_extra') }}"
        enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ auth()->user()->id - 1 }}">

        <div class="card shadow-lg mb-4 rounded-3">
            <div class="card-header bg-gradient-primary text-white py-3 rounded-top-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-truck me-2"></i>Thông tin nhà cung cấp</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="provider_name_display" class="form-label fw-bold">Nhà cung cấp:</label>
                    <input type="text" id="provider_name_display" class="form-control form-control-lg" readonly
                        placeholder="Tên nhà cung cấp">
                    <input type="hidden" name="provider_id" id="provider_id_hidden">
                </div>
            </div>
        </div>

        {{-- Product details will be loaded here by JavaScript --}}
        <div id="products_container" class="card shadow-lg mb-4 rounded-3">
            <div class="card-header bg-gradient-primary text-white py-3 rounded-top-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-boxes me-2"></i>Thông tin sản phẩm trong phiếu nhập</h5>
            </div>
            <div class="card-body">
                <div class="btn-group-bulk mb-4">
                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" id="selectAllProductsBtn">
                        <i class="fas fa-check-double me-2"></i>Chọn tất cả
                    </button>
                    <button type="button" class="btn btn-secondary rounded-pill px-4 shadow-sm"
                        id="deselectAllProductsBtn">
                        <i class="fas fa-times me-2"></i>Bỏ chọn tất cả
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" width="60px" class="text-center">Chọn</th>
                                <th scope="col">Sản phẩm</th>
                                <th scope="col" width="100px">Hình ảnh</th>
                                <th scope="col">Danh mục</th>
                                <th scope="col">Thương hiệu</th>
                                <th scope="col">Thông tin biến thể hiện có</th>
                                <th scope="col">Giá nhập thêm (VND)</th>
                                <th scope="col">Màu mới</th>
                                <th scope="col" width="200px">Kích cỡ & Số lượng nhập thêm</th>
                            </tr>
                        </thead>
                        <tbody id="products-tbody">
                            {{-- Product rows will be inserted here by JavaScript --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success rounded-pill px-5 py-3 shadow-lg mt-3 fw-bold">
            <i class="fas fa-save me-2"></i>Lưu thông tin nhập kho
        </button>
    </form>

    <div class="modal fade" id="modal-quantity" tabindex="-1" aria-labelledby="modal-quantity-label" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header bg-primary text-white text-center rounded-top-4">
                    <h5 class="modal-title fw-bold" id="modal-quantity-label">Nhập số lượng</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="quantity_variant" class="form-label fw-semibold">Số lượng:</label>
                        <input id="quantity_variant" class="form-control form-control-lg" type="number"
                            name="quantity_variant" value="1" min="1">
                    </div>
                </div>
                <div class="modal-footer justify-content-center border-top-0 pt-0">
                    <button type="button" class="btn btn-success text-white btn-quantity-submit rounded-pill px-4 py-2">
                        <i class="fas fa-check me-2"></i>Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
            /* Light background for the page */
        }

        .card {
            border: none;
            /* Remove default card border */
        }

        .card-header.bg-gradient-primary {
            background: linear-gradient(45deg, #007bff, #0056b3) !important;
            /* Blue gradient */
            border-bottom: none;
        }

        .btn-outline-primary {
            border-color: #007bff;
            color: #007bff;
        }

        .btn-outline-primary:hover {
            background-color: #007bff;
            color: white;
        }

        .btn-group-bulk button {
            transition: all 0.3s ease;
        }

        .btn-group-bulk button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 123, 255, 0.2);
        }

        .table thead th {
            background-color: #e9eff4;
            /* Lighter header background */
            color: #343a40;
            /* Darker text */
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody tr:hover {
            background-color: #f1f5f9;
        }

        .product-check {
            transform: scale(1.3);
            /* Slightly larger checkboxes */
            cursor: pointer;
        }

        .img-thumbnail {
            border: 1px solid #dee2e6;
            padding: 2px;
        }

        .variant-table {
            width: 100%;
            margin-top: 5px;
            border-collapse: collapse;
            font-size: 0.85rem;
            /* Slightly smaller font for inner table */
        }

        .variant-table th,
        .variant-table td {
            border: 1px solid #e9ecef;
            padding: 6px 8px;
            text-align: left;
        }

        .variant-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #555;
        }

        .variant-table td small {
            color: #6c757d;
        }

        .badge.bg-secondary {
            background-color: #6c757d !important;
            font-size: 0.75em;
            padding: 0.3em 0.6em;
            vertical-align: middle;
        }

        .form-control-lg {
            height: calc(3.5rem + 2px);
            /* Larger input fields */
            padding: 0.75rem 1rem;
            font-size: 1.1rem;
            border-radius: 0.3rem;
        }

        /* Select2 Customizations for larger appearance */
        .select2-container--bootstrap-5 .select2-selection--multiple {
            min-height: calc(3.5rem + 2px) !important;
            /* Match form-control-lg height */
            padding: 0.75rem 1rem !important;
            border-radius: 0.3rem !important;
            font-size: 1.1rem !important;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: #0d6efd;
            color: white;
            border: 1px solid #0a58ca;
            border-radius: 0.25rem;
            padding: 0.4rem 0.75rem;
            /* Larger padding for choices */
            font-size: 0.95rem;
            /* Slightly larger text in choices */
            margin-top: 0.3rem;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            margin-right: 0.25rem;
            font-size: 1.1rem;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
            padding-right: 2rem;
            /* Give space for the clear button */
        }

        .modal-header.bg-primary {
            background-color: #007bff !important;
        }

        .modal-footer {
            background-color: #f8f9fa;
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const inventory_id = urlParams.get('inventory_id');

            if (!inventory_id) {
                console.error("Không tìm thấy inventory_id trong URL.");
                alert("Không tìm thấy ID phiếu nhập. Vui lòng thử lại.");
                window.history.back();
                return;
            }

            let productsData = [];

            fetch(`http://127.0.0.1:8000/api/inventory/${inventory_id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status_code === 200) {
                        const inventoryData = data.data;

                        document.getElementById('provider_name_display').value = inventoryData.provider.name || "";
                        document.getElementById('provider_id_hidden').value = inventoryData.provider.id || "";

                        const groupedProducts = {};
                        inventoryData.detail.forEach(item => {
                            if (!groupedProducts[item.product.id]) {
                                groupedProducts[item.product.id] = {
                                    product: item.product,
                                    variants: []
                                };
                            }
                            groupedProducts[item.product.id].variants.push({
                                color: item.color,
                                sizes: item.sizes,
                                quantity: item.quantity,
                                price: item.price
                            });
                        });

                        const uniqueProducts = Object.values(groupedProducts);

                        productsData = uniqueProducts.map((item, index) => ({
                            product_id: item.product.id,
                            product_name: item.product.name,
                            product_image: item.product.image,
                            category_name: item.product.category.name,
                            brand_name: item.product.brand,
                            existing_variants: item.variants,
                            new_color: item.variants.length > 0 ? item.variants[0].color :
                                '',
                            new_price: '',
                            new_sizes_quantities: {}
                        }));

                        const productsTbody = document.getElementById('products-tbody');
                        productsTbody.innerHTML = '';

                        productsData.forEach((productItem, index) => {
                            const row = document.createElement('tr');
                            row.id = `product-row-${index}`;
                            row.innerHTML = `
                                <td class="text-center">
                                    <input type="checkbox" class="product-check form-check-input" data-index="${index}" checked>
                                    <input type="hidden" name="products[${index}][product_id]" value="${productItem.product_id}">
                                </td>
                                <td>
                                    <strong>${productItem.product_name}</strong>
                                </td>
                                <td>
                                    <img src="${productItem.product_image}" width="70" height="70" class="img-thumbnail rounded" alt="${productItem.product_name}">
                                </td>
                                <td>${productItem.category_name}</td>
                                <td>${productItem.brand_name}</td>
                                <td>
                                    <table class="variant-table">
                                        <thead>
                                            <tr>
                                                <th>Kích cỡ</th>
                                                <th>SL</th>
                                                <th>Giá</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${productItem.existing_variants.map(variant => `
                                                <tr>
                                                    <td>${variant.sizes.split(',').map(s => `<span class="badge bg-secondary me-1">${s.trim()}</span>`).join('')}</td>
                                                    <td>${variant.quantity}</td>
                                                    <td>${formatCurrency(variant.price)}</td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </td>
                                <td>
                                    <input type="number" name="products[${index}][new_price]" class="form-control new-price-input" data-index="${index}" placeholder="Nhập giá mới" min="0">
                                </td>
                                <td>
                                    <input type="text" name="products[${index}][new_color]" class="form-control new-color-input" data-index="${index}" placeholder="Nhập màu mới" value="${productItem.new_color || ''}">
                                </td>
                                <td>
                                    <select class="form-select select-sizes" name="products[${index}][new_sizes][]" multiple="multiple" data-index="${index}">
                                        <option value="XS">XS</option>
                                        <option value="S">S</option>
                                        <option value="M">M</option>
                                        <option value="L">L</option>
                                        <option value="XL">XL</option>
                                        <option value="XXL">XXL</option>
                                    </select>
                                    <input type="hidden" name="products[${index}][formatted_new_sizes]" class="formatted-new-sizes" data-index="${index}">
                                </td>
                            `;
                            productsTbody.appendChild(row);
                        });

                        document.getElementById('selectAllProductsBtn').addEventListener('click', function() {
                            document.querySelectorAll('.product-check').forEach(checkbox => {
                                checkbox.checked = true;
                                const index = checkbox.dataset.index;
                                $(`#product-row-${index} input, #product-row-${index} select`)
                                    .prop('disabled', false);
                                $(`#product-row-${index} .select-sizes`).select2('enable');
                            });
                        });

                        document.getElementById('deselectAllProductsBtn').addEventListener('click', function() {
                            document.querySelectorAll('.product-check').forEach(checkbox => {
                                checkbox.checked = false;
                                const index = checkbox.dataset.index;
                                $(`#product-row-${index} input:not([type="hidden"]), #product-row-${index} select`)
                                    .prop('disabled', true);
                                $(`#product-row-${index} .select-sizes`).select2('disable');
                            });
                        });

                        document.querySelectorAll('.product-check').forEach(checkbox => {
                            checkbox.addEventListener('change', function() {
                                const index = this.dataset.index;
                                const isDisabled = !this.checked;
                                $(`#product-row-${index} input:not([type="hidden"]), #product-row-${index} select`)
                                    .prop('disabled', isDisabled);
                                $(`#product-row-${index} .select-sizes`).select2(isDisabled ?
                                    'disable' : 'enable');
                            });
                        });

                        $('.select-sizes').each(function() {
                            const selectElement = $(this);
                            const index = selectElement.data('index');
                            if (!productsData[index]) {
                                productsData[index] = {
                                    new_sizes_quantities: {},
                                    new_color: ''
                                };
                            }
                            let sizesWithQuantities = productsData[index].new_sizes_quantities;

                            selectElement.select2({
                                theme: "bootstrap-5",
                                placeholder: "Chọn kích cỡ và số lượng",
                                allowClear: true,
                                templateSelection: function(selection) {
                                    if (sizesWithQuantities[selection.id]) {
                                        return `${selection.id} (${sizesWithQuantities[selection.id]})`;
                                    }
                                    return selection.text;
                                }
                            });

                            selectElement.on("select2:select", function(e) {
                                const selectedSize = e.params.data.id;
                                $('#modal-quantity-label').text("Nhập số lượng cho size " +
                                    selectedSize + " của sản phẩm " + productsData[index]
                                    .product_name);
                                $("#quantity_variant").val(sizesWithQuantities[selectedSize] ||
                                    1);
                                $("#modal-quantity").modal("show");

                                $('#modal-quantity').data('product-index', index);
                                $('#modal-quantity').data('selected-size', selectedSize);
                            });
                        });

                        $(".btn-quantity-submit").on("click", function() {
                            const quantity = parseInt($("#quantity_variant").val()) || 0;
                            const productIndex = $('#modal-quantity').data('product-index');
                            const selectedSize = $('#modal-quantity').data('selected-size');

                            if (quantity > 0) {
                                productsData[productIndex].new_sizes_quantities[selectedSize] =
                                    quantity;

                                if (!productsData[productIndex].new_color && productsData[productIndex]
                                    .existing_variants.length > 0) {
                                    productsData[productIndex].new_color = productsData[productIndex]
                                        .existing_variants[0].color;
                                    $(`#product-row-${productIndex} .new-color-input`).val(productsData[
                                        productIndex].new_color);
                                }

                                const formattedSizesInput = $(
                                    `#product-row-${productIndex} .formatted-new-sizes`);
                                formattedSizesInput.val(
                                    Object.entries(productsData[productIndex].new_sizes_quantities)
                                    .map(([size, qty]) => `${size}-${qty}`)
                                    .join(',')
                                );
                                $(`#product-row-${productIndex} .select-sizes`).trigger("change");
                                $("#modal-quantity").modal("hide");
                            } else {
                                alert("Vui lòng nhập số lượng hợp lệ!");
                            }
                        });

                        $('.select-sizes').on("select2:unselect", function(e) {
                            const unselectedSize = e.params.data.id;
                            const productIndex = $(this).data('index');
                            delete productsData[productIndex].new_sizes_quantities[unselectedSize];
                            const formattedSizesInput = $(
                                `#product-row-${productIndex} .formatted-new-sizes`);
                            formattedSizesInput.val(
                                Object.entries(productsData[productIndex].new_sizes_quantities)
                                .map(([size, qty]) => `${size}-${qty}`)
                                .join(',')
                            );
                        });

                        $(document).on('change', '.new-price-input', function() {
                            const index = $(this).data('index');
                            productsData[index].new_price = $(this).val();
                        });

                        $(document).on('change', '.new-color-input', function() {
                            const index = $(this).data('index');
                            const newColor = $(this).val().trim();

                            if (newColor.includes(',')) {
                                alert('Vui lòng chỉ nhập MỘT màu duy nhất cho sản phẩm này.');
                                $(this).val(productsData[index].new_color || '');
                                return;
                            }

                            productsData[index].new_color = newColor;
                        });

                        $("#formCreateInventory").on("submit", function(e) {
                            const selectedProductsToSend = [];
                            let validationError = false;

                            document.querySelectorAll('.product-check:checked').forEach(checkbox => {
                                const index = checkbox.dataset.index;
                                const productItem = productsData[index];

                                productItem.new_price = $(`#product-row-${index} .new-price-input`).val();
                                productItem.new_color = $(`#product-row-${index} .new-color-input`).val();

                                const hasNewPrice = productItem.new_price && parseFloat(productItem.new_price) > 0;
                                const hasNewSizesQuantities = Object.keys(productItem.new_sizes_quantities).length > 0;
                                const hasNewColor = productItem.new_color && productItem.new_color.trim() !== '';

                                const hasNewData = hasNewPrice || hasNewSizesQuantities || hasNewColor;

                                if (hasNewData) {
                                    // Validation: If new color or new price is provided, but no sizes/quantities are added
                                    if ((hasNewColor || hasNewPrice) && !hasNewSizesQuantities) {
                                        alert(
                                            `Lỗi: Vui lòng chọn kích cỡ và nhập số lượng nhập thêm cho sản phẩm "${productItem.product_name}" khi bạn đã nhập Màu mới hoặc Giá nhập thêm.`
                                        );
                                        validationError = true;
                                        return; // Break out of forEach
                                    }

                                    // Validation: if new sizes/quantities are provided, new_color and new_price must be set
                                    if (hasNewSizesQuantities && (!hasNewColor || !hasNewPrice)) {
                                        alert(
                                            `Lỗi: Vui lòng nhập đầy đủ Màu mới và Giá nhập thêm cho các biến thể mới của sản phẩm "${productItem.product_name}".`
                                        );
                                        validationError = true;
                                        return; // Break out of forEach
                                    }

                                    selectedProductsToSend.push({
                                        product_id: productItem.product_id,
                                        new_color: productItem.new_color,
                                        new_price: productItem.new_price,
                                        new_sizes_quantities: productItem.new_sizes_quantities
                                    });
                                }
                            });

                            if (validationError) {
                                e.preventDefault();
                                return;
                            }

                            if (selectedProductsToSend.length === 0) {
                                alert(
                                    "Vui lòng chọn ít nhất một sản phẩm và nhập thông tin bổ sung (giá, màu hoặc kích cỡ/số lượng)!"
                                );
                                e.preventDefault();
                                return;
                            }

                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'products_to_add';
                            input.value = JSON.stringify(selectedProductsToSend);
                            this.appendChild(input);

                            $(this).find('input[name^="products["]:not([type="hidden"])').prop(
                                'disabled', true);
                            $(this).find('select[name^="products["]').prop('disabled', true);
                        });
                    }
                })
                .catch(error => {
                    console.error("Lỗi khi lấy API:", error);
                    alert(
                        "Không thể tải thông tin phiếu nhập. Vui lòng kiểm tra kết nối mạng hoặc thử lại sau."
                    );
                });

            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(amount);
            }
        });
    </script>
@endsection
@else
    {{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

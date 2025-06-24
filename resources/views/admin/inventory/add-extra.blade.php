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
                <h5 class="mb-0 fw-bold"><i class="fas fa-truck me-2"></i>Thông tin nhà cung cấp và ghi chú</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="provider_name_display" class="form-label fw-bold">Nhà cung cấp:</label>
                    <input type="text" id="provider_name_display" class="form-control form-control-lg" readonly
                        placeholder="Tên nhà cung cấp">
                    <input type="hidden" name="provider_id" id="provider_id_hidden">
                </div>
                <div class="mb-3">
                    <label for="note" class="form-label fw-bold">Ghi chú:</label>
                    <textarea id="note" name="note" class="form-control" rows="3" placeholder="Nhập ghi chú cho phiếu nhập"
                        required></textarea>
                </div>
            </div>
        </div>

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
                                <th scope="col" width="50px" class="text-center">Chọn</th>
                                <th scope="col" width="150px">Sản phẩm</th>
                                <th scope="col" width="100px">Hình ảnh</th>
                                <th scope="col" width="120px">Thông tin cơ bản</th>
                                <th scope="col" class="text-center">Biến thể hiện có</th>
                                <th scope="col" width="250px">Thông tin nhập thêm</th>
                            </tr>
                        </thead>
                        <tbody id="products-tbody">
                            {{-- Product rows will be inserted here by JavaScript --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center align-items-center">
            <button type="submit" class="btn btn-success rounded-pill px-5 py-3 shadow-lg mt-3 fw-bold">
                <i class="fas fa-save me-2"></i>Tạo phiếu nhập
            </button>
        </div>
    </form>

    <!-- Modal for quantity input -->
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

    <!-- Modal for color selection -->
    <div class="modal fade" id="modal-colors" tabindex="-1" aria-labelledby="modal-colors-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header bg-primary text-white text-center rounded-top-4">
                    <h5 class="modal-title fw-bold" id="modal-colors-label">Chọn màu sắc</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="text" id="new-color-input" class="form-control" placeholder="Thêm màu mới">
                            <button class="btn btn-primary" id="add-color-btn">Thêm</button>
                        </div>
                    </div>

                    <div class="color-options row">
                        <!-- Color options will be inserted here by JavaScript -->
                    </div>

                    <div class="selected-colors mt-3">
                        <h6 class="fw-bold">Màu đã chọn:</h6>
                        <div id="selected-colors-container" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between border-top-0 pt-0">
                    <button type="button" class="btn btn-danger text-white btn-colors-clear rounded-pill px-4 py-2">
                        <i class="fas fa-trash me-2"></i>Xóa tất cả
                    </button>
                    <button type="button" class="btn btn-success text-white btn-colors-submit rounded-pill px-4 py-2">
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
        .select2-results__option[aria-selected=true] {
            background-color: #f8f9fa;
            color: #6c757d;
            position: relative;
        }

        .select2-results__option[aria-selected=true]:after {
            content: "✓";
            position: absolute;
            right: 10px;
            color: #28a745;
        }

        /* General styles */
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        /* Card styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
            border-radius: 10px 10px 0 0 !important;
        }

        /* Table styles */
        .table {
            font-size: 0.9rem;
        }

        .table th {
            background-color: #f1f3f9;
            font-weight: 600;
            color: #495057;
            vertical-align: middle;
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
        }

        .variant-table {
            font-size: 0.85rem;
            width: 100%;
        }

        .variant-table th,
        .variant-table td {
            padding: 0.3rem 0.5rem;
        }

        /* Color options */
        .color-option {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin: 0 auto 5px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s;
            position: relative;
        }

        .color-option:hover {
            transform: scale(1.1);
        }

        .color-option.selected {
            border-color: #000;
            box-shadow: 0 0 0 2px #4e73df;
        }

        .selected-color-badge {
            display: inline-flex;
            align-items: center;
            background-color: #e9ecef;
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 0.85rem;
        }

        .selected-color-badge .color-preview {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            margin-right: 5px;
            border: 1px solid #ddd;
        }

        /* New styles for color management */
        .color-options-container {
            max-height: 200px;
            overflow-y: auto;
            margin-bottom: 15px;
        }

        .color-option .delete-color {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            cursor: pointer;
        }

        .color-option:hover .delete-color {
            display: flex;
        }

        .size-quantity-badge {
            position: relative;
            padding-right: 20px;
        }

        .size-quantity-badge .quantity-display {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #28a745;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table th,
            .table td {
                white-space: nowrap;
                min-width: 120px;
            }
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }

            .btn-group-bulk {
                flex-direction: column;
                gap: 0.5rem;
            }

            .btn-group-bulk .btn {
                width: 100%;
            }
        }

        /* Badge styles */
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }

        /* Input styles */
        .form-control,
        .form-select {
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
        }

        /* Button styles */
        .btn {
            border-radius: 8px;
            font-weight: 500;
        }

        /* Modal styles */
        .modal-content {
            border-radius: 12px;
            border: none;
        }

        /* Image styles */
        .product-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        /* Compact form elements */
        .compact-form .form-group {
            margin-bottom: 0.5rem;
        }

        /* Scrollable table body */
        .table-scrollable {
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const inventory_id = urlParams.get('inventory_id');

            document.getElementById('note').value = `nhập thêm từ phiếu #${inventory_id}`;

            if (!inventory_id) {
                console.error("Không tìm thấy inventory_id trong URL.");
                alert("Không tìm thấy ID phiếu nhập. Vui lòng thử lại.");
                window.history.back();
                return;
            }

            let productsData = [];
            let currentProductIndex = null;
            let selectedColors = [];
            let customColors = [];

            const defaultColors = [{
                    name: 'Đen',
                    code: '#000000'
                },
                {
                    name: 'Trắng',
                    code: '#ffffff'
                },
                {
                    name: 'Xám',
                    code: '#808080'
                },
                {
                    name: 'Xanh navy',
                    code: '#000080'
                },
                {
                    name: 'Xanh dương',
                    code: '#0000ff'
                },
                {
                    name: 'Đỏ',
                    code: '#ff0000'
                },
                {
                    name: 'Hồng',
                    code: '#ffc0cb'
                },
                {
                    name: 'Vàng',
                    code: '#ffff00'
                },
                {
                    name: 'Xanh lá',
                    code: '#008000'
                },
                {
                    name: 'Nâu',
                    code: '#a52a2a'
                },
                {
                    name: 'Be',
                    code: '#f5f5dc'
                },
                {
                    name: 'Cam',
                    code: '#ffa500'
                },
                {
                    name: 'Tím',
                    code: '#800080'
                }
            ];

            // Hàm hiển thị các màu có sẵn
            function renderColorOptions() {
                const container = $('.color-options');
                container.empty();

                defaultColors.concat(customColors).forEach(color => {
                    container.append(`
                <div class="col-4 col-md-3 mb-3">
                    <div class="color-option" data-color="${color.name}" style="background-color: ${color.code};">
                        <span class="delete-color" data-color="${color.name}">×</span>
                    </div>
                    <span>${color.name}</span>
                </div>
            `);
                });
            }

            // Hàm tạo màu ngẫu nhiên cho màu mới
            function getRandomColor() {
                const letters = '0123456789ABCDEF';
                let color = '#';
                for (let i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }

            // Fetch inventory data
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

                        document.getElementById('provider_name_display').value = inventoryData.provider.name ||
                            "";
                        document.getElementById('provider_id_hidden').value = inventoryData.provider.id || "";

                        const groupedProducts = {};
                        inventoryData.detail.forEach(item => {
                            if (!groupedProducts[item.product.id]) {
                                groupedProducts[item.product.id] = {
                                    product: item.product,
                                    all_product_variants: item.product['product-variant'] || [],
                                    variants_in_slip: []
                                };
                            }

                            const parts = item.sizes.split('-');
                            let variantSizeFromSlip = '';
                            let variantColorFromSlip = '';

                            if (parts.length >= 3) {
                                variantSizeFromSlip = parts[0].trim();
                                variantColorFromSlip = parts[2].trim();
                            } else {
                                console.warn('Unexpected "sizes" format:', item.sizes);
                                variantColorFromSlip = item.color || '';
                                variantSizeFromSlip = item.sizes.trim();
                            }

                            groupedProducts[item.product.id].variants_in_slip.push({
                                color: variantColorFromSlip,
                                sizes: variantSizeFromSlip,
                                quantity_in_slip: item.quantity,
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
                            all_product_variants: item.all_product_variants,
                            variants_in_slip: item.variants_in_slip,
                            new_colors: [],
                            new_price: '',
                            new_sizes_quantities: {}
                        }));

                        const productsTbody = document.getElementById('products-tbody');
                        productsTbody.innerHTML = '';

                        productsData.forEach((productItem, index) => {
                            const slipVariantsMap = new Map();
                            productItem.variants_in_slip.forEach(v => {
                                slipVariantsMap.set(`${v.color}-${v.sizes}`, {
                                    quantity_in_slip: v.quantity_in_slip,
                                    price: v.price
                                });
                            });

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
                            <img src="${productItem.product_image}" class="product-img" alt="${productItem.product_name}">
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <small><strong>Danh mục:</strong> ${productItem.category_name}</small>
                                <small><strong>Thương hiệu:</strong> ${productItem.brand_name}</small>
                            </div>
                        </td>
                        <td>
                            <div class="variant-scroll" style="max-height: 150px; overflow-y: auto;">
                                <table class="variant-table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Màu</th>
                                            <th>Size</th>
                                            <th>Kho</th>
                                            <th>Đã nhập</th>
                                            <th>Giá</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${productItem.all_product_variants.map(variant => {
                                            const slipInfo = slipVariantsMap.get(`${variant.color}-${variant.size}`);
                                            const quantityInSlip = slipInfo ? slipInfo.quantity_in_slip : '---';
                                            const priceInSlip = slipInfo ? formatCurrency(slipInfo.price) : '---';
                                            return `
                                                                                <tr>
                                                                                    <td>${variant.color}</td>
                                                                                    <td><span class="badge bg-secondary">${variant.size}</span></td>
                                                                                    <td>${variant.stock || 0}</td>
                                                                                    <td>${quantityInSlip}</td>
                                                                                    <td>${priceInSlip}</td>
                                                                                </tr>
                                                                            `;
                                        }).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </td>
                        <td>
                            <div class="compact-form">
                                <div class="form-group mb-2">
                                    <label class="form-label fw-bold small">Giá nhập:</label>
                                    <input type="number" name="products[${index}][new_price]"
                                           class="form-control form-control-sm new-price-input"
                                           data-index="${index}"
                                           placeholder="Giá nhập"
                                           min="0">
                                </div>

                                <div class="form-group mb-2">
                                    <label class="form-label fw-bold small">Màu mới:</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary w-100 btn-select-colors"
                                            data-index="${index}">
                                        <i class="fas fa-palette me-1"></i> Chọn màu
                                    </button>
                                    <div class="selected-colors-display mt-1" data-index="${index}"></div>
                                    <input type="hidden" name="products[${index}][new_colors]"
                                           class="formatted-new-colors"
                                           data-index="${index}">
                                </div>

                                <div class="form-group">
                                    <label class="form-label fw-bold small">Size & SL:</label>
                                    <select class="form-select form-select-sm select-sizes"
                                            name="products[${index}][new_sizes][]"
                                            multiple="multiple"
                                            data-index="${index}">
                                        ${['XS', 'S', 'M', 'L', 'XL', 'XXL'].map(size =>
                                            `<option value="${size}">${size}</option>`
                                        ).join('')}
                                    </select>
                                    <input type="hidden" name="products[${index}][formatted_new_sizes]"
                                           class="formatted-new-sizes"
                                           data-index="${index}">
                                </div>
                            </div>
                        </td>
                    `;
                            productsTbody.appendChild(row);
                        });

                        // Initialize select2 for sizes
                        $('.select-sizes').select2({
                            theme: "bootstrap-5",
                            placeholder: "Chọn size",
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#products_container'),
                            templateSelection: formatSizeSelection,
                            templateResult: formatSizeResult
                        });

                        // Render color options
                        renderColorOptions();

                        // Xử lý thêm màu mới
                        $('#add-color-btn').click(function() {
                            const colorName = $('#new-color-input').val().trim();
                            if (colorName && !defaultColors.concat(customColors).some(c => c.name ===
                                    colorName)) {
                                customColors.push({
                                    name: colorName,
                                    code: getRandomColor()
                                });
                                renderColorOptions();
                                $('#new-color-input').val('');
                            }
                        });

                        // Xử lý xóa màu
                        $(document).on('click', '.delete-color', function(e) {
                            e.stopPropagation();
                            const colorName = $(this).data('color');
                            customColors = customColors.filter(c => c.name !== colorName);

                            // Xóa khỏi danh sách đã chọn nếu có
                            const index = selectedColors.indexOf(colorName);
                            if (index !== -1) {
                                selectedColors.splice(index, 1);
                                updateSelectedColorsDisplay();
                            }

                            renderColorOptions();
                        });

                        // Xử lý xóa tất cả màu đã chọn
                        $('.btn-colors-clear').click(function() {
                            selectedColors = [];
                            updateSelectedColorsDisplay();
                        });

                        // Color selection modal handlers
                        $(document).on('click', '.btn-select-colors', function() {
                            currentProductIndex = $(this).data('index');
                            selectedColors = productsData[currentProductIndex].new_colors || [];
                            updateSelectedColorsDisplay();
                            $('#modal-colors').modal('show');
                        });

                        $(document).on('click', '.color-option', function() {
                            const color = $(this).data('color');
                            const index = selectedColors.indexOf(color);

                            if (index === -1) {
                                selectedColors.push(color);
                                $(this).addClass('selected');
                            } else {
                                selectedColors.splice(index, 1);
                                $(this).removeClass('selected');
                            }

                            updateSelectedColorsDisplay();
                        });

                        $('.btn-colors-submit').click(function() {
                            if (currentProductIndex !== null) {
                                productsData[currentProductIndex].new_colors = selectedColors;
                                updateProductColorDisplay(currentProductIndex);
                                $('#modal-colors').modal('hide');
                            }
                        });

                        // Quantity modal handlers
                        $('.select-sizes').on("select2:select", function(e) {
                            const selectedSize = e.params.data.id;
                            const productIndex = $(this).data('index');

                            // Kiểm tra nếu size đã được chọn
                            if (productsData[productIndex].new_sizes_quantities[selectedSize]) {
                                alert(
                                    `Size ${selectedSize} đã được chọn. Vui lòng chọn size khác hoặc cập nhật số lượng cho size này.`
                                    );
                                $(this).val(Object.keys(productsData[productIndex]
                                    .new_sizes_quantities)).trigger('change');
                                return;
                            }

                            // Phần còn lại giữ nguyên
                            $('#modal-quantity-label').text(`Nhập số lượng cho size ${selectedSize}`);
                            $("#quantity_variant").val(
                                productsData[productIndex].new_sizes_quantities[selectedSize] || 1
                            );

                            $('#modal-quantity').data('product-index', productIndex);
                            $('#modal-quantity').data('selected-size', selectedSize);
                            $("#modal-quantity").modal("show");
                        });

                        $(".btn-quantity-submit").on("click", function() {
                            const quantity = parseInt($("#quantity_variant").val()) || 0;
                            const productIndex = $('#modal-quantity').data('product-index');
                            const selectedSize = $('#modal-quantity').data('selected-size');

                            if (quantity > 0) {
                                productsData[productIndex].new_sizes_quantities[selectedSize] =
                                    quantity;
                                updateProductSizesDisplay(productIndex);
                                $("#modal-quantity").modal("hide");
                            } else {
                                alert("Vui lòng nhập số lượng hợp lệ!");
                            }
                        });

                        $('.select-sizes').on("select2:unselect", function(e) {
                            const unselectedSize = e.params.data.id;
                            const productIndex = $(this).data('index');
                            delete productsData[productIndex].new_sizes_quantities[unselectedSize];
                            updateProductSizesDisplay(productIndex);
                        });

                        // Product selection handlers
                        document.getElementById('selectAllProductsBtn').addEventListener('click', function() {
                            document.querySelectorAll('.product-check').forEach(checkbox => {
                                checkbox.checked = true;
                                const index = checkbox.dataset.index;
                                toggleProductRow(index, true);
                            });
                        });

                        document.getElementById('deselectAllProductsBtn').addEventListener('click', function() {
                            document.querySelectorAll('.product-check').forEach(checkbox => {
                                checkbox.checked = false;
                                const index = checkbox.dataset.index;
                                toggleProductRow(index, false);

                                // Reset data when deselecting
                                productsData[index].new_colors = [];
                                productsData[index].new_price = '';
                                productsData[index].new_sizes_quantities = {};

                                // Update UI
                                $(`#product-row-${index} .selected-colors-display`).empty();
                                $(`#product-row-${index} .formatted-new-colors`).val('');
                                $(`#product-row-${index} .new-price-input`).val('');
                                $(`#product-row-${index} .select-sizes`).val(null).trigger(
                                    'change');
                                $(`#product-row-${index} .formatted-new-sizes`).val('');
                            });
                        });

                        document.querySelectorAll('.product-check').forEach(checkbox => {
                            checkbox.addEventListener('change', function() {
                                const index = this.dataset.index;
                                const isChecked = this.checked;
                                toggleProductRow(index, isChecked);

                                if (!isChecked) {
                                    // Reset data when unchecking
                                    productsData[index].new_colors = [];
                                    productsData[index].new_price = '';
                                    productsData[index].new_sizes_quantities = {};

                                    // Update UI
                                    $(`#product-row-${index} .selected-colors-display`).empty();
                                    $(`#product-row-${index} .formatted-new-colors`).val('');
                                    $(`#product-row-${index} .new-price-input`).val('');
                                    $(`#product-row-${index} .select-sizes`).val(null).trigger(
                                        'change');
                                    $(`#product-row-${index} .formatted-new-sizes`).val('');
                                }
                            });
                        });

                        // Form submission
                        $("#formCreateInventory").on("submit", function(e) {
                            e.preventDefault();

                            const note = $('#note').val().trim();

                            // Remove any existing hidden input
                            $(this).find('input[name="products_to_add"]').remove();

                            const selectedProductsToSend = [];
                            let validationError = false;
                            let hasSelectedProducts = false;

                            // Loop through all products
                            productsData.forEach((productItem, index) => {
                                const isChecked = $(`#product-row-${index} .product-check`).is(
                                    ':checked');

                                if (isChecked) {
                                    hasSelectedProducts = true;

                                    // Get values from form
                                    productItem.new_price = $(
                                        `#product-row-${index} .new-price-input`).val();
                                    productItem.new_colors = $(
                                            `#product-row-${index} .formatted-new-colors`).val()
                                        ?.split(',') || [];

                                    const hasNewPrice = productItem.new_price && parseFloat(
                                        productItem.new_price) > 0;
                                    const hasNewSizesQuantities = Object.keys(productItem
                                        .new_sizes_quantities).length > 0;
                                    const hasNewColors = productItem.new_colors.length > 0;

                                    const hasNewData = hasNewPrice || hasNewSizesQuantities ||
                                        hasNewColors;

                                    if (hasNewData) {
                                        if ((hasNewColors || hasNewPrice) && !
                                            hasNewSizesQuantities) {
                                            alert(
                                                `Lỗi: Vui lòng chọn size và số lượng cho sản phẩm "${productItem.product_name}"`
                                            );
                                            validationError = true;
                                            return;
                                        }

                                        if (hasNewSizesQuantities && (!hasNewColors || !
                                                hasNewPrice)) {
                                            alert(
                                                `Lỗi: Vui lòng nhập đủ màu và giá cho sản phẩm "${productItem.product_name}"`
                                            );
                                            validationError = true;
                                            return;
                                        }

                                        selectedProductsToSend.push({
                                            product_id: productItem.product_id,
                                            new_colors: productItem.new_colors,
                                            new_price: productItem.new_price,
                                            new_sizes_quantities: productItem
                                                .new_sizes_quantities
                                        });
                                    }
                                }
                            });


                            let hasDuplicateSizes = false;

                            selectedProductsToSend.forEach(product => {
                                const sizes = Object.keys(product.new_sizes_quantities);
                                const uniqueSizes = new Set(sizes);

                                if (sizes.length !== uniqueSizes.size) {
                                    hasDuplicateSizes = true;
                                    const productName = productsData.find(p => p.product_id ===
                                        product.product_id)?.product_name || '';
                                    alert(
                                        `Lỗi: Sản phẩm "${productName}" có size bị trùng lặp. Vui lòng kiểm tra lại.`
                                    );
                                }
                            });

                            if (hasDuplicateSizes) {
                                alert("Lỗi: Có size bị trùng lặp. Vui lòng kiểm tra lại.");
                                return;
                            }

                            if (validationError) {
                                return;
                            }

                            if (!hasSelectedProducts || selectedProductsToSend.length === 0) {
                                alert("Vui lòng chọn ít nhất một sản phẩm và nhập thông tin bổ sung!");
                                return;
                            }

                            // Create hidden input with selected products data
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'products_to_add';
                            input.value = JSON.stringify(selectedProductsToSend);
                            this.appendChild(input);


                            if (note) {
                                const noteInput = document.createElement('input');
                                noteInput.type = 'hidden';
                                noteInput.name = 'note';
                                noteInput.value = note;
                                this.appendChild(noteInput);
                            }


                            // Submit form
                            this.submit();
                        });
                    }
                })
                .catch(error => {
                    console.error("Lỗi khi lấy API:", error);
                    alert(
                        "Không thể tải thông tin phiếu nhập. Vui lòng kiểm tra kết nối mạng hoặc thử lại sau."
                    );
                });

            // Hàm định dạng hiển thị size trong dropdown (có số lượng)
            function formatSizeSelection(state) {
                if (!state.id) {
                    return state.text;
                }

                const $option = $(state.element);
                const size = state.id;
                const productIndex = $option.closest('select').data('index');
                const qty = productsData[productIndex]?.new_sizes_quantities[size] || 0;

                if (qty > 0) {
                    return $(`<span>${size} <span class="badge bg-success ms-1">${qty}</span></span>`);
                }
                return state.text;
            }

            // Hàm định dạng hiển thị size trong kết quả tìm kiếm dropdown
            function formatSizeResult(state) {
                if (!state.id) {
                    return state.text;
                }

                const $option = $(state.element);
                const size = state.id;
                const productIndex = $option.closest('select').data('index');
                const qty = productsData[productIndex]?.new_sizes_quantities[size] || 0;

                if (qty > 0) {
                    return $(`<span>${size} <small class="text-muted">(Đã chọn: ${qty})</small></span>`);
                }
                return state.text;
            }

            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(amount);
            }

            function toggleProductRow(index, isEnabled) {
                $(`#product-row-${index} input:not([type="hidden"]), #product-row-${index} select`)
                    .prop('disabled', !isEnabled);
                $(`#product-row-${index} .select-sizes`).select2(isEnabled ? 'enable' : 'disable');
                $(`#product-row-${index} .btn-select-colors`).prop('disabled', !isEnabled);
            }

            function updateSelectedColorsDisplay() {
                const container = $('#selected-colors-container');
                container.empty();

                selectedColors.forEach(color => {
                    const colorCode = getColorCode(color);
                    container.append(`
                <div class="selected-color-badge">
                    <div class="color-preview" style="background-color: ${colorCode};"></div>
                    ${color}
                </div>
            `);
                });

                // Highlight selected options in modal
                $('.color-option').removeClass('selected');
                selectedColors.forEach(color => {
                    $(`.color-option[data-color="${color}"]`).addClass('selected');
                });
            }

            function updateProductColorDisplay(index) {
                const displayContainer = $(`#product-row-${index} .selected-colors-display`);
                const hiddenInput = $(`#product-row-${index} .formatted-new-colors`);

                displayContainer.empty();
                hiddenInput.val(selectedColors.join(','));

                selectedColors.forEach(color => {
                    const colorCode = getColorCode(color);
                    displayContainer.append(`
                <span class="badge me-1 mb-1" style="background-color: ${colorCode}; color: ${getTextColor(colorCode)}">
                    ${color}
                </span>
            `);
                });
            }

            function updateProductSizesDisplay(index) {
                const formattedSizesInput = $(`#product-row-${index} .formatted-new-sizes`);
                const selectElement = $(`#product-row-${index} .select-sizes`);

                // Kiểm tra trùng lặp size
                const sizeCounts = {};
                Object.keys(productsData[index].new_sizes_quantities).forEach(size => {
                    sizeCounts[size] = (sizeCounts[size] || 0) + 1;
                });

                const duplicateSizes = Object.keys(sizeCounts).filter(size => sizeCounts[size] > 1);
                if (duplicateSizes.length > 0) {
                    alert(`Lỗi: Size ${duplicateSizes.join(', ')} đã được chọn nhiều lần. Vui lòng kiểm tra lại.`);
                    // Xóa các size trùng
                    duplicateSizes.forEach(size => {
                        delete productsData[index].new_sizes_quantities[size];
                    });
                    // Cập nhật lại select2
                    selectElement.val(Object.keys(productsData[index].new_sizes_quantities)).trigger('change');
                }

                // Phần còn lại giữ nguyên
                formattedSizesInput.val(
                    Object.entries(productsData[index].new_sizes_quantities)
                    .map(([size, qty]) => `${size}-${qty}`)
                    .join(',')
                );

                // Cập nhật hiển thị trên select2
                selectElement.find('option').each(function() {
                    const size = $(this).val();
                    const qty = productsData[index].new_sizes_quantities[size] || 0;

                    if (qty > 0) {
                        $(this).text(`${size} (${qty})`);
                    } else {
                        $(this).text(size);
                    }
                });

                // Cập nhật hiển thị badge số lượng trên các tag đã chọn
                setTimeout(() => {
                    selectElement.next('.select2-container').find('.select2-selection__choice').each(
                        function() {
                            const $choice = $(this);
                            const size = $choice.attr('title');
                            const qty = productsData[index].new_sizes_quantities[size] || 0;

                            // Xóa badge cũ nếu có
                            $choice.find('.size-quantity-badge').remove();

                            if (qty > 0) {
                                // Thêm badge mới
                                $choice.append(`
                        <span class="size-quantity-badge">
                            <span class="quantity-display">${qty}</span>
                        </span>
                    `);
                            }
                        });
                }, 100);

                selectElement.trigger("change");
            }

            function getColorCode(colorName) {
                // Kiểm tra trong defaultColors trước
                const defaultColor = defaultColors.find(c => c.name === colorName);
                if (defaultColor) return defaultColor.code;

                // Kiểm tra trong customColors
                const customColor = customColors.find(c => c.name === colorName);
                if (customColor) return customColor.code;

                // Nếu không tìm thấy, trả về màu mặc định
                return '#cccccc';
            }

            function getTextColor(bgColor) {
                // Convert hex to RGB
                const r = parseInt(bgColor.substr(1, 2), 16);
                const g = parseInt(bgColor.substr(3, 2), 16);
                const b = parseInt(bgColor.substr(5, 2), 16);

                // Calculate luminance
                const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

                // Return black or white depending on luminance
                return luminance > 0.5 ? '#000000' : '#ffffff';
            }
        });
    </script>
@endsection
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

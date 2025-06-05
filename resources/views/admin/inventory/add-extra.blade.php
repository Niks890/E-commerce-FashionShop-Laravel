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

        <div class="d-flex justify-content-center align-items-center">
            <button type="submit" class=" btn btn-success rounded-pill px-5 py-3 shadow-lg mt-3 fw-bold">
                <i class="fas fa-save me-2"></i>Tạo phiếu nhập
            </button>
        </div>

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
        /* General Body and Container Styles */
        body {
            background-color: #f0f2f5;
            /* A slightly softer, modern background */
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #333;
        }

        .container-fluid {
            /* Assuming master.blade.php provides a fluid container */
            padding-top: 2rem;
            padding-bottom: 2rem;
        }

        /* Card Enhancements */
        .card {
            border: none;
            border-radius: 0.75rem;
            /* More rounded corners */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            /* Softer, more prominent shadow */
            overflow: hidden;
            /* Ensures rounded corners apply to children */
        }

        .card-header {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%) !important;
            /* Vibrant gradient */
            color: white;
            padding: 1.5rem 2rem;
            /* More padding */
            font-size: 1.25rem;
            /* Larger header text */
            font-weight: 700;
            /* Bolder header */
            border-bottom: none;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            display: flex;
            align-items: center;
        }

        .card-header i {
            font-size: 1.5rem;
            /* Larger icons in header */
            margin-right: 0.75rem;
        }

        .card-body {
            padding: 2rem;
        }

        /* Back Button */
        .btn-outline-primary {
            border: 2px solid #007bff;
            color: #007bff;
            padding: 0.65rem 1.5rem;
            border-radius: 50rem;
            /* Fully rounded */
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            /* Align icon and text vertically */
            align-items: center;
        }

        .btn-outline-primary:hover {
            background-color: #007bff;
            color: white;
            transform: translateY(-3px);
            /* Slight lift effect */
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }

        .btn-outline-primary i {
            margin-right: 0.5rem;
        }

        /* Form Controls */
        .form-label {
            font-weight: 700;
            /* Bolder labels */
            color: #555;
            margin-bottom: 0.5rem;
        }

        .form-control-lg {
            padding: 0.85rem 1.25rem;
            /* Larger padding for inputs */
            font-size: 1.1rem;
            border-radius: 0.5rem;
            /* Slightly more rounded */
            border: 1px solid #ced4da;
            transition: all 0.3s ease;
        }

        .form-control-lg:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }

        /* Bulk Action Buttons */
        .btn-group-bulk {
            display: flex;
            /* Use flexbox for button group */
            gap: 1rem;
            /* Space between buttons */
            margin-bottom: 1.5rem;
        }

        .btn-group-bulk .btn {
            padding: 0.75rem 1.75rem;
            border-radius: 50rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-group-bulk .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        /* Table Styling */
        .table {
            border-collapse: separate;
            /* Allows border-radius on cells in some cases */
            border-spacing: 0;
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #e2e6ea;
            /* Slightly darker header for better contrast */
            color: #343a40;
            font-weight: 700;
            border-bottom: 2px solid #d3d9e0;
            padding: 1rem 1.25rem;
            vertical-align: middle;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #e9ecef;
            /* Lighter hover effect */
        }

        .table tbody td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
            /* Center content vertically */
            border-top: 1px solid #e9ecef;
        }

        .product-check {
            transform: scale(1.4);
            /* Larger checkbox */
            cursor: pointer;
            accent-color: #007bff;
            /* Color for checked state */
        }

        .img-thumbnail {
            border-radius: 0.5rem;
            /* Rounded image corners */
            object-fit: cover;
            /* Ensures images cover the area without distortion */
            width: 80px;
            /* Consistent size */
            height: 80px;
            /* Consistent size */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        /* Variant Table (Inner Table) */
        .variant-table {
            width: 100%;
            margin-top: 10px;
            /* More space above the table */
            border-radius: 0.5rem;
            /* Rounded corners for the inner table */
            overflow: hidden;
            /* Apply border-radius correctly */
            border: 1px solid #e0e6eb;
            /* Subtle border around the inner table */
        }

        .variant-table th,
        .variant-table td {
            padding: 8px 12px;
            text-align: left;
            border: none;
            /* Remove inner borders for a cleaner look */
        }

        .variant-table thead {
            background-color: #f1f4f7;
            /* Lighter background for inner table header */
        }

        .variant-table th {
            font-weight: 600;
            color: #495057;
        }

        .variant-table td {
            color: #555;
        }

        .badge.bg-secondary {
            background-color: #6c757d !important;
            font-size: 0.8em;
            /* Slightly larger badge text */
            padding: 0.4em 0.8em;
            border-radius: 0.3rem;
        }

        /* Select2 Customizations */
        .select2-container--bootstrap-5 .select2-selection--multiple {
            min-height: calc(3.5rem + 2px) !important;
            padding: 0.75rem 1rem !important;
            border-radius: 0.5rem !important;
            /* Match form control radius */
            font-size: 1.1rem !important;
            border: 1px solid #ced4da !important;
            transition: all 0.3s ease;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple:focus-within {
            border-color: #80bdff !important;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25) !important;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: #0d6efd;
            color: white;
            border: 1px solid #0a58ca;
            border-radius: 0.35rem;
            /* Slightly more rounded chips */
            padding: 0.5rem 0.85rem;
            /* More padding for chips */
            font-size: 0.95rem;
            margin-top: 0.4rem;
            /* Adjust vertical alignment */
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            font-size: 1.2rem;
            /* Larger remove icon */
            margin-right: 0.3rem;
        }

        .select2-container--bootstrap-5 .select2-dropdown {
            border-radius: 0.5rem;
            /* Rounded dropdown */
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            border: none;
        }

        .select2-container--bootstrap-5 .select2-results__option {
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }

        .select2-container--bootstrap-5 .select2-results__option.select2-results__option--highlighted {
            background-color: #007bff !important;
            color: white !important;
        }

        /* Modal Styling */
        .modal-content {
            border-radius: 0.75rem;
            /* Rounded modal corners */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background-color: #007bff !important;
            color: white;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            padding: 1.5rem;
            border-bottom: none;
        }

        .modal-header .btn-close {
            filter: invert(1);
            /* White close button for dark background */
        }

        .modal-title {
            font-weight: 700;
            font-size: 1.3rem;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            background-color: #f8f9fa;
            border-bottom-left-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
            padding: 1.5rem;
            border-top: none;
            justify-content: center;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            padding: 0.85rem 2.5rem;
            font-size: 1.1rem;
            border-radius: 50rem;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .table-responsive {
                border: 1px solid #e9ecef;
                /* Add a border to the scrollable table on smaller screens */
                border-radius: 0.5rem;
            }

            .table thead {
                display: none;
                /* Hide table headers on small screens */
            }

            .table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #dee2e6;
                border-radius: 0.75rem;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            }

            .table tbody td {
                display: block;
                text-align: right;
                padding-left: 50%;
                position: relative;
                border: none;
            }

            .table tbody td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 1.25rem;
                font-weight: 600;
                text-align: left;
                color: #495057;
            }

            /* Specific label for each column in responsive table */
            .table tbody td:nth-of-type(1):before {
                content: "Chọn";
            }

            .table tbody td:nth-of-type(2):before {
                content: "Sản phẩm";
            }

            .table tbody td:nth-of-type(3):before {
                content: "Hình ảnh";
            }

            .table tbody td:nth-of-type(4):before {
                content: "Danh mục";
            }

            .table tbody td:nth-of-type(5):before {
                content: "Thương hiệu";
            }

            .table tbody td:nth-of-type(6):before {
                content: "Thông tin biến thể hiện có";
            }

            .table tbody td:nth-of-type(7):before {
                content: "Giá nhập thêm (VND)";
            }

            .table tbody td:nth-of-type(8):before {
                content: "Màu mới";
            }

            .table tbody td:nth-of-type(9):before {
                content: "Kích cỡ & Số lượng nhập thêm";
            }

            .btn-group-bulk {
                flex-direction: column;
                /* Stack buttons vertically */
            }
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

                        document.getElementById('provider_name_display').value = inventoryData.provider.name ||
                            "";
                        document.getElementById('provider_id_hidden').value = inventoryData.provider.id || "";

                        const groupedProducts = {};
                        inventoryData.detail.forEach(item => {
                            if (!groupedProducts[item.product.id]) {
                                groupedProducts[item.product.id] = {
                                    product: item.product,
                                    // Lưu trữ TẤT CẢ các biến thể của sản phẩm từ 'product-variant'
                                    all_product_variants: item.product['product-variant'] || [],
                                    // Vẫn giữ variants_in_slip để hiển thị số lượng từ phiếu nhập hiện tại
                                    variants_in_slip: []
                                };
                            }

                            // Parse the "sizes" string to extract color and size for lookup
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

                            // Thêm thông tin biến thể từ phiếu nhập vào variants_in_slip
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
                            // Giờ bạn có cả `all_product_variants`
                            all_product_variants: item.all_product_variants,
                            variants_in_slip: item
                                .variants_in_slip, // Vẫn giữ để đối chiếu số lượng phiếu nhập
                            new_color: '',
                            new_price: '',
                            new_sizes_quantities: {}
                        }));

                        const productsTbody = document.getElementById('products-tbody');
                        productsTbody.innerHTML = '';

                        productsData.forEach((productItem, index) => {
                            // Tạo một map để dễ dàng tra cứu số lượng từ phiếu nhập
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
                            <img src="${productItem.product_image}" width="70" height="70" class="img-thumbnail rounded" alt="${productItem.product_name}">
                        </td>
                        <td>${productItem.category_name}</td>
                        <td>${productItem.brand_name}</td>
                        <td>
                            <table class="variant-table">
                            <thead>
                                <tr>
                                    <th>Màu</th>
                                    <th>Kích cỡ</th>
                                    <th>SL Tổng (Kho)</th> <th>SL(Đã thêm)</th> <th>Giá Nhập (Phiếu này)</th> </tr>
                            </thead>
                                                             <tbody>
                                    ${productItem.all_product_variants.map(variant => {
                                        const slipInfo = slipVariantsMap.get(`${variant.color}-${variant.size}`);
                                        // Sử dụng '---' nếu không có dữ liệu từ phiếu nhập để rõ ràng hơn
                                        const quantityInSlip = slipInfo ? slipInfo.quantity_in_slip : '---';
                                        const priceInSlip = slipInfo ? formatCurrency(slipInfo.price) : '---';
                                        return `
                                                                <tr>
                                                                    <td>${variant.color}</td>
                                                                    <td><span class="badge bg-secondary me-1">${variant.size}</span></td>
                                                                    <td>${variant.stock || 0}</td>
                                                                    <td>${quantityInSlip}</td>
                                                                    <td>${priceInSlip}</td>
                                                                </tr>
                                                            `;
                                    }).join('')}
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

                        // ... (phần còn lại của mã JavaScript, không thay đổi)

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

                                productItem.new_price = $(
                                    `#product-row-${index} .new-price-input`).val();
                                productItem.new_color = $(
                                    `#product-row-${index} .new-color-input`).val();

                                const hasNewPrice = productItem.new_price && parseFloat(
                                    productItem.new_price) > 0;
                                const hasNewSizesQuantities = Object.keys(productItem
                                    .new_sizes_quantities).length > 0;
                                const hasNewColor = productItem.new_color && productItem
                                    .new_color.trim() !== '';

                                const hasNewData = hasNewPrice || hasNewSizesQuantities ||
                                    hasNewColor;

                                if (hasNewData) {
                                    // Validation: If new color or new price is provided, but no sizes/quantities are added
                                    if ((hasNewColor || hasNewPrice) && !
                                        hasNewSizesQuantities) {
                                        alert(
                                            `Lỗi: Vui lòng chọn kích cỡ và nhập số lượng nhập thêm cho sản phẩm "${productItem.product_name}" khi bạn đã nhập Màu mới hoặc Giá nhập thêm.`
                                        );
                                        validationError = true;
                                        return; // Break out of forEach
                                    }

                                    // Validation: if new sizes/quantities are provided, new_color and new_price must be set
                                    if (hasNewSizesQuantities && (!hasNewColor || !
                                            hasNewPrice)) {
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
                                        new_sizes_quantities: productItem
                                            .new_sizes_quantities
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

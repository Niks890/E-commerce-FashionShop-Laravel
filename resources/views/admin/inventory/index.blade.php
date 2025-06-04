@can('warehouse workers')
    @extends('admin.master')
    @section('title', 'Quản lý nhập hàng')
@section('content')
    {{-- Alert success --}}
    @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-lg js-div-dissappear text-center mx-auto"
            style="max-width: 500px; animation: slide-down 0.5s ease;">
            <i class="fas fa-check-circle me-2"></i>
            {{ Session::get('success') }}
        </div>
        <style>
            @keyframes slide-down {
                0% {
                    transform: translateY(-50%);
                    opacity: 0;
                }

                100% {
                    transform: translateY(0);
                    opacity: 1;
                }
            }
        </style>
    @endif

    {{-- Card content --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <form id="search-form" method="GET" action="{{ route('inventory.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <input type="text" name="query" id="search-input" class="form-control"
                            placeholder="Nhập tên nhân viên, ID phiếu nhập hoặc tên sản phẩm..."
                            value="{{ request('query') }}" />
                        <button type="submit" class="btn btn-outline-primary" id="search-button">
                            <i class="fas fa-search"></i>
                        </button>

                    </div>
                </div>
                <div class="col-md-7 d-flex">
                    <div class="input-group me-2 flex-grow-1">
                        <span class="input-group-text">Từ</span>
                        <input type="date" name="start_date" id="start-date-input" class="form-control"
                            value="{{ request('start_date') }}" />
                    </div>
                    <div class="input-group flex-grow-1">
                        <span class="input-group-text">Đến</span>
                        <input type="date" name="end_date" id="end-date-input" class="form-control"
                            value="{{ request('end_date') }}" />
                    </div>

                </div>
                <div class="col-12 text-end mt-2">
                    <a href="{{ route('inventory.create') }}" class="btn btn-success me-2">
                        <i class="fas fa-plus-circle me-1"></i>Tạo phiếu nhập
                    </a>
                    <a href="{{ route('admin.revenueInventory') }}" class="btn btn-warning">
                        <i class="fas fa-warehouse me-1"></i> Quản lý kho
                    </a>
                    <button type="button" class="btn btn-danger" id="clear-filters-button">
                        <i class="fas fa-times"></i> Xóa bộ lọc
                    </button>
                </div>
            </form>
            {{-- Table --}}
            <div class="table-responsive mt-4">
                <table class="table table-hover table-bordered align-middle text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Sản phẩm</th>
                            <th>Hình ảnh</th>
                            <th>Nhà cung cấp</th>
                            <th>Nhân viên lập</th>
                            <th>Tổng Số lượng</th>
                            <th>Giá nhập</th>
                            <th>Tổng tiền</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- dữ liệu ở đây --}}
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
        </div>
    </div>




    {{-- Modal Inventory Detail --}}
    <div class="modal fade" id="inventoryDetail" tabindex="-1" aria-labelledby="inventoryDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Thông tin chi tiết phiếu nhập</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Thông tin phiếu nhập</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <strong>Mã phiếu nhập:</strong>
                                            <span class="badge bg-primary" id="inventory-id"></span>
                                        </div>
                                        <div class="col-12">
                                            <strong>Nhân viên lập phiếu:</strong>
                                            <span id="staff-name" class="text-info"></span>
                                        </div>
                                        <div class="col-12">
                                            <strong>Nhà cung cấp:</strong>
                                            <span id="provider-name" class="text-success"></span>
                                        </div>
                                        <div class="col-12">
                                            <strong>Ngày tạo:</strong>
                                            <span id="iventory-created"></span>
                                        </div>
                                        <div class="col-12">
                                            <strong>Ngày cập nhật:</strong>
                                            <span id="iventory-updated"></span>
                                        </div>
                                        <div class="col-12">
                                            <strong>Trạng thái:</strong>
                                            <span id="inventory-status" class="badge bg-info"></span>
                                        </div>
                                        <div class="col-12 mt-3">
                                            <strong>Tổng tiền:</strong>
                                            <span id="total_price" class="text-danger fs-5 fw-bold"></span>
                                        </div>
                                        <div class="col-12">
                                            <strong>VAT:</strong>
                                            <span id="inventory-vat" class="text-danger fw-bold"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Thay đổi ở đây: Container cho danh sách sản phẩm --}}
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-box me-2"></i>Thông tin sản phẩm</h6>
                                </div>
                                <div class="card-body" id="products-list-container"
                                    style="max-height: 380px; overflow-y: auto;">
                                    <p class="text-muted text-center">Đang tải thông tin sản phẩm...</p>
                                </div>
                            </div>
                        </div>
                        {{-- Hết thay đổi --}}

                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-list me-2"></i>Chi tiết nhập kho</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="bg-light p-3 rounded">
                                                <strong>Tổng số lượng nhập:</strong><br>
                                                <span id="total_quantity" class="fs-4 text-primary fw-bold"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="bg-light p-3 rounded">
                                                <strong>Màu sắc:</strong><br>
                                                <span id="colors" class="text-info"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="bg-light p-3 rounded">
                                                <strong>Size & Số lượng:</strong><br>
                                                <div id="size_and_quantity" class="text-success small"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Đóng
                    </button>
                    @can('warehouse workers')
                        <button type="button" class="btn btn-primary" id="print-inventory-btn">
                            <i class="fas fa-print me-1"></i> In phiếu nhập
                        </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection



@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
@endsection

@section('js')
    @if (Session::has('success'))
        <script src="{{ asset('assets/js/message.js') }}"></script>
    @endif

    <script>
        $(document).ready(function() {
            // Lấy query và ngày từ URL khi tải trang lần đầu (nếu có)
            const urlParams = new URLSearchParams(window.location.search);
            const initialQuery = urlParams.get('query') || '';
            const initialStartDate = urlParams.get('start_date') || '';
            const initialEndDate = urlParams.get('end_date') || '';

            $('#search-input').val(initialQuery);
            $('#start-date-input').val(initialStartDate);
            $('#end-date-input').val(initialEndDate);

            // Gọi hàm fetchInventories lần đầu khi tải trang
            fetchInventories(1, initialQuery, initialStartDate, initialEndDate);

            // Xử lý sự kiện khi submit form tìm kiếm
            $('#search-form').on('submit', function(e) {
                e.preventDefault();
                const searchTerm = $('#search-input').val();
                const startDate = $('#start-date-input').val();
                const endDate = $('#end-date-input').val();

                updateUrl(searchTerm, startDate, endDate);
                fetchInventories(1, searchTerm, startDate, endDate);
            });

            // Xử lý sự kiện khi thay đổi giá trị của ô input ngày
            $('#start-date-input, #end-date-input').on('change', function() {
                const searchTerm = $('#search-input').val();
                const startDate = $('#start-date-input').val();
                const endDate = $('#end-date-input').val();

                updateUrl(searchTerm, startDate, endDate);
                fetchInventories(1, searchTerm, startDate, endDate);
            });

            // Xử lý sự kiện khi bấm nút "Xóa bộ lọc"
            $('#clear-filters-button').on('click', function() {
                $('#search-input').val('');
                $('#start-date-input').val('');
                $('#end-date-input').val('');

                updateUrl('', '', '');
                fetchInventories(1, '', '', '');
            });

            // Hàm hỗ trợ cập nhật URL trình duyệt
            function updateUrl(query, startDate, endDate) {
                let newUrl = `${window.location.pathname}`;
                const params = new URLSearchParams();

                if (query) {
                    params.append('query', query);
                }
                if (startDate) {
                    params.append('start_date', startDate);
                }
                if (endDate) {
                    params.append('end_date', endDate);
                }

                if (params.toString()) {
                    newUrl += `?${params.toString()}`;
                }

                window.history.pushState({
                    path: newUrl
                }, '', newUrl);
            }
        });





        function fetchInventories(page, searchTerm = '', startDate = '', endDate = '') {
            let apiUrl = `http://127.0.0.1:8000/api/inventory?page=${page}`;

            if (searchTerm) {
                apiUrl += `&query=${encodeURIComponent(searchTerm)}`;
            }
            if (startDate) {
                apiUrl += `&start_date=${encodeURIComponent(startDate)}`;
            }
            if (endDate) {
                apiUrl += `&end_date=${encodeURIComponent(endDate)}`;
            }

            $.ajax({
                url: apiUrl,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if (response.status_code === 200) {
                        let data = response.data;
                        let tbody = $("table tbody");
                        tbody.empty();

                        if (data.length === 0) {
                            tbody.append(
                                `<tr><td colspan="9" class="text-center">Không tìm thấy phiếu nhập nào.</td></tr>`
                            );
                            $("#pagination").empty();
                            return;
                        }

                        $.each(data, function(index, inventory) {
                            let totalQuantity = 0;
                            let totalPrice = parseFloat(inventory.total_price);
                            let productsInfo = [];
                            let productImages = [];
                            let productPrices = []; // To store all prices for range calculation

                            // Iterate through each product group in the inventory slip
                            $.each(inventory.detail, function(i, productGroup) {
                                productsInfo.push(productGroup.product.name);
                                productImages.push(productGroup.product.image);

                                // Collect quantities and prices from variants
                                $.each(productGroup.variants, function(j, variant) {
                                    totalQuantity += parseInt(variant.quantity);
                                    productPrices.push(parseFloat(variant.price));
                                });
                            });

                            // --- CẬP NHẬT PHẦN HIỂN THỊ SẢN PHẨM VÀ HÌNH ẢNH TRÊN BẢNG CHÍNH ---

                            // Hiển thị tối đa 2 tên sản phẩm, còn lại dùng "(+X)"
                            let productsDisplay = '';
                            if (productsInfo.length > 0) {
                                productsDisplay += productsInfo[0];
                                if (productsInfo.length > 1) {
                                    productsDisplay +=
                                        `<br> <small class="text-muted">(+${productsInfo.length - 1} sản phẩm khác)</small>`;
                                }
                            } else {
                                productsDisplay = 'N/A';
                            }

                            // Hiển thị tối đa 2 hình ảnh, còn lại dùng icon
                            let imageDisplay = '';
                            if (productImages.length > 0) {
                                imageDisplay +=
                                    `<img src="${productImages[0]}" width="45" class="rounded" title="${productsInfo[0] || ''}">`;
                                if (productImages.length > 1) {
                                    imageDisplay +=
                                        `<img src="${productImages[1]}" width="45" class="rounded ms-1 d-none d-md-inline" title="${productsInfo[1] || ''}">`; // Hide on small screens
                                    if (productImages.length > 2) {
                                        imageDisplay +=
                                            `<span class="badge bg-secondary ms-1"> +${productImages.length - 2}</span>`;
                                    }
                                }
                            } else {
                                imageDisplay = 'N/A';
                            }

                            // Calculate price range for the entire inventory slip (from all product variants)
                            let priceDisplay = 'N/A';
                            if (productPrices.length > 0) {
                                let minPrice = Math.min(...productPrices);
                                let maxPrice = Math.max(...productPrices);
                                if (minPrice === maxPrice) {
                                    priceDisplay = minPrice.toLocaleString('vi-VN') + " đ";
                                } else {
                                    priceDisplay =
                                        `${minPrice.toLocaleString('vi-VN')} - ${maxPrice.toLocaleString('vi-VN')} đ`;
                                }
                            }

                            // (Giữ nguyên phần tính colors và sizes nếu bạn muốn hiển thị tổng quan ở cột Tổng số lượng)
                            let colors = new Set();
                            let sizes = new Set();
                            $.each(inventory.detail, function(i, productGroup) {
                                $.each(productGroup.variants, function(j, variant) {
                                    colors.add(variant.color);
                                    sizes.add(variant.size);
                                });
                            });


                            let row = `
                        <tr>
                            <td>${inventory.id}</td>
                            <td>${productsDisplay}</td>
                            <td>${imageDisplay}</td>
                            <td>${inventory.provider.name}</td>
                            <td>${inventory.staff ? inventory.staff.name : 'N/A'}</td>
                            <td>
                                <span class="badge bg-primary">${totalQuantity}</span>
                                <br><small class="text-muted">${colors.size} màu, ${sizes.size} size</small>
                            </td>
                            <td>${priceDisplay}</td>
                            <td><strong>${totalPrice.toLocaleString('vi-VN')} đ</strong></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                                    <button type="button" class="btn btn-secondary btn-sm btn-inventory-detail"
                                            data-inventory-id="${inventory.id}">
                                        <i class="fas fa-eye me-1"></i>Chi tiết
                                    </button>
                                    <form method="GET" action="{{ route('inventory.add_extra') }}" class="d-inline">
                                        <input type="hidden" name="inventory_id" value="${inventory.id}">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-plus me-1"></i>Nhập thêm
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    `;
                            tbody.append(row);
                        });

                        renderPagination(response.pagination, searchTerm, startDate, endDate);
                    } else {
                        console.error("Lỗi API:", response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Lỗi khi lấy dữ liệu:", xhr.responseText);
                }
            });
        }


        function renderPagination(pagination, searchTerm = '', startDate = '', endDate = '') {
            let paginationDiv = $("#pagination");
            paginationDiv.empty();

            const current = pagination.current_page;
            const last = pagination.last_page;

            function createPageButton(page, text = null, disabled = false, active = false) {
                let btnClass = "btn btn-primary btn-sm mx-1";
                if (disabled) btnClass += " disabled";
                if (active) btnClass += " active";

                let displayText = text || page;

                if (disabled) {
                    return `<button class="${btnClass}" disabled>${displayText}</button>`;
                } else {
                    return `<button class="${btnClass}" onclick="fetchInventories(${page}, '${encodeURIComponent(searchTerm)}', '${encodeURIComponent(startDate)}', '${encodeURIComponent(endDate)}')">${displayText}</button>`;
                }
            }

            // Nút prev
            paginationDiv.append(createPageButton(current - 1, "<", current <= 1));

            let delta = 2;
            let rangeStart = Math.max(1, current - delta);
            let rangeEnd = Math.min(last, current + delta);

            if (rangeStart > 1) {
                paginationDiv.append(createPageButton(1, 1));
                if (rangeStart > 2) {
                    paginationDiv.append('<span class="mx-1 align-self-center">...</span>');
                }
            }

            for (let i = rangeStart; i <= rangeEnd; i++) {
                paginationDiv.append(createPageButton(i, i, false, i === current));
            }

            if (rangeEnd < last) {
                if (rangeEnd < last - 1) {
                    paginationDiv.append('<span class="mx-1 align-self-center">...</span>');
                }
                paginationDiv.append(createPageButton(last, last));
            }

            // Nút next
            paginationDiv.append(createPageButton(current + 1, ">", current >= last));
        }
    </script>

    <script>
        @if ($errors->any())
            $(document).ready(function() {
                $('#inventoryDetail').addClass("open");
            })
        @endif
    </script>
    <script>
        // Cập nhật hàm xử lý modal chi tiết
        $(document).ready(function() {
            $("table").on("click", ".btn-inventory-detail", function(e) {
                e.preventDefault();
                let inventory_id = $(this).data('inventory-id');

                $.ajax({
                    url: `http://127.0.0.1:8000/api/inventoryDetail/${inventory_id}`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.status_code === 200) {
                            let inventory_detail = response.data;

                            // Thông tin cơ bản phiếu nhập
                            $('#inventory-id').text(inventory_detail.id);
                            $('#staff-name').text(inventory_detail.staff.name);
                            $('#provider-name').text(inventory_detail.provider.name);
                            $('#total_price').text(parseFloat(inventory_detail.total_price)
                                .toLocaleString('vi-VN') + " đ");
                            $('#iventory-created').text(new Date(inventory_detail.created_at)
                                .toLocaleDateString('vi-VN'));
                            $('#iventory-updated').text(new Date(inventory_detail.updated_at)
                                .toLocaleDateString('vi-VN'));
                            $('#inventory-vat').text(inventory_detail.vat);

                            // Hiển thị trạng thái
                            let statusText = '';
                            let statusClass = '';
                            if (inventory_detail.status === 1) {
                                statusText = 'Hoàn thành';
                                statusClass = 'bg-success';
                            } else if (inventory_detail.status === 0) {
                                statusText = 'Đang xử lý';
                                statusClass = 'bg-warning text-dark';
                            } else {
                                statusText = 'Không xác định';
                                statusClass = 'bg-secondary';
                            }
                            $('#inventory-status').text(statusText).removeClass().addClass(
                                `badge ${statusClass}`);


                            // Gọi hàm hiển thị chi tiết sản phẩm
                            displayProductDetailsInModal(inventory_detail.detail);

                            $("#inventoryDetail").modal("show");
                        } else {
                            alert("Không thể lấy dữ liệu chi tiết phiếu nhập!");
                        }
                    },
                    error: function() {
                        alert("Đã có lỗi xảy ra, vui lòng thử lại!");
                    }
                });
            });
        });


        function displayProductDetailsInModal(productDetails) {
            const productsListContainer = $('#products-list-container');
            productsListContainer.empty(); // Xóa nội dung cũ

            let totalQuantityAllProducts = 0;
            let allColors = new Set();
            let allSizes = new Set();
            let sizeMap = new Map();
            // Đã bỏ biến allVATPercentages

            if (productDetails.length === 0) {
                productsListContainer.html(
                    '<p class="text-muted text-center">Không có sản phẩm nào trong phiếu nhập này.</p>');
                // Reset summary fields if no products
                $('#total_quantity').text('0');
                $('#colors').text('N/A');
                $('#size_and_quantity').html('N/A');
                // Đã bỏ $('#inventory-vat').text('N/A');
                return;
            }

            productDetails.forEach(function(productGroup) {
                let productHtml = `
                    <div class="product-item mb-3 pb-3 border-bottom">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <img src="${productGroup.product.image.startsWith('http') ? '' : 'http://127.0.0.1:8000/'}${productGroup.product.image}"
                                     width="80" class="rounded border shadow-sm" alt="${productGroup.product.name}">
                            </div>
                            <div class="col">
                                <h6 class="mb-1 text-primary">${productGroup.product.name}</h6>
                                <div class="small">
                                    <strong>Thương hiệu:</strong> <span>${productGroup.product.brand || 'N/A'}</span><br>
                                    <strong>Danh mục:</strong> <span class="text-secondary">${productGroup.product.category.name || 'N/A'}</span><br>
                `;

                let productPrices = [];
                let productVariantsHtml = `<strong>Biến thể:</strong><ul class="list-unstyled mb-0">`;
                productGroup.variants.forEach(function(variant) {
                    totalQuantityAllProducts += parseInt(variant.quantity);
                    allColors.add(variant.color);
                    allSizes.add(variant.size);
                    productPrices.push(parseFloat(variant.price));

                    // For size & quantity summary at the bottom
                    let key = `${variant.size} (${variant.color})`;
                    sizeMap.set(key, (sizeMap.get(key) || 0) + parseInt(variant.quantity));

                    productVariantsHtml +=
                        `<li>- Màu: ${variant.color}, Size: ${variant.size}, SL: ${variant.quantity} (${formatCurrency(variant.price)}/sp)</li>`;
                });
                productVariantsHtml += `</ul>`;

                // Display price range for the current product
                if (productPrices.length > 0) {
                    let minPrice = Math.min(...productPrices);
                    let maxPrice = Math.max(...productPrices);
                    productHtml +=
                        `<strong>Giá nhập:</strong> <span class="text-warning fw-bold">${minPrice === maxPrice ? formatCurrency(minPrice) : `${formatCurrency(minPrice)} - ${formatCurrency(maxPrice)}`}</span><br>`;
                } else {
                    productHtml += `<strong>Giá nhập:</strong> <span class="text-warning fw-bold">N/A</span><br>`;
                }

                productHtml += `</div></div>${productVariantsHtml}</div>`; // Close col, row, and add variants HTML
                productsListContainer.append(productHtml);
            });

            // Cập nhật các trường tổng quan ở phần "Chi tiết nhập kho"
            $('#total_quantity').text(totalQuantityAllProducts);
            $('#colors').text(Array.from(allColors).join(', ') || 'N/A');

            let sizeAndQuantityHtml = '';
            if (sizeMap.size > 0) {
                sizeMap.forEach((quantity, sizeColor) => {
                    sizeAndQuantityHtml += `<div>${sizeColor}: ${quantity}</div>`;
                });
            } else {
                sizeAndQuantityHtml = 'N/A';
            }
            $('#size_and_quantity').html(sizeAndQuantityHtml);
        }



        // Function to format currency
        function formatCurrency(amount) {
            return parseFloat(amount).toLocaleString('vi-VN') + " đ";
        }

        function displayMultipleProductsDetail(productDetails) {
            let allProductImagesHtml = '';
            let allProductNamesHtml = '';
            let allProductBrandsHtml = '';
            let allUniqueCategoryNames = new Set(); // Dùng Set để lưu danh mục duy nhất
            let allProductPricesHtml = '';
            let totalQuantityAllProducts = 0;
            let allColors = new Set();
            let allSizes = new Set();
            let allVATPercentages = new Set(); // Dùng Set để lưu phần trăm VAT duy nhất

            productDetails.forEach(function(productGroup) {
                // Product Image
                if (productGroup.product.image) {
                    const imageUrl =
                        `${productGroup.product.image.startsWith('http') ? '' : 'http://127.0.0.1:8000/'}${productGroup.product.image}`;
                    allProductImagesHtml +=
                        `<img src="${imageUrl}" width="100" class="rounded border shadow-sm mb-2 me-2" alt="${productGroup.product.name}">`;
                }

                // Product Name
                allProductNamesHtml += `<span class="text-primary">${productGroup.product.name}</span><br>`;

                // Product Brand
                allProductBrandsHtml += `<span>${productGroup.product.brand}</span><br>`;

                // Category Name (Add to Set)
                if (productGroup.product.category && productGroup.product.category.name) {
                    allUniqueCategoryNames.add(productGroup.product.category.name);
                }

                // Price and Variants
                let productPrices = [];
                let productTotalQuantity = 0;
                productGroup.variants.forEach(function(variant) {
                    productTotalQuantity += parseInt(variant.quantity);
                    allColors.add(variant.color);
                    allSizes.add(variant.size);
                    productPrices.push(parseFloat(variant.price));
                    if (variant.vat_percentage !== undefined && variant.vat_percentage !== null) {
                        allVATPercentages.add(variant.vat_percentage); // Thu thập phần trăm VAT
                    }
                });

                totalQuantityAllProducts += productTotalQuantity;

                if (productPrices.length > 0) {
                    let minPrice = Math.min(...productPrices);
                    let maxPrice = Math.max(...productPrices);
                    if (minPrice === maxPrice) {
                        allProductPricesHtml +=
                            `<span class="text-warning fw-bold">${formatCurrency(minPrice)}</span><br>`;
                    } else {
                        allProductPricesHtml +=
                            `<span class="text-warning fw-bold">${formatCurrency(minPrice)} - ${formatCurrency(maxPrice)}</span><br>`;
                    }
                } else {
                    allProductPricesHtml += `<span class="text-warning fw-bold">N/A</span><br>`;
                }
            });

            // Cập nhật phần tử chứa ảnh
            $('#product-images-container').html(allProductImagesHtml || 'Không có ảnh');
            $('#product-name').html(allProductNamesHtml || 'N/A');
            $('#product-brand').html(allProductBrandsHtml || 'N/A');
            // Hiển thị danh mục duy nhất
            $('#category-name').html(Array.from(allUniqueCategoryNames).join('<br>') || 'N/A');
            $('#product-price').html(allProductPricesHtml || 'N/A');

            // Cập nhật chi tiết biến thể
            $('#total_quantity').text(totalQuantityAllProducts);
            $('#colors').text(Array.from(allColors).join(', ') || 'N/A');

            // Tổng hợp size và số lượng
            let sizeAndQuantityHtml = '';
            let sizeMap = new Map();
            productDetails.forEach(function(productGroup) {
                productGroup.variants.forEach(function(variant) {
                    let key = `${variant.size} (${variant.color})`;
                    sizeMap.set(key, (sizeMap.get(key) || 0) + parseInt(variant.quantity));
                });
            });

            sizeMap.forEach((quantity, sizeColor) => {
                sizeAndQuantityHtml += `<div>${sizeColor}: ${quantity}</div>`;
            });
            $('#size_and_quantity').html(sizeAndQuantityHtml || 'N/A');

            // Hiển thị thông tin VAT
            let vatDisplay = 'N/A';
            if (allVATPercentages.size > 0) {
                vatDisplay = Array.from(allVATPercentages).map(vat => `${vat}%`).join(', ');
            }
            $('#product-vat').text(vatDisplay);
        }

        // Hàm hiển thị chi tiết cho 1 sản phẩm (cập nhật để thêm VAT)
        function displaySingleProductDetail(productGroup) {
            const imageUrl =
                `${productGroup.product.image.startsWith('http') ? '' : 'http://127.0.0.1:8000/'}${productGroup.product.image}`;
            $('#product-images-container').html(`<img src="${imageUrl}" width="120" class="rounded border shadow-sm">`);
            $('#product-name').text(productGroup.product.name);
            $('#product-brand').text(productGroup.product.brand);
            $('#category-name').text(productGroup.product.category.name);
            $('#product-price').text(formatCurrency(productGroup.variants[0].price));

            // Hiển thị VAT cho 1 sản phẩm
            let singleProductVAT = 'N/A';
            if (productGroup.variants[0].vat_percentage !== undefined && productGroup.variants[0].vat_percentage !== null) {
                singleProductVAT = `${productGroup.variants[0].vat_percentage}%`;
            }
            $('#product-vat').text(singleProductVAT);

            let totalQuantity = 0;
            let colors = new Set();
            let sizeAndQuantityHtml = '';

            productGroup.variants.forEach(function(variant) {
                totalQuantity += parseInt(variant.quantity);
                colors.add(variant.color);
                sizeAndQuantityHtml += `<div>${variant.size} (${variant.color}): ${variant.quantity}</div>`;
            });

            $('#total_quantity').text(totalQuantity);
            $('#colors').text(Array.from(colors).join(', ') || 'N/A');
            $('#size_and_quantity').html(sizeAndQuantityHtml || 'N/A');
        }


        // Cập nhật hàm xử lý modal chi tiết để truyền cả inventory_detail.status
        $(document).ready(function() {
            $("table").on("click", ".btn-inventory-detail", function(e) {
                e.preventDefault();
                let inventory_id = $(this).data('inventory-id');

                $.ajax({
                    url: `http://127.0.0.1:8000/api/inventoryDetail/${inventory_id}`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.status_code === 200) {
                            let inventory_detail = response.data;
                            console.log(inventory_detail);

                            // Thông tin cơ bản
                            $('#inventory-id').text(inventory_detail.id);
                            $('#staff-name').text(inventory_detail.staff.name);
                            $('#provider-name').text(inventory_detail.provider.name);
                            $('#total_price').text(parseFloat(inventory_detail.total_price)
                                .toLocaleString('vi-VN') + " đ");
                            $('#iventory-created').text(new Date(inventory_detail.createdate)
                                .toLocaleDateString('vi-VN')); // Sử dụng created_at
                            $('#iventory-updated').text(new Date(inventory_detail.updatedate)
                                .toLocaleDateString('vi-VN')); // Sử dụng updated_at
                            $('#inventory-status').text(inventory_detail
                                .status); // Cập nhật trạng thái

                            let inventoryVAT = 'N/A';
                            if (inventory_detail.vat !== undefined && inventory_detail.vat !==
                                null) {
                                inventoryVAT = formatCurrency(inventory_detail
                                    .vat); // Nếu VAT là số tiền
                                // Hoặc: inventoryVAT = `${inventory_detail.vat}%`; // Nếu VAT là %
                            }
                            $('#inventory-vat').text(inventoryVAT);
                            if (inventory_detail.detail.length > 0) {
                                if (inventory_detail.detail.length === 1) {
                                    displaySingleProductDetail(inventory_detail.detail[0]);
                                } else {
                                    displayMultipleProductsDetail(inventory_detail.detail);
                                }
                            } else {
                                // Xử lý trường hợp không có sản phẩm nào trong phiếu nhập
                                $('#product-images-container').html('Không có ảnh sản phẩm.');
                                $('#product-name').text('N/A');
                                $('#product-brand').text('N/A');
                                $('#category-name').text('N/A');
                                $('#product-price').text('N/A');
                                $('#product-vat').text('N/A');
                                $('#total_quantity').text(0);
                                $('#colors').text('N/A');
                                $('#size_and_quantity').html('N/A');
                            }

                            $("#inventoryDetail").modal("show");
                        } else {
                            alert("Không thể lấy dữ liệu chi tiết!");
                        }
                    },
                    error: function() {
                        alert("Đã có lỗi xảy ra, vui lòng thử lại!");
                    }
                });
            });





            // Print Inventory to PDF
            $(document).on('click', '#print-inventory-btn', function() {
                // Get the inventory ID from the modal
                const inventoryId = $('#inventory-id').text();

                // Show loading indicator
                $(this).html('<i class="fas fa-spinner fa-spin me-1"></i> Đang tạo PDF...').prop('disabled',
                    true);

                // Send request to server
                $.ajax({
                    url: '/inventory/generatePDF/' + inventoryId,
                    type: 'GET',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(response) {
                        // Create download link
                        const blob = new Blob([response], {
                            type: 'application/pdf'
                        });
                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = 'PhieuNhap_' + inventoryId + '.pdf';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        // Reset button
                        $('#print-inventory-btn').html(
                            '<i class="fas fa-print me-1"></i> In phiếu nhập').prop(
                            'disabled', false);
                    },
                    error: function(xhr) {
                        console.error('Error generating PDF:', xhr);
                        alert('Có lỗi xảy ra khi tạo PDF. Vui lòng thử lại.');
                        $('#print-inventory-btn').html(
                            '<i class="fas fa-print me-1"></i> In phiếu nhập').prop(
                            'disabled', false);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.btn-add-extra').click(function(e) {
                e.preventDefault();
                let row = $(this).closest("tr");
                let promoId = row.find("td:first").text().trim();
                window.location.href = "/" + promoId;
            });
        });
    </script>
@endsection
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

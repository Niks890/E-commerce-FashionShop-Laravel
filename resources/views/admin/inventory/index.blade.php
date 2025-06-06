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
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Bộ lọc phiếu nhập</h5>
        </div>
        <div class="card-body">
            <form id="search-form" method="GET" action="{{ route('inventory.index') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="search-input" class="form-label small text-muted">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="query" id="search-input" class="form-control"
                            placeholder="Nhập tên nhân viên, ID phiếu nhập hoặc tên sản phẩm..."
                            value="{{ request('query') }}" />
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="start-date-input" class="form-label small text-muted">Ngày bắt đầu</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="date" name="start_date" id="start-date-input" class="form-control"
                                    value="{{ request('start_date') }}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="end-date-input" class="form-label small text-muted">Ngày kết thúc</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="date" name="end_date" id="end-date-input" class="form-control"
                                    value="{{ request('end_date') }}" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="status-filter" class="form-label small text-muted">Trạng thái</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                        <select name="status" id="status-filter" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt
                            </option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt
                            </option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Đã huỷ</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-9 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-danger" id="clear-filters-button">
                            <i class="fas fa-eraser me-1"></i> Xóa bộ lọc
                        </button>
                        <a href="{{ route('admin.revenueInventory') }}" class="btn btn-outline-warning">
                            <i class="fas fa-warehouse me-1"></i> Quản lý kho
                        </a>
                        <a href="{{ route('inventory.create') }}" class="btn btn-success">
                            <i class="fas fa-plus-circle me-1"></i> Tạo phiếu nhập
                        </a>
                    </div>
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
                            <th>Trạng thái</th>
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
                                        <div class="col-12">
                                            <strong>Ghi chú:</strong>
                                            <span id="inventory-note" class="badge bg-info"></span>
                                        </div>


                                        <div class="col-12">
                                            <strong>VAT:</strong>
                                            <span id="inventory-vat" class="text-danger fw-bold"></span>
                                        </div>
                                        <div class="col-12 mt-3">
                                            <strong>Tổng tiền:</strong>
                                            <span id="total_price" class="text-danger fs-5 fw-bold"></span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

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



    {{-- Modal xác nhận duyệt --}}
    <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Xác nhận duyệt phiếu nhập</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn duyệt phiếu nhập này?</p>
                    <p class="text-danger"><strong>Lưu ý:</strong> Sau khi duyệt, phiếu nhập sẽ không thể chỉnh sửa.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <form id="approveForm" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-1"></i> Xác nhận duyệt
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal xác nhận huỷ --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Xác nhận huỷ phiếu nhập</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn huỷ phiếu nhập này?</p>
                    <p class="text-danger"><strong>Lưu ý:</strong> Sản phẩm trong phiếu nhập sẽ được chuyển sang trạng thái
                        ẩn.</p>

                    <div class="mb-3">
                        <label for="rejectReason" class="form-label">Lý do huỷ <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejectReason" rows="3" required placeholder="Nhập lý do huỷ phiếu nhập..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <form id="rejectForm" method="POST">
                        @csrf
                        <input type="hidden" name="note" id="rejectNoteInput">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times me-1"></i> Xác nhận huỷ
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
    <style>
        /* Custom styles for the filter form */
        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, .125);
        }

        .form-label {
            font-size: 0.85rem;
            margin-bottom: 0.25rem;
        }

        .input-group-text {
            background-color: #f8f9fa;
        }

        .table th {
            white-space: nowrap;
        }

        .badge {
            font-size: 0.85em;
            padding: 0.4em 0.65em;
        }

        /* Status badges */
        .badge-pending {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-approved {
            background-color: #198754;
        }

        .badge-rejected {
            background-color: #dc3545;
        }

        /* Action buttons */
        .btn-action-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
@endsection

@section('js')
    @if (Session::has('success'))
        <script src="{{ asset('assets/js/message.js') }}"></script>
    @endif

    <script>
        // Xử lý nút duyệt
        $(document).on('click', '.btn-approve', function() {
            const inventoryId = $(this).data('inventory-id');
            $('#approveForm').attr('action', `/inventory/${inventoryId}/approve`);
            $('#approveModal').modal('show');
        });

        // Xử lý nút huỷ
        $(document).on('click', '.btn-reject', function() {
            const inventoryId = $(this).data('inventory-id');
            $('#rejectForm').attr('action', `/inventory/${inventoryId}/reject`);
            $('#rejectModal').modal('show');
        });

        // Khi submit form huỷ, thêm lý do vào input ẩn
        $('#rejectForm').on('submit', function(e) {
            const reason = $('#rejectReason').val().trim();
            if (!reason) {
                e.preventDefault();
                alert('Vui lòng nhập lý do huỷ');
                return;
            }
            $('#rejectNoteInput').val(reason);
        });


        $(document).ready(function() {
            // Lấy query và ngày từ URL khi tải trang lần đầu (nếu có)
            const urlParams = new URLSearchParams(window.location.search);
            const initialQuery = urlParams.get('query') || '';
            const initialStartDate = urlParams.get('start_date') || '';
            const initialEndDate = urlParams.get('end_date') || '';
            const initialStatus = urlParams.get('status') || '';

            $('#search-input').val(initialQuery);
            $('#start-date-input').val(initialStartDate);
            $('#end-date-input').val(initialEndDate);
            $('#status-filter').val(initialStatus);

            // Gọi hàm fetchInventories lần đầu khi tải trang
            fetchInventories(1, initialQuery, initialStartDate, initialEndDate, initialStatus);

            // Xử lý sự kiện khi submit form tìm kiếm
            $('#search-form').on('submit', function(e) {
                e.preventDefault();
                const searchTerm = $('#search-input').val().trim();
                const startDate = $('#start-date-input').val();
                const endDate = $('#end-date-input').val();
                const status = $('#status-filter').val();

                updateUrl(searchTerm, startDate, endDate, status);
                fetchInventories(1, searchTerm, startDate, endDate, status);
            });

            // Xử lý sự kiện khi thay đổi giá trị của ô input ngày hoặc trạng thái
            $('#start-date-input, #end-date-input, #status-filter').on('change', function() {
                const searchTerm = $('#search-input').val().trim();
                const startDate = $('#start-date-input').val();
                const endDate = $('#end-date-input').val();
                const status = $('#status-filter').val();

                updateUrl(searchTerm, startDate, endDate, status);
                fetchInventories(1, searchTerm, startDate, endDate, status);
            });

            // Xử lý sự kiện khi bấm nút "Xóa bộ lọc"
            $('#clear-filters-button').on('click', function() {
                $('#search-input').val('');
                $('#start-date-input').val('');
                $('#end-date-input').val('');
                $('#status-filter').val('');

                updateUrl('', '', '', '');
                fetchInventories(1, '', '', '', '');
            });

            // Hàm hỗ trợ cập nhật URL trình duyệt
            function updateUrl(query, startDate, endDate, status) {
                let newUrl = `${window.location.pathname}`;
                const params = new URLSearchParams();

                if (query && query.trim() !== '') params.append('query', query.trim());
                if (startDate) params.append('start_date', startDate);
                if (endDate) params.append('end_date', endDate);
                if (status) params.append('status', status);

                if (params.toString()) {
                    newUrl += `?${params.toString()}`;
                }

                window.history.pushState({
                    path: newUrl
                }, '', newUrl);
            }
        });

        function fetchInventories(page, searchTerm = '', startDate = '', endDate = '', status = '') {
            // Làm sạch searchTerm
            searchTerm = searchTerm ? searchTerm.trim() : '';

            let apiUrl = `http://127.0.0.1:8000/api/inventory?page=${page}`;

            // Chỉ thêm param nếu có giá trị
            if (searchTerm && searchTerm !== '') {
                apiUrl += `&query=${encodeURIComponent(searchTerm)}`;
            }
            if (startDate && startDate !== '') {
                apiUrl += `&start_date=${encodeURIComponent(startDate)}`;
            }
            if (endDate && endDate !== '') {
                apiUrl += `&end_date=${encodeURIComponent(endDate)}`;
            }
            if (status && status !== '') {
                apiUrl += `&status=${encodeURIComponent(status)}`;
            }

            console.log('API URL:', apiUrl); // Debug log

            $.ajax({
                url: apiUrl,
                type: "GET",
                dataType: "json",
                beforeSend: function() {
                    // Hiển thị loading
                    $("table tbody").html(
                        '<tr><td colspan="10" class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải...</td></tr>'
                    );
                },
                success: function(response) {
                    console.log('API Response:', response); // Debug log

                    if (response.status_code === 200) {
                        let data = response.data;
                        let tbody = $("table tbody");
                        tbody.empty();

                        if (data.length === 0) {
                            tbody.append(
                                `<tr><td colspan="10" class="text-center">Không tìm thấy phiếu nhập nào.</td></tr>`
                            );
                            $("#pagination").empty();
                            return;
                        }

                        $.each(data, function(index, inventory) {
                            let totalQuantity = 0;
                            let totalPrice = parseFloat(inventory.total_price);
                            let productsInfo = [];
                            let productImages = [];
                            let productPrices = [];

                            // Xử lý thông tin sản phẩm
                            if (inventory.detail && inventory.detail.length > 0) {
                                $.each(inventory.detail, function(i, productGroup) {
                                    if (productGroup.product) {
                                        productsInfo.push(productGroup.product.name);
                                        productImages.push(productGroup.product.image);
                                    }

                                    // Tính toán số lượng và giá
                                    if (productGroup.variants && productGroup.variants.length >
                                        0) {
                                        $.each(productGroup.variants, function(j, variant) {
                                            totalQuantity += parseInt(variant
                                                .quantity || 0);
                                            productPrices.push(parseFloat(variant
                                                .price || 0));
                                        });
                                    }
                                });
                            }

                            // Tạo chuỗi hiển thị sản phẩm
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

                            // Tạo chuỗi hiển thị hình ảnh
                            let imageDisplay = '';
                            if (productImages.length > 0 && productImages[0]) {
                                imageDisplay +=
                                    `<img src="${productImages[0]}" width="45" class="rounded" title="${productsInfo[0] || ''}">`;
                                if (productImages.length > 1 && productImages[1]) {
                                    imageDisplay +=
                                        `<img src="${productImages[1]}" width="45" class="rounded ms-1 d-none d-md-inline" title="${productsInfo[1] || ''}">`;
                                    if (productImages.length > 2) {
                                        imageDisplay +=
                                            `<span class="badge bg-secondary ms-1"> +${productImages.length - 2}</span>`;
                                    }
                                }
                            } else {
                                imageDisplay = 'N/A';
                            }

                            // Tính toán giá hiển thị
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

                            // Xử lý màu sắc và kích thước
                            let colors = new Set();
                            let sizes = new Set();
                            if (inventory.detail && inventory.detail.length > 0) {
                                $.each(inventory.detail, function(i, productGroup) {
                                    if (productGroup.variants && productGroup.variants.length >
                                        0) {
                                        $.each(productGroup.variants, function(j, variant) {
                                            if (variant.color) colors.add(variant
                                                .color);
                                            if (variant.size) sizes.add(variant.size);
                                        });
                                    }
                                });
                            }

                            // Xử lý trạng thái
                            let statusBadge = '';
                            switch (inventory.status) {
                                case 'pending':
                                    statusBadge = '<span class="badge badge-pending">Chờ duyệt</span>';
                                    break;
                                case 'approved':
                                    statusBadge = '<span class="badge badge-approved">Đã duyệt</span>';
                                    break;
                                case 'rejected':
                                    statusBadge = '<span class="badge badge-rejected">Đã huỷ</span>';
                                    break;
                                default:
                                    statusBadge = '<span class="badge bg-secondary">N/A</span>';
                            }

                            // Tạo nút hành động
                            actionButtons = `
                                <div class="btn-action-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-inventory-detail"
                                            data-inventory-id="${inventory.id}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                            `;

                            @can('warehouse workers')
                                if (inventory.status === 'pending') {
                                    actionButtons += `
                                    <button type="button" class="btn btn-sm btn-success btn-approve"
                                            data-inventory-id="${inventory.id}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger btn-reject"
                                            data-inventory-id="${inventory.id}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                `;
                                }
                            @endcan

                            // Thêm nút nhập thêm nếu phiếu đã được duyệt
                            if (inventory.status === 'approved') {
                                actionButtons += `
                                    <form method="GET" action="{{ route('inventory.add_extra') }}" class="d-inline">
                                        <input type="hidden" name="inventory_id" value="${inventory.id}">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </form>
                                `;
                            }

                            actionButtons += `</div>`;

                            let row = `
                                <tr>
                                    <td>${inventory.id}</td>
                                    <td>${productsDisplay}</td>
                                    <td>${imageDisplay}</td>
                                    <td>${inventory.provider && inventory.provider.name ? inventory.provider.name : 'N/A'}</td>
                                    <td>${inventory.staff && inventory.staff.name ? inventory.staff.name : 'N/A'}</td>
                                    <td>
                                        <span class="badge bg-primary">${totalQuantity}</span>
                                        <br><small class="text-muted">${colors.size} màu, ${sizes.size} size</small>
                                    </td>
                                    <td>${priceDisplay}</td>
                                    <td><strong>${totalPrice.toLocaleString('vi-VN')} đ</strong></td>
                                    <td>${statusBadge}</td>
                                    <td>${actionButtons}</td>
                                </tr>
                            `;
                            tbody.append(row);
                        });

                        // Gọi renderPagination với đầy đủ tham số
                        renderPagination(response.pagination, searchTerm, startDate, endDate, status);
                    } else {
                        console.error("Lỗi API:", response.message);
                        $("table tbody").html('<tr><td colspan="10" class="text-center text-danger">Lỗi: ' + (
                            response.message || 'Không thể tải dữ liệu') + '</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Lỗi khi lấy dữ liệu:", xhr.responseText);
                    let errorMessage = 'Đã có lỗi xảy ra';
                    try {
                        let errorResponse = JSON.parse(xhr.responseText);
                        errorMessage = errorResponse.message || errorMessage;
                    } catch (e) {
                        // Ignore JSON parse error
                    }
                    $("table tbody").html('<tr><td colspan="10" class="text-center text-danger">Lỗi: ' +
                        errorMessage + '</td></tr>');
                }
            });
        }

        // Sửa hàm renderPagination để nhận đầy đủ tham số
        function renderPagination(pagination, searchTerm = '', startDate = '', endDate = '', status = '') {
            let paginationDiv = $("#pagination");
            paginationDiv.empty();

            if (!pagination || !pagination.last_page || pagination.last_page <= 1) {
                return; // Không hiển thị pagination nếu chỉ có 1 trang
            }

            const current = pagination.current_page;
            const last = pagination.last_page;

            function createPageButton(page, text = null, disabled = false, active = false) {
                let btnClass = "btn btn-outline-primary btn-sm mx-1";
                if (disabled) btnClass += " disabled";
                if (active) btnClass += " active";

                let displayText = text || page;

                if (disabled) {
                    return `<button class="${btnClass}" disabled>${displayText}</button>`;
                } else {
                    // Escape các tham số để tránh lỗi XSS
                    let escapedSearchTerm = (searchTerm || '').replace(/'/g, "\\'");
                    let escapedStartDate = (startDate || '').replace(/'/g, "\\'");
                    let escapedEndDate = (endDate || '').replace(/'/g, "\\'");
                    let escapedStatus = (status || '').replace(/'/g, "\\'");

                    return `<button class="${btnClass}" onclick="fetchInventories(${page}, '${escapedSearchTerm}', '${escapedStartDate}', '${escapedEndDate}', '${escapedStatus}')">${displayText}</button>`;
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
                            $('#iventory-created').text(new Date(inventory_detail.createdate)
                                .toLocaleDateString('vi-VN'));
                            $('#iventory-updated').text(new Date(inventory_detail.updatedate)
                                .toLocaleDateString('vi-VN'));
                            $('#inventory-vat').text(parseFloat(inventory_detail.vat)
                                .toLocaleString('vi-VN') + " đ");
                            $('#inventory-note').text(inventory_detail.note);

                            // Hiển thị trạng thái
                            let statusText = '';
                            let statusClass = '';
                            if (inventory_detail.status === 'approved') {
                                statusText = 'Đã duyệt';
                                statusClass = 'badge-approved';
                            } else if (inventory_detail.status === 'pending') {
                                statusText = 'Chờ duyệt';
                                statusClass = 'badge-pending';
                            } else {
                                statusText = 'Đã huỷ';
                                statusClass = 'bg-danger';
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

        function displayProductDetailsInModal(productDetails) {
            const productsListContainer = $('#products-list-container');
            productsListContainer.empty(); // Xóa nội dung cũ

            let totalQuantityAllProducts = 0;
            let allColors = new Set();
            let allSizes = new Set();
            let sizeMap = new Map();

            if (productDetails.length === 0) {
                productsListContainer.html(
                    '<p class="text-muted text-center">Không có sản phẩm nào trong phiếu nhập này.</p>');
                // Reset summary fields if no products
                $('#total_quantity').text('0');
                $('#colors').text('N/A');
                $('#size_and_quantity').html('N/A');
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
    </script>
@endsection

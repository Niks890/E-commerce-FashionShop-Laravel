@can('salers')
    @extends('admin.master')
    @section('title', 'Quản lý Đơn hàng đang giao')

@section('content')
    <div id="root" class="container mx-auto max-w-7xl px-4 py-6">
        <div id="success-message"
            class="hidden alert alert-success d-flex align-items-center shadow-sm py-2 px-3 mb-4 rounded-lg js-div-dissappear"
            role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <span id="success-text"></span>
        </div>

        <div class="card shadow-sm rounded-xl">
            <div class="card-body p-4 p-md-5">
                <h2 class="card-title text-2xl font-bold mb-4 text-gray-800">Quản lý giao hàng</h2>

                <form id="search-form" class="mb-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-8">
                            <div class="input-group shadow-sm-sm">
                                <input name="query" type="text" class="form-control rounded-s-lg"
                                    placeholder="Nhập ID đơn hàng hoặc số điện thoại để tìm kiếm..." value="">
                                <button class="btn btn-primary rounded-e-lg" type="submit" aria-label="Tìm kiếm">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select name="status_filter" class="form-select shadow-sm-sm rounded-lg"
                                onchange="document.getElementById('search-form').submit()">
                                <option value="">Tất cả trạng thái</option>
                                <option value="Đã xử lý">Đã xử lý</option>
                                <option value="Đang giao hàng">Đang giao hàng</option>
                                <option value="Giao thành công">Giao thành công</option>
                                <option value="Giao thất bại">Giao thất bại</option>
                            </select>
                        </div>
                    </div>
                </form>

                <div class="mb-4">
                    <h6 class="text-muted mb-2">Lọc nhanh:</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="#" data-status="" class="badge bg-primary text-decoration-none p-2 filter-badge">
                            <i class="fa fa-list me-1"></i>Tất cả
                            <span class="badge bg-secondary ms-1" id="total-count">0</span>
                        </a>
                        <a href="#" data-status="Đã xử lý"
                            class="badge bg-light text-dark text-decoration-none p-2 filter-badge">
                            <i class="fa fa-check me-1"></i>Đã xử lý
                            <span class="badge bg-secondary ms-1" id="processed-count">0</span>
                        </a>
                        <a href="#" data-status="Đang giao hàng"
                            class="badge bg-light text-dark text-decoration-none p-2 filter-badge">
                            <i class="fa fa-truck me-1"></i>Đang giao
                            <span class="badge bg-secondary ms-1" id="shipping-count">0</span>
                        </a>
                        <a href="#" data-status="Giao thành công"
                            class="badge bg-light text-dark text-decoration-none p-2 filter-badge">
                            <i class="fa fa-thumbs-up me-1"></i>Thành công
                            <span class="badge bg-secondary ms-1" id="success-count">0</span>
                        </a>
                        <a href="#" data-status="Giao thất bại"
                            class="badge bg-light text-dark text-decoration-none p-2 filter-badge">
                            <i class="fa fa-times-circle me-1"></i>Thất bại
                            <span class="badge bg-secondary ms-1" id="failed-count">0</span>
                        </a>
                    </div>
                </div>

                <div id="filter-info" class="hidden alert alert-info d-flex align-items-center mb-3 rounded-lg">
                    <i class="fa fa-info-circle me-2"></i>
                    <span>
                        Đang hiển thị: <strong id="current-status-filter">Tất cả trạng thái</strong>
                        <span id="current-query-filter" class="hidden"> | Tìm kiếm: <strong>""</strong></span>
                        | Tổng: <strong id="current-total-orders">0</strong> đơn hàng
                    </span>
                    <button type="button" id="clear-filters-btn" class="btn btn-sm btn-outline-secondary ms-auto rounded-md">
                        <i class="fa fa-times me-1"></i>Xóa bộ lọc
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-uppercase text-secondary">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Tên khách hàng</th>
                                <th scope="col">Địa chỉ</th>
                                <th scope="col">Số điện thoại</th>
                                <th scope="col">Tổng tiền</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Ngày tạo</th>
                                <th scope="col" class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="order-table-body">
                            </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Pagination">
                        <ul class="pagination">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="assignShipperModal" tabindex="-1" aria-labelledby="assignShipperModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="assignShipperForm">
                <input type="hidden" name="order_id" id="modalOrderId" value="">
                <div class="modal-content rounded-xl shadow-lg">
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignShipperModalLabel">Chọn nhân viên giao hàng</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="shipperSelect" class="form-label">Nhân viên giao hàng</label>
                            <select id="shipperSelect" name="shipper_id" class="form-select rounded-md" required>
                                <option value="" selected disabled>Chọn nhân viên</option>
                                <option value="1">Nguyễn Văn A</option>
                                <option value="2">Trần Thị B</option>
                                <option value="3">Lê Văn C</option>
                            </select>
                        </div>
                        <div id="shipperOrderCount" class="alert alert-info d-none rounded-md">
                            <strong>Số đơn đang giao: </strong> <span id="orderCountNumber">0</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary rounded-md">Xác nhận</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="deliverySuccessModal" tabindex="-1" aria-labelledby="deliverySuccessModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-xl shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="deliverySuccessModalLabel">Xác nhận giao hàng thành công</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-4">Bạn có chắc chắn muốn cập nhật trạng thái đơn hàng <strong
                            id="successOrderId"></strong> thành "Giao thành công"?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-success rounded-md" id="confirmDeliverySuccess">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deliveryFailedModal" tabindex="-1" aria-labelledby="deliveryFailedModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-xl shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="deliveryFailedModalLabel">Xác nhận giao hàng thất bại</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-4">Bạn có chắc chắn muốn cập nhật trạng thái đơn hàng <strong
                            id="failedOrderId"></strong> thành "Giao thất bại"?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger rounded-md" id="confirmDeliveryFailed">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customAlertModal" tabindex="-1" aria-labelledby="customAlertModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-xl shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="customAlertModalLabel">Thông báo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body" id="customAlertBody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary rounded-md" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        :root {
            --primary-color: #007bff; /* Bootstrap primary color */
            --success-color: #28a745; /* Bootstrap success color */
            --info-color: #17a2b8; /* Bootstrap info color */
            --danger-color: #dc3545; /* Bootstrap danger color */
            --secondary-color: #6c757d; /* Bootstrap secondary color */
            --light-color: #f8f9fa; /* Bootstrap light color */
            --dark-color: #343a40; /* Bootstrap dark color */
            --border-radius-base: 0.75rem; /* 12px */
            --border-radius-sm: 0.5rem; /* 8px */
            --border-radius-pill: 9999px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* General styling for improved aesthetics */
        .card {
            border-radius: var(--border-radius-base);
            border: none; /* Remove default border */
        }

        .card-body {
            padding: 1.5rem; /* Slightly more padding */
        }

        .btn {
            border-radius: var(--border-radius-sm);
            font-weight: 500;
        }

        .form-control,
        .form-select {
            border-radius: var(--border-radius-sm);
        }

        .input-group > .form-control,
        .input-group > .btn {
            border-radius: 0 !important; /* Reset border-radius for input group elements */
        }

        .input-group > .form-control.rounded-s-lg {
            border-top-left-radius: var(--border-radius-sm) !important;
            border-bottom-left-radius: var(--border-radius-sm) !important;
        }

        .input-group > .btn.rounded-e-lg {
            border-top-right-radius: var(--border-radius-sm) !important;
            border-bottom-right-radius: var(--border-radius-sm) !important;
        }


        .badge {
            border-radius: var(--border-radius-pill);
            padding: 0.4rem 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            text-transform: capitalize; /* Capitalize first letter of status */
        }

        .badge i {
            font-size: 0.8em;
        }

        .alert {
            border-radius: var(--border-radius-sm);
        }

        /* Specific badge colors for clarity */
        .badge.bg-success { background-color: var(--success-color) !important; color: white; }
        .badge.bg-info { background-color: var(--info-color) !important; color: white; }
        .badge.bg-primary { background-color: var(--primary-color) !important; color: white; }
        .badge.bg-danger { background-color: var(--danger-color) !important; color: white; }
        .badge.bg-secondary { background-color: var(--secondary-color) !important; color: white; }
        .badge.bg-light { background-color: var(--light-color) !important; color: var(--dark-color); border: 1px solid #dee2e6; } /* Add border for light badges */

        /* Table styling */
        .table {
            --bs-table-hover-bg: #f5f5f5; /* Light hover effect */
        }

        .table thead th {
            border-bottom: 2px solid #e2e8f0; /* Stronger header border */
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody td {
            padding: 0.75rem 1rem;
        }

        /* Responsive table styles */
        @media (max-width: 767px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .card-body {
                padding: 1rem;
            }

            table thead {
                display: none;
            }

            table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #e5e7eb;
                border-radius: var(--border-radius-sm);
                padding: 1rem;
                background-color: #ffffff;
                box-shadow: var(--shadow-sm);
            }

            table tbody tr td {
                display: flex;
                justify-content: space-between;
                padding: 0.5rem 0;
                border-bottom: 1px solid #f3f4f6;
            }

            table tbody tr td:last-child {
                border-bottom: none;
                flex-direction: column;
                align-items: center;
                padding-top: 0.75rem;
                gap: 0.5rem; /* Spacing between buttons on small screens */
            }

            table tbody tr td::before {
                content: attr(data-label) ": ";
                font-weight: 600;
                color: #6b7280;
                min-width: 120px;
            }

            .table-responsive {
                overflow-x: hidden;
            }

            .modal-dialog {
                margin: 0.5rem;
            }

            .d-flex.flex-wrap.gap-2 {
                gap: 0.5rem !important;
            }

            .badge.text-decoration-none {
                font-size: 0.7rem; /* Smaller font size for badges on mobile */
                padding: 0.4rem 0.6rem !important;
            }
        }

        /* Animation for success message */
        .js-div-dissappear {
            animation: fadeOut 0.5s ease-out 2.5s forwards;
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-10px);
                display: none;
            }
        }
    </style>
@endsection

@section('js')
    <script>
        // Dummy data for orders
        let orders = [{
                id: 'ORD001',
                customer_name: 'Nguyễn Văn An',
                address: '123 Đường ABC, Quận 1, TP.HCM',
                phone: '0901234567',
                total: 1500000,
                status: 'Đã xử lý',
                created_at: '2025-05-20 10:00:00'
            },
            {
                id: 'ORD002',
                customer_name: 'Trần Thị Bình',
                address: '456 Đường XYZ, Quận 3, TP.HCM',
                phone: '0902345678',
                total: 230000,
                status: 'Đang giao hàng',
                created_at: '2025-05-19 14:30:00'
            },
            {
                id: 'ORD003',
                customer_name: 'Lê Văn Chính',
                address: '789 Đường DEF, Quận 5, TP.HCM',
                phone: '0903456789',
                total: 850000,
                status: 'Giao thành công',
                created_at: '2025-05-18 09:15:00'
            },
            {
                id: 'ORD004',
                customer_name: 'Phạm Thị Dung',
                address: '101 Đường GHI, Quận 7, TP.HCM',
                phone: '0904567890',
                total: 420000,
                status: 'Đã xử lý',
                created_at: '2025-05-17 11:45:00'
            },
            {
                id: 'ORD005',
                customer_name: 'Hoàng Văn Em',
                address: '202 Đường JKL, Quận 10, TP.HCM',
                phone: '0905678901',
                total: 700000,
                status: 'Đang giao hàng',
                created_at: '2025-05-16 16:00:00'
            },
            {
                id: 'ORD006',
                customer_name: 'Nguyễn Thị Hương',
                address: '303 Đường MNO, Quận Bình Thạnh, TP.HCM',
                phone: '0906789012',
                total: 120000,
                status: 'Giao thất bại',
                created_at: '2025-05-15 08:00:00'
            },
            {
                id: 'ORD007',
                customer_name: 'Vũ Văn Khang',
                address: '404 Đường PQR, Quận Gò Vấp, TP.HCM',
                phone: '0907890123',
                total: 980000,
                status: 'Đã xử lý',
                created_at: '2025-05-14 13:00:00'
            },
            {
                id: 'ORD008',
                customer_name: 'Đinh Thị Lan',
                address: '505 Đường STU, Quận Tân Bình, TP.HCM',
                phone: '0908901234',
                total: 300000,
                status: 'Đang giao hàng',
                created_at: '2025-05-13 17:00:00'
            },
            {
                id: 'ORD009',
                customer_name: 'Bùi Văn Minh',
                address: '606 Đường VWX, Quận Phú Nhuận, TP.HCM',
                phone: '0909012345',
                total: 650000,
                status: 'Giao thành công',
                created_at: '2025-05-12 10:30:00'
            },
            {
                id: 'ORD010',
                customer_name: 'Đỗ Thị Nga',
                address: '707 Đường YZA, Quận Thủ Đức, TP.HCM',
                phone: '0910123456',
                total: 280000,
                status: 'Đã xử lý',
                created_at: '2025-05-11 09:00:00'
            }
        ];

        // Dummy data for shipper order counts
        const shipperData = {
            1: 3, // Nguyễn Văn A đang giao 3 đơn
            2: 5, // Trần Thị B đang giao 5 đơn
            3: 1, // Lê Văn C đang giao 1 đơn
        };

        // Function to format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(amount);
        }

        // Function to display success message
        function showSuccessMessage(message) {
            const successMessageDiv = document.getElementById('success-message');
            const successTextSpan = document.getElementById('success-text');
            successTextSpan.textContent = message;
            successMessageDiv.classList.remove('hidden');
            successMessageDiv.classList.add('js-div-dissappear'); // Add animation class
            setTimeout(() => {
                successMessageDiv.classList.add('hidden');
                successMessageDiv.classList.remove('js-div-dissappear'); // Remove animation class for next use
            }, 3000); // Hide after 3 seconds
        }

        // Function to render orders into the table
        function renderOrders(filteredOrders) {
            const tableBody = document.getElementById('order-table-body');
            tableBody.innerHTML = ''; // Clear existing rows

            if (filteredOrders.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fa fa-inbox fa-2x mb-2 d-block"></i>
                            Không tìm thấy đơn hàng nào phù hợp với bộ lọc.
                        </td>
                    </tr>
                `;
                return;
            }

            filteredOrders.forEach(order => {
                let statusBadgeClass = '';
                let statusIcon = '';
                switch (order.status) {
                    case 'Đã xử lý':
                        statusBadgeClass = 'bg-success';
                        statusIcon = '<i class="fa fa-check me-1"></i>';
                        break;
                    case 'Đang giao hàng':
                        statusBadgeClass = 'bg-info';
                        statusIcon = '<i class="fa fa-truck me-1"></i>';
                        break;
                    case 'Giao thành công':
                        statusBadgeClass = 'bg-primary'; // Using primary for success to differentiate from 'Đã xử lý'
                        statusIcon = '<i class="fa fa-thumbs-up me-1"></i>';
                        break;
                    case 'Giao thất bại':
                        statusBadgeClass = 'bg-danger';
                        statusIcon = '<i class="fa fa-times-circle me-1"></i>';
                        break;
                    default:
                        statusBadgeClass = 'bg-secondary';
                        break;
                }

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td data-label="ID">${order.id}</td>
                    <td data-label="Tên khách hàng">${order.customer_name}</td>
                    <td data-label="Địa chỉ">${order.address}</td>
                    <td data-label="Số điện thoại">${order.phone}</td>
                    <td data-label="Tổng tiền">${formatCurrency(order.total)}</td>
                    <td data-label="Trạng thái">
                        <span class="badge ${statusBadgeClass} fw-semibold">${statusIcon}${order.status}</span>
                    </td>
                    <td data-label="Ngày tạo">${order.created_at}</td>
                    <td data-label="Hành động" class="text-center">
                        <button class="btn btn-sm btn-secondary me-1 view-details-btn" data-order-id="${order.id}" title="Xem chi tiết">
                            <i class="fa fa-eye"></i>
                        </button>
                        ${order.status === 'Đã xử lý' ? `
                                <button class="btn btn-sm btn-success btn-assign-shipper" data-order-id="${order.id}" title="Giao hàng">
                                    <i class="fa fa-truck me-1"></i> Giao hàng
                                </button>
                            ` : ''}
                        ${order.status === 'Đang giao hàng' ? `
                                <button class="btn btn-sm btn-success delivery-success-btn me-1" data-order-id="${order.id}" title="Giao hàng thành công">
                                    <i class="fa fa-thumbs-up me-1"></i> Thành công
                                </button>
                                <button class="btn btn-sm btn-danger delivery-failed-btn" data-order-id="${order.id}" title="Giao hàng thất bại">
                                    <i class="fa fa-times-circle me-1"></i> Thất bại
                                </button>
                            ` : ''}
                    </td>
                `;
                tableBody.appendChild(row);
            });

            // Add event listeners for newly rendered buttons
            addEventListenersToButtons();
        }

        // Function to update counts for quick filters
        function updateFilterCounts() {
            const totalCount = orders.length;
            const processedCount = orders.filter(order => order.status === 'Đã xử lý').length;
            const shippingCount = orders.filter(order => order.status === 'Đang giao hàng').length;
            const successCount = orders.filter(order => order.status === 'Giao thành công').length;
            const failedCount = orders.filter(order => order.status === 'Giao thất bại').length;

            document.getElementById('total-count').textContent = totalCount;
            document.getElementById('processed-count').textContent = processedCount;
            document.getElementById('shipping-count').textContent = shippingCount;
            document.getElementById('success-count').textContent = successCount;
            document.getElementById('failed-count').textContent = failedCount;
        }

        // Function to apply filters and render
        function applyFilters() {
            const query = document.querySelector('input[name="query"]').value.toLowerCase();
            const statusFilter = document.querySelector('select[name="status_filter"]').value;

            let filteredOrders = orders.filter(order => {
                const matchesQuery = (order.id.toLowerCase().includes(query) || order.phone.includes(query) || order.customer_name.toLowerCase().includes(query));
                const matchesStatus = (statusFilter === '' || order.status === statusFilter);
                return matchesQuery && matchesStatus;
            });

            renderOrders(filteredOrders);
            updateFilterInfo(filteredOrders.length, query, statusFilter);
            updateQuickFilterBadges(statusFilter);
        }

        // Function to update filter info display
        function updateFilterInfo(count, query, status) {
            const filterInfoDiv = document.getElementById('filter-info');
            const currentStatusSpan = document.getElementById('current-status-filter');
            const currentQuerySpan = document.getElementById('current-query-filter');
            const currentTotalOrdersSpan = document.getElementById('current-total-orders');

            if (query || status) {
                filterInfoDiv.classList.remove('hidden');
                currentStatusSpan.textContent = status === '' ? 'Tất cả trạng thái' : status;
                if (query) {
                    currentQuerySpan.innerHTML = ` | Tìm kiếm: <strong>"${query}"</strong>`;
                    currentQuerySpan.classList.remove('hidden');
                } else {
                    currentQuerySpan.classList.add('hidden');
                }
                currentTotalOrdersSpan.textContent = count;
            } else {
                filterInfoDiv.classList.add('hidden');
            }
        }

        // Function to update quick filter badge active state
        function updateQuickFilterBadges(activeStatus) {
            document.querySelectorAll('.filter-badge').forEach(badge => {
                if (badge.dataset.status === activeStatus) {
                    badge.classList.remove('bg-light', 'text-dark');
                    badge.classList.add('bg-primary', 'text-white');
                } else {
                    badge.classList.remove('bg-primary', 'text-white');
                    badge.classList.add('bg-light', 'text-dark');
                }
            });
        }

        // Add event listeners for all dynamic buttons
        function addEventListenersToButtons() {
            // Assign Shipper buttons
            document.querySelectorAll('.btn-assign-shipper').forEach(button => {
                button.onclick = () => {
                    const orderId = button.dataset.orderId;
                    document.getElementById('modalOrderId').value = orderId;
                    document.getElementById('shipperSelect').value = ""; // Reset select
                    document.getElementById('shipperOrderCount').classList.add('d-none'); // Hide count initially
                    const assignShipperModal = new bootstrap.Modal(document.getElementById('assignShipperModal'));
                    assignShipperModal.show();
                };
            });

            // Delivery Success buttons
            document.querySelectorAll('.delivery-success-btn').forEach(button => {
                button.onclick = () => {
                    const orderId = button.dataset.orderId;
                    document.getElementById('successOrderId').textContent = orderId;
                    const deliverySuccessModal = new bootstrap.Modal(document.getElementById(
                        'deliverySuccessModal'));
                    deliverySuccessModal.show();
                    document.getElementById('confirmDeliverySuccess').onclick = () => {
                        updateOrderStatus(orderId, 'Giao thành công');
                        deliverySuccessModal.hide();
                        showSuccessMessage(`Đơn hàng ${orderId} đã được cập nhật thành "Giao thành công".`);
                    };
                };
            });

            // Delivery Failed buttons
            document.querySelectorAll('.delivery-failed-btn').forEach(button => {
                button.onclick = () => {
                    const orderId = button.dataset.orderId;
                    document.getElementById('failedOrderId').textContent = orderId;
                    const deliveryFailedModal = new bootstrap.Modal(document.getElementById(
                        'deliveryFailedModal'));
                    deliveryFailedModal.show();
                    document.getElementById('confirmDeliveryFailed').onclick = () => {
                        updateOrderStatus(orderId, 'Giao thất bại');
                        deliveryFailedModal.hide();
                        showSuccessMessage(`Đơn hàng ${orderId} đã được cập nhật thành "Giao thất bại".`);
                    };
                };
            });

            // View Details buttons (dummy action)
            document.querySelectorAll('.view-details-btn').forEach(button => {
                button.onclick = () => {
                    const orderId = button.dataset.orderId;
                    // Using a custom modal for alerts instead of window.alert
                    const customAlertModal = new bootstrap.Modal(document.getElementById('customAlertModal'));
                    document.getElementById('customAlertBody').textContent =
                        `Xem chi tiết đơn hàng ID: ${orderId} (Chức năng này cần được phát triển thêm)`;
                    customAlertModal.show();
                };
            });
        }

        // Function to update order status
        function updateOrderStatus(orderId, newStatus) {
            const orderIndex = orders.findIndex(order => order.id === orderId);
            if (orderIndex !== -1) {
                orders[orderIndex].status = newStatus;
                applyFilters(); // Re-render table with updated status
                updateFilterCounts(); // Update counts in quick filters
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initial render of orders
            applyFilters();
            updateFilterCounts();

            // Event listener for search form submission
            document.getElementById('search-form').addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent actual form submission
                applyFilters();
            });

            // Event listeners for quick filter badges
            document.querySelectorAll('.filter-badge').forEach(badge => {
                badge.addEventListener('click', function(event) {
                    event.preventDefault();
                    const status = this.dataset.status;
                    document.querySelector('select[name="status_filter"]').value = status;
                    // Also update the search input if there's a query to clear it
                    document.querySelector('input[name="query"]').value = '';
                    applyFilters();
                });
            });

            // Event listener for clear filters button
            document.getElementById('clear-filters-btn').addEventListener('click', function() {
                document.querySelector('input[name="query"]').value = '';
                document.querySelector('select[name="status_filter"]').value = '';
                applyFilters();
            });

            // Event listener for shipper selection in modal
            const shipperSelect = document.getElementById('shipperSelect');
            const shipperOrderCountDiv = document.getElementById('shipperOrderCount');
            const orderCountNumberSpan = document.getElementById('orderCountNumber');

            shipperSelect.addEventListener('change', () => {
                const shipperId = shipperSelect.value;
                if (shipperId && shipperData[shipperId] !== undefined) {
                    orderCountNumberSpan.textContent = shipperData[shipperId];
                    shipperOrderCountDiv.classList.remove('d-none');
                } else {
                    shipperOrderCountDiv.classList.add('d-none');
                }
            });

            // Handle assign shipper form submission
            document.getElementById('assignShipperForm').addEventListener('submit', function(event) {
                event.preventDefault();
                const orderId = document.getElementById('modalOrderId').value;
                const shipperId = document.getElementById('shipperSelect').value;

                if (shipperId) {
                    updateOrderStatus(orderId, 'Đang giao hàng');
                    const assignShipperModal = bootstrap.Modal.getInstance(document.getElementById('assignShipperModal'));
                    assignShipperModal.hide();
                    showSuccessMessage(`Đơn hàng ${orderId} đã được giao cho nhân viên ID: ${shipperId}.`);
                } else {
                    const customAlertModal = new bootstrap.Modal(document.getElementById('customAlertModal'));
                    document.getElementById('customAlertBody').textContent = 'Vui lòng chọn một nhân viên giao hàng.';
                    customAlertModal.show();
                }
            });
        });
    </script>
@endsection
@endcan

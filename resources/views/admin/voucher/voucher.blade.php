@can('salers')
    @extends('admin.master')
    @section('title', 'Thông tin Voucher')
@section('content')

    <div id="message-container"></div>
    <div class="card mx-auto" style="max-width: 1400px;">
        <div class="card-body p-4">
            <div class="card-sub mb-4">
                <form id="searchForm" class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                    <div class="d-flex flex-column flex-md-row align-items-center gap-3 w-100 w-md-auto">
                        <div class="position-relative w-100 flex-grow-1">
                            <div class="position-absolute top-50 start-0 translate-middle-y ps-3">
                                <i class="fa fa-search text-secondary"></i>
                            </div>
                            <input name="query" type="text" placeholder="Tìm kiếm voucher..."
                                class="form-control ps-5 py-2 border border-secondary focus:border-primary d-block w-100" />
                        </div>
                        <div class="w-100 w-md-auto">
                            <select name="status"
                                class="form-select border border-secondary focus:border-primary d-block w-100">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active">Còn hạn</option>
                                <option value="inactive">Đã hết hạn</option>
                                <option value="upcoming">Sắp diễn ra</option>
                            </select>
                        </div>
                    </div>
                    <div class="w-100 w-md-auto text-md-end">
                        <button type="button"
                            class="btn btn-success add-new-modal btn-create d-flex align-items-center justify-content-center w-100 w-md-auto px-4 py-2">
                            <i class="fa fa-plus me-2"></i> Thêm mới Voucher
                        </button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="p-3 text-start">ID</th>
                            <th class="p-3 text-start">Mã Voucher</th>
                            <th class="p-3 text-start">Mô tả</th>
                            <th class="p-3 text-start">% KM</th>
                            <th class="p-3 text-start">Số lần khả dụng</th>
                            <th class="p-3 text-start">Bắt đầu</th>
                            <th class="p-3 text-start">Kết thúc</th>
                            <th class="p-3 text-start">Trạng thái</th>
                            <th class="p-3 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="voucherTableBody">
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $vouchers->links() }}
            </div>
        </div>
    </div>

    <!-- Modal thêm/sửa voucher -->
    <div class="modal fade" id="discountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form id="discountForm" class="modal-content">
                <input type="hidden" name="id" id="voucherId">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="discountModalLabel">Thêm Voucher</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row g-3 px-4 py-4">
                    <div class="col-12">
                        <label for="vouchers_code" class="form-label text-secondary fw-medium">Mã Voucher</label>
                        <input type="text" name="vouchers_code" id="vouchers_code"
                            class="form-control border border-secondary rounded p-2 w-100" maxlength="10">
                    </div>
                    <div class="col-12">
                        <label for="name" class="form-label text-secondary fw-medium">Mô tả Voucher</label>
                        <textarea name="name" id="name" class="form-control border border-secondary rounded p-2 w-100" rows="3"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="percent_discount" class="form-label text-secondary fw-medium">Phần trăm giảm giá
                            (%)</label>
                        <input type="number" name="percent_discount" id="percent_discount"
                            class="form-control border border-secondary rounded p-2 w-100" step="0.001" min="0"
                            max="100">
                    </div>
                    <div class="col-md-6">
                        <label for="max_discount" class="form-label text-secondary fw-medium">Giảm tối đa (VNĐ)</label>
                        <input type="number" name="max_discount" id="max_discount"
                            class="form-control border border-secondary rounded p-2 w-100" step="0.001" min="0">
                    </div>
                    <div class="col-md-6">
                        <label for="min_order_amount" class="form-label text-secondary fw-medium">Đơn hàng tối thiểu
                            (VNĐ)</label>
                        <input type="number" name="min_order_amount" id="min_order_amount"
                            class="form-control border border-secondary rounded p-2 w-100" step="0.001" min="0">
                    </div>
                    <div class="col-md-6">
                        <label for="available_uses" class="form-label text-secondary fw-medium">Số lần khả dụng</label>
                        <input type="number" name="available_uses" id="available_uses"
                            class="form-control border border-secondary rounded p-2 w-100" min="1">
                    </div>
                    <div class="col-md-6">
                        <label for="start_date" class="form-label text-secondary fw-medium">Ngày bắt đầu</label>
                        <input type="datetime-local" name="start_date" id="start_date"
                            class="form-control border border-secondary rounded p-2 w-100">
                    </div>
                    <div class="col-md-6">
                        <label for="end_date" class="form-label text-secondary fw-medium">Ngày kết thúc</label>
                        <input type="datetime-local" name="end_date" id="end_date"
                            class="form-control border border-secondary rounded p-2 w-100">
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 bg-light d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-success px-4 py-2">Lưu</button>
                    <button type="button" class="btn btn-secondary px-4 py-2" data-bs-dismiss="modal">Hủy</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal chi tiết voucher -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="detailModalLabel"><i class="fas fa-ticket-alt me-2"></i> Chi tiết
                        Voucher</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start gap-4">
                        <div class="text-center mb-3 mb-md-0" style="width: 33.333%;">
                            <i class="fas fa-tags text-primary" style="font-size: 60px;"></i>
                        </div>
                        <div class="w-100" style="width: 66.666%;">
                            <p class="mb-2 text-secondary"><strong>ID:</strong> <span id="promo-id"
                                    class="text-muted fw-normal"></span></p>
                            <p class="mb-2 text-secondary"><strong>Mã Voucher:</strong> <span id="promo-code"
                                    class="fw-bold text-primary"></span></p>
                            <p class="mb-2 text-secondary"><strong>Mô tả:</strong> <span id="promo-name"
                                    class="fw-bold text-primary"></span></p>
                            <p class="mb-2 text-secondary"><strong>Phần trăm giảm giá:</strong> <span id="promo-percent"
                                    class="badge bg-success"></span></p>
                            <p class="mb-2 text-secondary"><strong>Giảm tối đa:</strong> <span id="promo-max-discount"
                                    class="text-muted fw-normal"></span></p>
                            <p class="mb-2 text-secondary"><strong>Đơn hàng tối thiểu:</strong> <span id="promo-min-order"
                                    class="text-muted fw-normal"></span></p>
                            <p class="mb-2 text-secondary"><strong>Số lần khả dụng:</strong> <span id="promo-uses"
                                    class="text-muted fw-normal"></span></p>
                            <p class="mb-2 text-secondary"><strong>Ngày bắt đầu:</strong> <span id="promo-start"
                                    class="text-muted fw-normal"></span></p>
                            <p class="mb-2 text-secondary"><strong>Ngày kết thúc:</strong> <span id="promo-end"
                                    class="text-muted fw-normal"></span></p>
                            <p class="mb-2 text-secondary"><strong>Trạng thái:</strong> <span id="promo-status"
                                    class="fw-bold badge"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center bg-light py-3">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        // Dữ liệu voucher từ server
        let vouchers = @json($transformedVouchers);

        // Function to display messages (success/error)
        function showMessage(type, message) {
            const messageContainer = document.getElementById('message-container');
            const alertDiv = document.createElement('div');
            alertDiv.className =
                `shadow-lg p-2 move-from-top js-div-dissappear flex items-center text-center ${type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`;
            alertDiv.style.width = '25rem';
            alertDiv.innerHTML = `
                <i class="fas ${type === 'success' ? 'fa-check bg-success' : 'fa-times bg-danger'} text-white rounded-full p-2 mr-2"></i>
                <span class="flex-grow text-sm">${message}</span>
            `;
            messageContainer.appendChild(alertDiv);

            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }

        // Function to format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(amount);
        }

        // Function to get voucher status
        function getVoucherStatus(startDateStr, endDateStr) {
            const now = new Date();
            const startDate = new Date(startDateStr);
            const endDate = new Date(endDateStr);

            if (now >= startDate && now <= endDate) {
                return {
                    text: 'Đang hiệu lực',
                    class: 'bg-success'
                };
            } else if (now > endDate) {
                return {
                    text: 'Đã hết hạn',
                    class: 'bg-danger'
                };
            } else {
                return {
                    text: 'Sắp diễn ra',
                    class: 'bg-info'
                };
            }
        }

        // Function to render the table
        function renderVoucherTable(data) {
            const tableBody = document.getElementById('voucherTableBody');
            tableBody.innerHTML = '';

            if (data.length === 0) {
                tableBody.innerHTML =
                    `<tr><td colspan="9" class="text-center py-4 text-gray-500">Không tìm thấy voucher nào.</td></tr>`;
                return;
            }

            data.forEach(voucher => {
                const status = getVoucherStatus(voucher.start_date, voucher.end_date);
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="p-3">${voucher.id}</td>
                    <td class="p-3 fw-semibold">${voucher.code}</td>
                    <td class="p-3" style="max-width: 200px;">${voucher.description.substring(0, 50)}${voucher.description.length > 50 ? '...' : ''}</td>
                    <td class="p-3"><span class="badge bg-success">${(voucher.percent_discount * 100).toFixed(1)}%</span></td>
                    <td class="p-3">${voucher.available_uses}</td>
                    <td class="p-3">${new Date(voucher.start_date).toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</td>
                    <td class="p-3">${new Date(voucher.end_date).toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</td>
                    <td class="p-3"><span class="badge ${status.class}">${status.text}</span></td>
                    <td class="p-3 text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <button type="button" class="btn btn-sm btn-info btn-detail" data-id="${voucher.id}" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-primary btn-edit" data-id="${voucher.id}" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="${voucher.id}" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            addEventListenersToButtons();
        }

        // Function to filter and search vouchers
        function filterAndSearchVouchers() {
            const query = document.querySelector('input[name="query"]').value.toLowerCase();
            const statusFilter = document.querySelector('select[name="status"]').value;

            let filteredVouchers = vouchers.filter(voucher => {
                const matchesQuery = voucher.code.toLowerCase().includes(query) ||
                    voucher.description.toLowerCase().includes(query);

                let matchesStatus = true;
                if (statusFilter) {
                    const voucherStatus = getVoucherStatus(voucher.start_date, voucher.end_date);
                    switch (statusFilter) {
                        case 'active':
                            matchesStatus = voucherStatus.text === 'Đang hiệu lực';
                            break;
                        case 'inactive':
                            matchesStatus = voucherStatus.text === 'Đã hết hạn';
                            break;
                        case 'upcoming':
                            matchesStatus = voucherStatus.text === 'Sắp diễn ra';
                            break;
                    }
                }

                return matchesQuery && matchesStatus;
            });

            renderVoucherTable(filteredVouchers);
        }

        // Add event listeners for detail, edit, delete buttons
        function addEventListenersToButtons() {
            // Detail Button
            document.querySelectorAll('.btn-detail').forEach(button => {
                button.onclick = (e) => {
                    const id = parseInt(e.currentTarget.dataset.id);
                    const voucher = vouchers.find(v => v.id === id);
                    if (voucher) {
                        const status = getVoucherStatus(voucher.start_date, voucher.end_date);
                        document.getElementById('promo-id').textContent = voucher.id;
                        document.getElementById('promo-code').textContent = voucher.code;
                        document.getElementById('promo-name').textContent = voucher.description;
                        document.getElementById('promo-percent').textContent =
                            `${(voucher.percent_discount * 100).toFixed(1)}%`;
                        document.getElementById('promo-max-discount').textContent = formatCurrency(voucher
                            .max_discount);
                        document.getElementById('promo-min-order').textContent = formatCurrency(voucher
                            .min_order_amount);
                        document.getElementById('promo-uses').textContent = voucher.available_uses;
                        document.getElementById('promo-start').textContent = new Date(voucher.start_date)
                            .toLocaleString('vi-VN');
                        document.getElementById('promo-end').textContent = new Date(voucher.end_date)
                            .toLocaleString('vi-VN');

                        const promoStatusSpan = document.getElementById('promo-status');
                        promoStatusSpan.textContent = status.text;
                        promoStatusSpan.className = `fw-bold badge ${status.class}`;

                        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
                        detailModal.show();
                    } else {
                        showMessage('error', 'Không tìm thấy chi tiết voucher!');
                    }
                };
            });

            // Edit Button
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.onclick = (e) => {
                    const id = parseInt(e.currentTarget.dataset.id);
                    const voucher = vouchers.find(v => v.id === id);
                    if (voucher) {
                        document.getElementById('discountModalLabel').textContent = 'Sửa Voucher';
                        document.getElementById('voucherId').value = voucher.id;
                        document.getElementById('vouchers_code').value = voucher.code;
                        document.getElementById('name').value = voucher.description;
                        document.getElementById('percent_discount').value = (voucher.percent_discount * 100)
                            .toFixed(3);
                        document.getElementById('max_discount').value = voucher.max_discount;
                        document.getElementById('min_order_amount').value = voucher.min_order_amount;
                        document.getElementById('available_uses').value = voucher.available_uses;

                        // Format dates for datetime-local input
                        const startDate = new Date(voucher.start_date);
                        const endDate = new Date(voucher.end_date);
                        document.getElementById('start_date').value = startDate.toISOString().slice(0, 16);
                        document.getElementById('end_date').value = endDate.toISOString().slice(0, 16);

                        const discountModal = new bootstrap.Modal(document.getElementById('discountModal'));
                        discountModal.show();
                    } else {
                        showMessage('error', 'Không tìm thấy voucher để sửa!');
                    }
                };
            });

            // Delete Button
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.onclick = (e) => {
                    const id = parseInt(e.currentTarget.dataset.id);

                    if (confirm('Bạn có chắc chắn muốn xóa voucher này?')) {
                        // In a real application, you would make an AJAX call to delete from database
                        vouchers = vouchers.filter(v => v.id !== id);
                        renderVoucherTable(vouchers);
                        showMessage('success', 'Đã xóa voucher thành công!');
                    }
                };
            });
        }

        // Event listener for "Add new" button
        document.querySelector('.btn-create').onclick = () => {
            document.getElementById('discountModalLabel').textContent = 'Thêm Voucher';
            document.getElementById('voucherId').value = '';
            document.getElementById('discountForm').reset();
            const discountModal = new bootstrap.Modal(document.getElementById('discountModal'));
            discountModal.show();
        };

        // Event listener for form submission (Add/Edit)
        document.getElementById('discountForm').onsubmit = (e) => {
            e.preventDefault();

            const id = document.getElementById('voucherId').value;
            const vouchers_code = document.getElementById('vouchers_code').value.trim();
            const name = document.getElementById('name').value.trim();
            const percent_discount = parseFloat(document.getElementById('percent_discount').value) / 100;
            const max_discount = parseFloat(document.getElementById('max_discount').value);
            const min_order_amount = parseFloat(document.getElementById('min_order_amount').value);
            const available_uses = parseInt(document.getElementById('available_uses').value);
            const start_date = document.getElementById('start_date').value;
            const end_date = document.getElementById('end_date').value;

            // Validation
            if (!vouchers_code || !name || isNaN(percent_discount) || isNaN(max_discount) ||
                isNaN(min_order_amount) || isNaN(available_uses) || !start_date || !end_date) {
                showMessage('error', 'Vui lòng điền đầy đủ và hợp lệ các trường!');
                return;
            }

            if (new Date(start_date) >= new Date(end_date)) {
                showMessage('error', 'Ngày kết thúc phải sau ngày bắt đầu!');
                return;
            }

            if (id) {
                // Edit existing voucher
                const index = vouchers.findIndex(v => v.id === parseInt(id));
                if (index !== -1) {
                    vouchers[index] = {
                        ...vouchers[index],
                        code: vouchers_code,
                        description: name,
                        percent_discount,
                        max_discount,
                        min_order_amount,
                        available_uses,
                        start_date,
                        end_date
                    };
                    showMessage('success', 'Cập nhật voucher thành công!');
                } else {
                    showMessage('error', 'Không tìm thấy voucher để cập nhật!');
                }
            } else {
                // Add new voucher
                const newId = vouchers.length > 0 ? Math.max(...vouchers.map(v => v.id)) + 1 : 1;
                vouchers.push({
                    id: newId,
                    code: vouchers_code,
                    description: name,
                    percent_discount,
                    max_discount,
                    min_order_amount,
                    available_uses,
                    start_date,
                    end_date
                });
                showMessage('success', 'Thêm voucher mới thành công!');
            }

            filterAndSearchVouchers();
            const discountModal = bootstrap.Modal.getInstance(document.getElementById('discountModal'));
            discountModal.hide();
        };

        // Event listeners for search and filter
        document.querySelector('select[name="status"]').onchange = filterAndSearchVouchers;

        let searchTimeout = null;
        document.querySelector('input[name="query"]').onkeyup = () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(filterAndSearchVouchers, 500);
        };

        // Initial render on page load
        document.addEventListener('DOMContentLoaded', () => {
            renderVoucherTable(vouchers);
        });
    </script>
@endsection

@section('css')
    <style>
        .card {
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .btn {
            border-radius: 0.5rem;
        }

        .form-control,
        .form-select {
            border-radius: 0.5rem;
        }

        .badge {
            padding: 0.35em 0.65em;
            border-radius: 0.5rem;
            font-weight: 600;
        }

        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .table tbody tr:hover {
            background-color: #f0f4f8;
        }

        .modal-content {
            border-radius: 1rem;
            overflow: hidden;
        }

        .modal-header {
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
        }

        .move-from-top {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            animation: slideInFromTop 0.5s ease-out forwards;
            border-radius: 0.75rem;
        }

        @keyframes slideInFromTop {
            from {
                transform: translateX(-50%) translateY(-100%);
                opacity: 0;
            }

            to {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
        }

        .js-div-dissappear {
            animation: fadeOut 0.5s ease-out 2.5s forwards;
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
                visibility: hidden;
            }
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .table td {
            vertical-align: middle;
        }
    </style>
@endsection
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

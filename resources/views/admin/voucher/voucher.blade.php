@can('salers')
    @extends('admin.master')
    @section('title', 'Thông tin Voucher')
@section('content')

    <div id="message-container"></div>
    <div class="card mx-auto" style="max-width: 1400px;"> {{-- Adjust max-width as needed --}}
        <div class="card-body p-4"> {{-- Reduced padding for Bootstrap's typical spacing --}}
            <div class="card-sub mb-4">
                <form id="searchForm" class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3"> {{-- Changed gap-4 to gap-3 for Bootstrap spacing, flex-col to flex-column, flex-row to flex-md-row --}}
                    {{-- Search Input and Status Filter --}}
                    <div class="d-flex flex-column flex-md-row align-items-center gap-3 w-100 w-md-auto"> {{-- Changed gap-4 to gap-3, w-full to w-100, w-auto to w-md-auto --}}
                        <div class="position-relative w-100 flex-grow-1"> {{-- Added position-relative and flex-grow-1 --}}
                            <div class="position-absolute top-50 start-0 translate-middle-y ps-3"> {{-- Changed inset-y-0 left-0 to top-50 start-0 translate-middle-y, flex items-center pl-3 to ps-3 --}}
                                <i class="fa fa-search text-secondary"></i> {{-- Changed text-gray-400 to text-secondary --}}
                            </div>
                            <input name="query" type="text" placeholder="Tìm kiếm voucher..."
                                class="form-control ps-5 py-2 border border-secondary focus:border-primary d-block w-100" /> {{-- Changed pl-10 pr-3 to ps-5, border-gray-300 to border-secondary, focus:ring-blue-500 focus:border-blue-500 to focus:border-primary, block w-full to d-block w-100 --}}
                        </div>

                        <div class="w-100 w-md-auto"> {{-- Changed w-full to w-100, w-auto to w-md-auto --}}
                            <select name="status"
                                class="form-select border border-secondary focus:border-primary d-block w-100"> {{-- Changed border-gray-300 to border-secondary, focus:ring-blue-500 focus:border-blue-500 to focus:border-primary, block w-full to d-block w-100 --}}
                                <option value="">Tất cả trạng thái</option>
                                <option value="active">Còn hạn</option>
                                <option value="inactive">Đã hết hạn</option>
                                <option value="upcoming">Sắp diễn ra</option>
                            </select>
                        </div>
                    </div>
                    {{-- Add New Button --}}
                    <div class="w-100 w-md-auto text-md-end"> {{-- Changed w-full to w-100, w-auto to w-md-auto, md:text-end to text-md-end --}}
                        <button type="button"
                            class="btn btn-success add-new-modal btn-create d-flex align-items-center justify-content-center w-100 w-md-auto px-4 py-2"> {{-- Changed flex items-center justify-center to d-flex align-items-center justify-content-center, w-full to w-100, w-auto to w-md-auto --}}
                            <i class="fa fa-plus me-2"></i> Thêm mới Voucher {{-- Changed mr-2 to me-2 --}}
                        </button>
                    </div>
                </form>
            </div>

            <div class="table-responsive"> {{-- Changed overflow-x-auto to table-responsive --}}
                <table class="table table-hover align-middle w-100"> {{-- Changed w-full to w-100 --}}
                    <thead class="table-light">
                        <tr>
                            <th class="p-3 text-start">ID</th> {{-- Changed text-left to text-start --}}
                            <th class="p-3 text-start">Tên Voucher</th> {{-- Changed text-left to text-start --}}
                            <th class="p-3 text-start">% KM</th> {{-- Changed text-left to text-start --}}
                            <th class="p-3 text-start">Số lần khả dụng</th> {{-- Changed text-left to text-start --}}
                            <th class="p-3 text-start">Bắt đầu</th> {{-- Changed text-left to text-start --}}
                            <th class="p-3 text-start">Kết thúc</th> {{-- Changed text-left to text-start --}}
                            <th class="p-3 text-start">Trạng thái</th> {{-- Changed text-left to text-start --}}
                            <th class="p-3 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="voucherTableBody">
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <li class="page-item disabled"><a class="page-link" href="#">Trước</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">Tiếp</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

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
                        <label for="name" class="form-label text-secondary fw-medium">Tên Voucher</label> {{-- Changed text-gray-700 font-medium to text-secondary fw-medium --}}
                        <input type="text" name="name" id="name"
                            class="form-control border border-secondary rounded p-2 w-100"> {{-- Changed border-gray-300 rounded-md p-2 w-full to border border-secondary rounded p-2 w-100 --}}
                    </div>
                    <div class="col-12">
                        <label for="percent_discount" class="form-label text-secondary fw-medium">Phần trăm giảm giá
                            (%)</label> {{-- Changed text-gray-700 font-medium to text-secondary fw-medium --}}
                        <input type="number" name="percent_discount" id="percent_discount"
                            class="form-control border border-secondary rounded p-2 w-100" step="0.01" min="0" {{-- Changed border-gray-300 rounded-md p-2 w-full to border border-secondary rounded p-2 w-100 --}}
                            max="100">
                    </div>
                    <div class="col-12">
                        <label for="available_uses" class="form-label text-secondary fw-medium">Số lần khả dụng</label> {{-- Changed text-gray-700 font-medium to text-secondary fw-medium --}}
                        <input type="number" name="available_uses" id="available_uses"
                            class="form-control border border-secondary rounded p-2 w-100" min="0"> {{-- Changed border-gray-300 rounded-md p-2 w-full to border border-secondary rounded p-2 w-100 --}}
                    </div>
                    <div class="col-md-6">
                        <label for="start_date" class="form-label text-secondary fw-medium">Ngày bắt đầu</label> {{-- Changed text-gray-700 font-medium to text-secondary fw-medium --}}
                        <input type="datetime-local" name="start_date" id="start_date"
                            class="form-control border border-secondary rounded p-2 w-100"> {{-- Changed border-gray-300 rounded-md p-2 w-full to border border-secondary rounded p-2 w-100 --}}
                    </div>
                    <div class="col-md-6">
                        <label for="end_date" class="form-label text-secondary fw-medium">Ngày kết thúc</label> {{-- Changed text-gray-700 font-medium to text-secondary fw-medium --}}
                        <input type="datetime-local" name="end_date" id="end_date"
                            class="form-control border border-secondary rounded p-2 w-100"> {{-- Changed border-gray-300 rounded-md p-2 w-full to border border-secondary rounded p-2 w-100 --}}
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 bg-light d-flex justify-content-end gap-2"> {{-- Changed bg-gray-50 to bg-light, flex justify-end gap-2 to d-flex justify-content-end gap-2 --}}
                    <button type="submit" class="btn btn-success px-4 py-2">Lưu</button>
                    <button type="button" class="btn btn-secondary px-4 py-2" data-bs-dismiss="modal">Hủy</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="detailModalLabel"><i class="fas fa-ticket-alt me-2"></i> Chi tiết {{-- Changed mr-2 to me-2 --}}
                        Voucher</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4"> {{-- Reduced padding --}}
                    <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start gap-4"> {{-- Changed flex flex-col md:flex-row items-center md:items-start gap-6 to d-flex flex-column flex-md-row align-items-center align-items-md-start gap-4 --}}
                        <div class="text-center mb-3 mb-md-0" style="width: 33.333%;"> {{-- Changed md:w-1/3 to width: 33.333%; text-center mb-4 md:mb-0 to text-center mb-3 mb-md-0 --}}
                            <i class="fas fa-tags text-primary" style="font-size: 60px;"></i>
                        </div>
                        <div class="w-100" style="width: 66.666%;"> {{-- Changed md:w-2/3 w-full to w-100 and added width style --}}
                            <p class="mb-2 text-secondary"><strong>ID:</strong> <span id="promo-id"
                                        class="text-muted fw-normal"></span></p> {{-- Changed text-gray-700 font-normal to text-secondary fw-normal --}}
                            <p class="mb-2 text-secondary"><strong>Tên Voucher:</strong> <span id="promo-name"
                                        class="fw-bold text-primary"></span></p> {{-- Changed text-gray-700 to text-secondary --}}
                            <p class="mb-2 text-secondary"><strong>Phần trăm giảm giá:</strong> <span id="promo-percent"
                                        class="badge bg-success"></span></p> {{-- Changed text-gray-700 to text-secondary --}}
                            <p class="mb-2 text-secondary"><strong>Số lần khả dụng:</strong> <span id="promo-uses"
                                        class="text-muted fw-normal"></span></p> {{-- Changed text-gray-700 font-normal to text-secondary fw-normal --}}
                            <p class="mb-2 text-secondary"><strong>Ngày bắt đầu:</strong> <span id="promo-start"
                                        class="text-muted fw-normal"></span></p> {{-- Changed text-gray-700 font-normal to text-secondary fw-normal --}}
                            <p class="mb-2 text-secondary"><strong>Ngày kết thúc:</strong> <span id="promo-end"
                                        class="text-muted fw-normal"></span></p> {{-- Changed text-gray-700 font-normal to text-secondary fw-normal --}}
                            <p class="mb-2 text-secondary"><strong>Trạng thái:</strong> <span id="promo-status"
                                        class="fw-bold badge"></span></p> {{-- Changed text-gray-700 to text-secondary --}}
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center bg-light py-3"> {{-- Changed bg-gray-50 to bg-light --}}
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Đóng {{-- Changed mr-2 to me-2 --}}
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <script>
        // Mock data for vouchers
        let vouchers = [{
                id: 1,
                name: 'Voucher Hè 2024',
                percent_discount: 0.15,
                available_uses: 100,
                start_date: '2024-06-01T00:00',
                end_date: '2024-08-31T23:59'
            },
            {
                id: 2,
                name: 'Voucher Black Friday',
                percent_discount: 0.25,
                available_uses: 0,
                start_date: '2023-11-24T00:00',
                end_date: '2023-11-27T23:59'
            },
            {
                id: 3,
                name: 'Voucher Thành viên mới',
                percent_discount: 0.10,
                available_uses: 500,
                start_date: '2024-01-01T00:00',
                end_date: '2024-12-31T23:59'
            },
            {
                id: 4,
                name: 'Voucher Flash Sale',
                percent_discount: 0.30,
                available_uses: 10,
                start_date: '2024-05-20T10:00',
                end_date: '2024-05-22T22:00'
            },
            {
                id: 5,
                name: 'Voucher Tết Nguyên Đán',
                percent_discount: 0.20,
                available_uses: 0,
                start_date: '2024-02-01T00:00',
                end_date: '2024-02-15T23:59'
            },
            {
                id: 6,
                name: 'Voucher Sinh nhật cửa hàng',
                percent_discount: 0.05,
                available_uses: 200,
                start_date: '2024-07-10T09:00',
                end_date: '2024-07-17T17:00'
            },
            {
                id: 7,
                name: 'Voucher Back to School',
                percent_discount: 0.12,
                available_uses: 75,
                start_date: '2024-08-01T00:00',
                end_date: '2024-09-05T23:59'
            },
            {
                id: 8,
                name: 'Voucher Cyber Monday',
                percent_discount: 0.35,
                available_uses: 0,
                start_date: '2023-11-28T00:00',
                end_date: '2023-11-28T23:59'
            },
            {
                id: 9,
                name: 'Voucher Giáng sinh',
                percent_discount: 0.18,
                available_uses: 150,
                start_date: '2024-12-01T00:00',
                end_date: '2024-12-25T23:59'
            }, // Upcoming
        ];

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
            }, 3000); // Remove after 3 seconds
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
            tableBody.innerHTML = ''; // Clear existing rows

            if (data.length === 0) {
                tableBody.innerHTML =
                    `<tr><td colspan="8" class="text-center py-4 text-gray-500">Không tìm thấy voucher nào.</td></tr>`;
                return;
            }

            data.forEach(voucher => {
                const status = getVoucherStatus(voucher.start_date, voucher.end_date);
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="p-3">${voucher.id}</td>
                    <td class="p-3 fw-semibold">${voucher.name}</td>
                    <td class="p-3"><span class="badge bg-success">${(voucher.percent_discount * 100).toFixed(0)}%</span></td>
                    <td class="p-3">${voucher.available_uses}</td>
                    <td class="p-3">${new Date(voucher.start_date).toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</td>
                    <td class="p-3">${new Date(voucher.end_date).toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</td>
                    <td class="p-3"><span class="badge ${status.class}">${status.text}</span></td>
                    <td class="p-3 text-center">
                        <div class="flex justify-center gap-2">
                            <button type="button" class="btn btn-sm btn-info btn-detail" data-id="${voucher.id}"><i class="fas fa-eye"></i></button>
                            <button type="button" class="btn btn-sm btn-primary btn-edit" data-id="${voucher.id}"><i class="fas fa-edit"></i></button>
                            <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="${voucher.id}"><i class="fas fa-trash"></i></button>
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
                const matchesQuery = voucher.name.toLowerCase().includes(query);
                const voucherStatus = getVoucherStatus(voucher.start_date, voucher.end_date);
                const matchesStatus = statusFilter === '' || voucherStatus.text.toLowerCase().includes(statusFilter
                    .toLowerCase());
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
                    const promo = vouchers.find(v => v.id === id);
                    if (promo) {
                        const status = getVoucherStatus(promo.start_date, promo.end_date);
                        document.getElementById('promo-id').textContent = promo.id;
                        document.getElementById('promo-name').textContent = promo.name;
                        document.getElementById('promo-percent').textContent =
                            `${(promo.percent_discount * 100).toFixed(0)}%`;
                        document.getElementById('promo-uses').textContent = promo
                            .available_uses; // Display available uses
                        document.getElementById('promo-start').textContent = new Date(promo.start_date)
                            .toLocaleString('vi-VN', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        document.getElementById('promo-end').textContent = new Date(promo.end_date)
                            .toLocaleString('vi-VN', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        const promoStatusSpan = document.getElementById('promo-status');
                        promoStatusSpan.textContent = status.text;
                        promoStatusSpan.className =
                            `fw-bold badge ${status.class}`; // Update class for status badge
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
                    const promo = vouchers.find(v => v.id === id);
                    if (promo) {
                        document.getElementById('discountModalLabel').textContent = 'Sửa Voucher';
                        document.getElementById('voucherId').value = promo.id;
                        document.getElementById('name').value = promo.name;
                        document.getElementById('percent_discount').value = promo.percent_discount * 100;
                        document.getElementById('available_uses').value = promo
                            .available_uses; // Populate available uses
                        document.getElementById('start_date').value = promo.start_date.slice(0,
                            16); // Format for datetime-local
                        document.getElementById('end_date').value = promo.end_date.slice(0,
                            16); // Format for datetime-local
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
                    // Using custom modal for confirmation instead of alert/confirm
                    const confirmDelete = (callback) => {
                        const modalHtml = `
                            <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-sm modal-dialog-centered">
                                    <div class="modal-content rounded-lg shadow-lg">
                                        <div class="modal-header bg-warning text-white rounded-t-lg">
                                            <h5 class="modal-title text-lg font-semibold" id="confirmDeleteModalLabel">Xác nhận xóa</h5>
                                            <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-6 text-center">
                                            <p class="text-gray-700">Bạn có chắc chắn muốn xóa voucher này?</p>
                                        </div>
                                        <div class="modal-footer flex justify-center gap-3 p-4 bg-gray-50 rounded-b-lg">
                                            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Xóa</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        document.body.insertAdjacentHTML('beforeend', modalHtml);
                        const confirmModal = new bootstrap.Modal(document.getElementById(
                            'confirmDeleteModal'));
                        confirmModal.show();

                        document.getElementById('confirmDeleteBtn').onclick = () => {
                            callback(true);
                            confirmModal.hide();
                            document.getElementById('confirmDeleteModal').remove(); // Clean up modal
                        };
                        document.getElementById('confirmDeleteModal').addEventListener('hidden.bs.modal',
                        () => {
                                document.getElementById('confirmDeleteModal')
                            .remove(); // Clean up modal on close
                            });
                    };

                    confirmDelete((confirmed) => {
                        if (confirmed) {
                            vouchers = vouchers.filter(v => v.id !== id);
                            renderVoucherTable(vouchers);
                            showMessage('success', 'Đã xóa voucher thành công!');
                        }
                    });
                };
            });
        }

        // Event listener for "Add new" button
        document.querySelector('.btn-create').onclick = () => {
            document.getElementById('discountModalLabel').textContent = 'Thêm Voucher';
            document.getElementById('voucherId').value = ''; // Clear ID for new entry
            document.getElementById('discountForm').reset();
            const discountModal = new bootstrap.Modal(document.getElementById('discountModal'));
            discountModal.show();
        };

        // Event listener for form submission (Add/Edit)
        document.getElementById('discountForm').onsubmit = (e) => {
            e.preventDefault();
            const id = document.getElementById('voucherId').value;
            const name = document.getElementById('name').value;
            const percent_discount = parseFloat(document.getElementById('percent_discount').value) / 100;
            const available_uses = parseInt(document.getElementById('available_uses').value); // Get available uses
            const start_date = document.getElementById('start_date').value;
            const end_date = document.getElementById('end_date').value;

            if (!name || isNaN(percent_discount) || isNaN(available_uses) || !start_date || !end_date) {
                showMessage('error', 'Vui lòng điền đầy đủ và hợp lệ các trường!');
                return;
            }

            if (id) {
                // Edit existing voucher
                const index = vouchers.findIndex(v => v.id === parseInt(id));
                if (index !== -1) {
                    vouchers[index] = {
                        ...vouchers[index],
                        name,
                        percent_discount,
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
                    name,
                    percent_discount,
                    available_uses,
                    start_date,
                    end_date
                });
                showMessage('success', 'Thêm voucher mới thành công!');
            }
            filterAndSearchVouchers(); // Re-render with updated data
            const discountModal = bootstrap.Modal.getInstance(document.getElementById('discountModal'));
            discountModal.hide();
        };

        // Event listeners for search and filter
        document.querySelector('select[name="status"]').onchange = filterAndSearchVouchers;

        let searchTimeout = null;
        document.querySelector('input[name="query"]').onkeyup = () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(filterAndSearchVouchers, 500); // Debounce search
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
            /* Rounded corners for card */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .btn {
            border-radius: 0.5rem;
            /* Rounded corners for buttons */
        }

        .form-control,
        .form-select {
            border-radius: 0.5rem;
            /* Rounded corners for inputs */
        }

        .badge {
            padding: 0.35em 0.65em;
            border-radius: 0.5rem;
            font-weight: 600;
        }

        .table thead th {
            background-color: #f8f9fa;
            /* Light background for table header */
            font-weight: 600;
        }

        .table tbody tr:hover {
            background-color: #f0f4f8;
            /* Lighter hover effect */
        }

        .modal-content {
            border-radius: 1rem;
            /* More rounded corners for modals */
            overflow: hidden;
            /* Ensure content respects border-radius */
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
            /* Fade out after 2.5s delay */
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
    </style>
@endsection
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

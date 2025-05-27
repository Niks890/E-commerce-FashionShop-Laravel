@can('managers')
    @extends('admin.master')

    @section('title', 'Danh sách khách hàng')

@section('content')
    <div class="container mt-4">
        <h3 class="mb-3">Danh sách khách hàng</h3>

        <!-- Form tìm kiếm và lọc -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('customer.index') }}">
                    <div class="row g-3">
                        <!-- Thanh tìm kiếm -->
                        <div class="col-md-4">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Tìm theo tên, email, số điện thoại...">
                        </div>

                        <!-- Lọc từ ngày -->
                        <div class="col-md-2">
                            <label for="from_date" class="form-label">Từ ngày</label>
                            <input type="date" class="form-control" id="from_date" name="from_date"
                                value="{{ request('from_date') }}">
                        </div>

                        <!-- Lọc đến ngày -->
                        <div class="col-md-2">
                            <label for="to_date" class="form-label">Đến ngày</label>
                            <input type="date" class="form-control" id="to_date" name="to_date"
                                value="{{ request('to_date') }}">
                        </div>

                        <!-- Lọc theo số đơn hàng -->
                        <div class="col-md-2">
                            <label for="order_count" class="form-label">Số đơn hàng</label>
                            <select class="form-select" id="order_count" name="order_count">
                                <option value="">Tất cả</option>
                                <option value="0" {{ request('order_count') == '0' ? 'selected' : '' }}>Chưa có đơn
                                </option>
                                <option value="1-5" {{ request('order_count') == '1-5' ? 'selected' : '' }}>1-5 đơn
                                </option>
                                <option value="6-10" {{ request('order_count') == '6-10' ? 'selected' : '' }}>6-10 đơn
                                </option>
                                <option value="11+" {{ request('order_count') == '11+' ? 'selected' : '' }}>Trên 10 đơn
                                </option>
                            </select>
                        </div>

                        <!-- Sắp xếp theo đơn hàng -->
                        <div class="col-md-2">
                            <label for="order_sort" class="form-label">Sắp xếp</label>
                            <select class="form-select" id="order_sort" name="order_sort">
                                <option value="">Mặc định</option>
                                <option value="most_orders" {{ request('order_sort') == 'most_orders' ? 'selected' : '' }}>
                                    Nhiều đơn nhất</option>
                                <option value="least_orders"
                                    {{ request('order_sort') == 'least_orders' ? 'selected' : '' }}>Ít đơn nhất</option>
                            </select>
                        </div>

                        <!-- Lọc theo voucher -->
                        <div class="col-md-2">
                            <label for="voucher_status" class="form-label">Voucher</label>
                            <select class="form-select" id="voucher_status" name="voucher_status">
                                <option value="">Tất cả</option>
                                <option value="has_voucher"
                                    {{ request('voucher_status') == 'has_voucher' ? 'selected' : '' }}>Đã tặng voucher
                                </option>
                                <option value="no_voucher"
                                    {{ request('voucher_status') == 'no_voucher' ? 'selected' : '' }}>Chưa tặng voucher
                                </option>
                            </select>
                        </div>

                        <!-- Nút tìm kiếm và reset -->
                        <div class="col-md-12 d-flex justify-content-center gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                            <a href="{{ route('customer.index') }}" class="btn btn-secondary">
                                <i class="fas fa-refresh"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Hiển thị kết quả tìm kiếm -->
        @if (request()->hasAny(['search', 'from_date', 'to_date', 'order_count']))
            <div class="alert alert-info">
                <strong>Kết quả tìm kiếm:</strong>
                Tìm thấy {{ $customers->total() }} khách hàng
                @if (request('search'))
                    với từ khóa "<strong>{{ request('search') }}</strong>"
                @endif
                @if (request('from_date') || request('to_date'))
                    từ {{ request('from_date') ? date('d/m/Y', strtotime(request('from_date'))) : 'đầu' }}
                    đến {{ request('to_date') ? date('d/m/Y', strtotime(request('to_date'))) : 'cuối' }}
                @endif
                @if (request('order_count'))
                    với số đơn hàng: {{ request('order_count') }}
                @endif
                @if (request('order_sort'))
                    sắp xếp: {{ request('order_sort') == 'most_orders' ? 'Nhiều đơn nhất' : 'Ít đơn nhất' }}
                @endif
                @if (request('voucher_status'))
                    {{ request('voucher_status') == 'has_voucher' ? 'đã tặng voucher' : 'chưa tặng voucher' }}
                @endif
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tên khách hàng</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th>Giới tính</th>
                        <th>Avatar</th>
                        <th>Ngày tạo</th>
                        <th>Số đơn hàng</th>
                        {{-- <th>Voucher đã tặng</th> --}}
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>
                                <span class="badge {{ $customer->sex == 1 ? 'bg-primary' : 'bg-info' }}">
                                    {{ $customer->sex == 1 ? 'Nam' : 'Nữ' }}
                                </span>
                            </td>
                            <td>
                                <img src="{{ $customer->avatar ?? asset('client/img/user.png') }}" width="45"
                                    height="45" class="rounded-circle">
                            </td>
                            <td>{{ $customer->created_at->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-success">{{ $customer->orders_count ?? 0 }}</span>
                            </td>
                            {{-- <td>
                                    @if ($customer->vouchers_count > 0)
                                        <span class="badge bg-info">{{ $customer->vouchers_count }} voucher</span>
                                    @else
                                        <span class="badge bg-secondary">Chưa có</span>
                                    @endif
                                </td> --}}
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                    data-bs-target="#voucherModal" data-customer-id="{{ $customer->id }}"
                                    data-customer-name="{{ $customer->name }}">
                                    <i class="fas fa-gift"></i> Tặng voucher
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p>Không tìm thấy khách hàng nào</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {{ $customers->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Modal tặng voucher -->
    <div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="voucherModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="voucherModalLabel">
                        <i class="fas fa-gift me-2"></i>Tặng voucher cho khách hàng
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="voucherForm">
                        <input type="hidden" id="customerId" name="customer_id">

                        <!-- Thông tin khách hàng -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-user me-2"></i>Khách hàng:</h6>
                            <p class="mb-0" id="customerName"></p>
                        </div>

                        <div class="row">
                            <!-- Loại voucher -->
                            <div class="col-md-6">
                                <label for="voucherType" class="form-label">Loại voucher <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="voucherType" name="voucher_type" required>
                                    <option value="">Chọn loại voucher</option>
                                    <option value="discount_percent">Giảm giá theo %</option>
                                    <option value="discount_amount">Giảm giá theo số tiền</option>
                                    <option value="free_shipping">Miễn phí vận chuyển</option>
                                    <option value="buy_get_free">Mua X tặng Y</option>
                                </select>
                            </div>

                            <!-- Giá trị voucher -->
                            <div class="col-md-6">
                                <label for="voucherValue" class="form-label">Giá trị <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="voucherValue" name="voucher_value"
                                        placeholder="Nhập giá trị" required>
                                    <span class="input-group-text" id="valueUnit">%</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <!-- Giá trị đơn hàng tối thiểu -->
                            <div class="col-md-6">
                                <label for="minOrderValue" class="form-label">Đơn hàng tối thiểu</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="minOrderValue" name="min_order_value"
                                        placeholder="0">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>

                            <!-- Giá trị giảm tối đa -->
                            <div class="col-md-6">
                                <label for="maxDiscount" class="form-label">Giảm tối đa</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="maxDiscount" name="max_discount"
                                        placeholder="Không giới hạn">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <!-- Ngày bắt đầu -->
                            <div class="col-md-6">
                                <label for="startDate" class="form-label">Ngày bắt đầu <span
                                        class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="startDate" name="start_date"
                                    required>
                            </div>

                            <!-- Ngày kết thúc -->
                            <div class="col-md-6">
                                <label for="endDate" class="form-label">Ngày kết thúc <span
                                        class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="endDate" name="end_date"
                                    required>
                            </div>
                        </div>

                        <!-- Số lần sử dụng -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="usageLimit" class="form-label">Số lần sử dụng</label>
                                <input type="number" class="form-control" id="usageLimit" name="usage_limit"
                                    placeholder="1" value="1" min="1">
                            </div>

                            <!-- Trạng thái -->
                            <div class="col-md-6">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active">Kích hoạt</option>
                                    <option value="inactive">Tạm dừng</option>
                                </select>
                            </div>
                        </div>

                        <!-- Mô tả -->
                        <div class="mt-3">
                            <label for="description" class="form-label">Mô tả voucher</label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                placeholder="Nhập mô tả chi tiết về voucher..."></textarea>
                        </div>

                        <!-- Ghi chú -->
                        <div class="mt-3">
                            <label for="note" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="note" name="note" rows="2" placeholder="Ghi chú thêm..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Hủy
                    </button>
                    <button type="button" class="btn btn-success" id="sendVoucherBtn">
                        <i class="fas fa-paper-plane me-1"></i>Tặng voucher
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const voucherModal = document.getElementById('voucherModal');
            const voucherType = document.getElementById('voucherType');
            const valueUnit = document.getElementById('valueUnit');
            const sendVoucherBtn = document.getElementById('sendVoucherBtn');

            // Xử lý khi mở modal
            voucherModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const customerId = button.getAttribute('data-customer-id');
                const customerName = button.getAttribute('data-customer-name');

                document.getElementById('customerId').value = customerId;
                document.getElementById('customerName').textContent = customerName;
            });

            // Thay đổi đơn vị khi chọn loại voucher
            voucherType.addEventListener('change', function() {
                const selectedType = this.value;
                if (selectedType === 'discount_percent') {
                    valueUnit.textContent = '%';
                } else if (selectedType === 'discount_amount') {
                    valueUnit.textContent = 'VNĐ';
                } else if (selectedType === 'free_shipping') {
                    valueUnit.textContent = 'VNĐ';
                } else {
                    valueUnit.textContent = '';
                }
            });

            // Xử lý gửi voucher (chỉ UI)
            sendVoucherBtn.addEventListener('click', function() {
                // Validation cơ bản
                const form = document.getElementById('voucherForm');
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (isValid) {
                    // Hiển thị loading
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Đang gửi...';
                    this.disabled = true;

                    // Giả lập gửi voucher (chỉ UI)
                    setTimeout(() => {
                        alert('Đã tặng voucher thành công!');

                        // Reset form và đóng modal
                        form.reset();
                        const modal = bootstrap.Modal.getInstance(voucherModal);
                        modal.hide();

                        // Reset button
                        this.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Tặng voucher';
                        this.disabled = false;
                    }, 1500);
                } else {
                    alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
                }
            });

            // Reset validation khi đóng modal
            voucherModal.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('voucherForm');
                form.reset();
                form.querySelectorAll('.is-invalid').forEach(field => {
                    field.classList.remove('is-invalid');
                });
            });
        });
    </script>

@endsection
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

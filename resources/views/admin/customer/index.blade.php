{{-- resources/views/admin/customer/index.blade.php --}}

@extends('admin.master')

@section('title', 'Danh sách khách hàng')

@section('content')
    <div class="container mt-4">
        <h3 class="mb-3">Danh sách khách hàng</h3>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('customer.index') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Tìm theo tên, email, số điện thoại...">
                        </div>

                        <div class="col-md-2">
                            <label for="from_date" class="form-label">Từ ngày</label>
                            <input type="date" class="form-control" id="from_date" name="from_date"
                                value="{{ request('from_date') }}">
                        </div>

                        <div class="col-md-2">
                            <label for="to_date" class="form-label">Đến ngày</label>
                            <input type="date" class="form-control" id="to_date" name="to_date"
                                value="{{ request('to_date') }}">
                        </div>

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

        @if (request()->hasAny(['search', 'from_date', 'to_date', 'order_count', 'voucher_status']))
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
                        <th>Voucher đã tặng</th>
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
                            <td>
                                @if ($customer->voucherUsages->count() > 0)
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                        data-bs-target="#vouchersModal{{ $customer->id }}">
                                        {{ $customer->voucherUsages->count() }} voucher
                                    </button>

                                    <div class="modal fade" id="vouchersModal{{ $customer->id }}" tabindex="-1"
                                        aria-labelledby="vouchersModalLabel{{ $customer->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="vouchersModalLabel{{ $customer->id }}">
                                                        Voucher đã tặng cho {{ $customer->name }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @if ($customer->voucherUsages->isNotEmpty())
                                                        <ul class="list-group">
                                                            @foreach ($customer->voucherUsages as $usage)
                                                                <li class="list-group-item">
                                                                    <strong>Mã:</strong>
                                                                    {{ $usage->voucher->vouchers_code }} <br>
                                                                    <strong>Mô tả:</strong>
                                                                    {{ $usage->voucher->vouchers_description }} <br>
                                                                    <strong>Ngày tặng:</strong>
                                                                    {{ $usage->created_at->format('d/m/Y H:i') }} <br>
                                                                    <strong>Ngày hết hạn:</strong>
                                                                    @if ($usage->expiry_date)
                                                                        <span
                                                                            class="badge bg-warning">{{ \Carbon\Carbon::parse($usage->expiry_date)->format('d/m/Y H:i') }}</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">Không có thời
                                                                            hạn</span>
                                                                    @endif
                                                                    <br>
                                                                    <strong>Trạng thái:</strong>
                                                                    @if ($usage->used_at)
                                                                        <span class="badge bg-success">Đã sử dụng lúc:
                                                                            {{ \Carbon\Carbon::parse($usage->used_at)->format('d/m/Y H:i') }}</span>
                                                                    @elseif ($usage->expiry_date && \Carbon\Carbon::parse($usage->expiry_date)->isPast())
                                                                        <span class="badge bg-danger">Đã hết hạn</span>
                                                                    @else
                                                                        <span class="badge bg-primary">Chưa sử dụng</span>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p>Không có voucher nào được tặng cho khách hàng này.</p>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Đóng</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-secondary">Chưa có</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-success send-voucher-btn"
                                    data-customer-id="{{ $customer->id }}" data-customer-name="{{ $customer->name }}">
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

    <div id="voucherFormContainer" class="d-none">
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-gift me-2"></i>Tặng voucher cho: <span id="selectedCustomerName"></span>
                </h5>
            </div>
            <div class="card-body">
                <form id="voucherForm" action="{{ route('customer.sendVoucher') }}" method="POST">
                    @csrf
                    <input type="hidden" id="customerId" name="customer_id">

                    <div class="row">
                        <div class="col-md-6">
                            <label for="voucher_id" class="form-label">Chọn voucher <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="voucher_id" name="voucher_id" required>
                                <option value="">-- Chọn voucher --</option>
                                @foreach ($vouchers as $voucher)
                                    <option value="{{ $voucher->id }}">
                                        {{ $voucher->vouchers_code }} - {{ $voucher->vouchers_description }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="message" class="form-label">Lời nhắn (nếu có)</label>
                        <textarea class="form-control" id="message" name="message" rows="2"></textarea>
                    </div>

                    <div class="mt-3 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" id="cancelVoucherBtn">
                            <i class="fas fa-times me-1"></i> Hủy
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane me-1"></i> Gửi voucher
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const voucherFormContainer = document.getElementById('voucherFormContainer');
            const sendVoucherBtns = document.querySelectorAll('.send-voucher-btn');
            const cancelVoucherBtn = document.getElementById('cancelVoucherBtn');

            // Xử lý khi click nút tặng voucher
            sendVoucherBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const customerId = this.getAttribute('data-customer-id');
                    const customerName = this.getAttribute('data-customer-name');

                    // Đặt giá trị cho form
                    document.getElementById('customerId').value = customerId;
                    document.getElementById('selectedCustomerName').textContent = customerName;

                    // Hiển thị form
                    voucherFormContainer.classList.remove('d-none');

                    // Cuộn đến form
                    voucherFormContainer.scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });

            // Xử lý khi click hủy
            cancelVoucherBtn.addEventListener('click', function() {
                voucherFormContainer.classList.add('d-none');
            });

            // Xử lý submit form
            document.getElementById('voucherForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const form = this;
                const submitBtn = form.querySelector('button[type="submit"]');

                // Hiển thị loading
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang gửi...';
                submitBtn.disabled = true;

                // Gửi form bằng AJAX
                fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Đã gửi voucher thành công!');
                            window.location.reload();
                        } else {
                            alert('Có lỗi xảy ra: ' + (data.message || 'Vui lòng thử lại sau'));
                            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Gửi voucher';
                            submitBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        alert('Có lỗi xảy ra: ' + error);
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Gửi voucher';
                        submitBtn.disabled = false;
                    });
            });
        });
    </script>
@endsection

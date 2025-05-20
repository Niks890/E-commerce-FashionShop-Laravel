@can('salers')
@extends('admin.master')
@section('title', 'Đơn hàng đã xử lý')

@section('content')
    @if (Session::has('success'))
        <div class="alert alert-success d-flex align-items-center shadow-sm py-2 px-3 mb-3 js-div-dissappear" style="max-width: 26rem;">
            <i class="fas fa-check-circle me-2"></i>
            <span>{{ Session::get('success') }}</span>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('order.search') }}" class="mb-3">
                @csrf
                <div class="input-group">
                    <input name="query" type="text" class="form-control" placeholder="Nhập ID đơn hàng hoặc số điện thoại để tìm kiếm..." />
                    <button class="btn btn-primary" type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>

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
                    <tbody>
                        @forelse ($data as $model)
                            <tr>
                                <td>{{ $model->id }}</td>
                                <td>{{ $model->customer_name }}</td>
                                <td>{{ $model->address }}</td>
                                <td>{{ $model->phone }}</td>
                                <td>{{ number_format($model->total, 0, ',', '.') }} đ</td>
                                <td class="text-warning fw-bold">{{ $model->status }}</td>
                                <td>{{ $model->created_at }}</td>
                                <td class="text-center">
                                    <a href="{{ route('order.show', $model->id) }}" class="btn btn-sm btn-secondary me-1" title="Xem chi tiết">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    @if ($model->status === 'Đã xử lý')
                                        <button
                                            class="btn btn-sm btn-success btn-assign-shipper"
                                            data-order-id="{{ $model->id }}"
                                            title="Đưa cho đơn vị vận chuyển"
                                            type="button"
                                            >
                                            <i class="fa fa-truck"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">Không có đơn hàng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $data->links() }}
            </div>
        </div>
    </div>

    <!-- Modal chọn nhân viên giao hàng -->
    <div class="modal fade" id="assignShipperModal" tabindex="-1" aria-labelledby="assignShipperModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form id="assignShipperForm" method="POST" action="">
            @csrf
            <input type="hidden" name="order_id" id="modalOrderId" value="">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="assignShipperModalLabel">Chọn nhân viên giao hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                    <label for="shipperSelect" class="form-label">Nhân viên giao hàng</label>
                    <select id="shipperSelect" name="shipper_id" class="form-select" required>
                        <option value="" selected disabled>Chọn nhân viên</option>
                        <option value="1">Nguyễn Văn A</option>
                        <option value="2">Trần Thị B</option>
                        <option value="3">Lê Văn C</option>
                    </select>
                </div>
                <div id="shipperOrderCount" class="alert alert-info d-none">
                    <strong>Số đơn đang giao: </strong> <span id="orderCountNumber">0</span>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary">Xác nhận</button>
              </div>
            </div>
        </form>
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
        document.addEventListener('DOMContentLoaded', function() {
            const shipperData = {
                1: 3, // Nguyễn Văn A đang giao 3 đơn
                2: 5, // Trần Thị B đang giao 5 đơn
                3: 1, // Lê Văn C đang giao 1 đơn
            };

            const assignButtons = document.querySelectorAll('.btn-assign-shipper');
            const modal = new bootstrap.Modal(document.getElementById('assignShipperModal'));
            const modalOrderIdInput = document.getElementById('modalOrderId');
            const shipperSelect = document.getElementById('shipperSelect');
            const shipperOrderCountDiv = document.getElementById('shipperOrderCount');
            const orderCountNumberSpan = document.getElementById('orderCountNumber');

            assignButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const orderId = button.getAttribute('data-order-id');
                    modalOrderIdInput.value = orderId;

                    // Reset modal khi mở
                    shipperSelect.value = "";
                    shipperOrderCountDiv.classList.add('d-none');
                    orderCountNumberSpan.textContent = "0";

                    modal.show();
                });
            });

            shipperSelect.addEventListener('change', () => {
                const shipperId = shipperSelect.value;
                if(shipperId && shipperData[shipperId] !== undefined) {
                    orderCountNumberSpan.textContent = shipperData[shipperId];
                    shipperOrderCountDiv.classList.remove('d-none');
                } else {
                    shipperOrderCountDiv.classList.add('d-none');
                    orderCountNumberSpan.textContent = "0";
                }
            });
        });
    </script>
@endsection

@else
    {{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

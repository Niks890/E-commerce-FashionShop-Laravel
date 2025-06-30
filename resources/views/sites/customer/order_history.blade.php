@extends('sites.master')
@section('title', 'Lịch sử đơn hàng')

@section('content')
    @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-lg p-2 move-from-top js-div-dissappear"
            role="alert" style="width: 26rem; display:flex; text-align:center">
            <i class="fas fa-check p-2 bg-success text-white rounded-circle pe-2 mx-2"></i>
            {{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container mt-4">
        <h3 class="mb-4 text-center">Lịch sử đơn hàng của bạn</h3>

        <!-- Form tìm kiếm và lọc -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <button class="btn btn-link w-100 text-start d-flex align-items-center text-dark text-decoration-none"
                        type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="true"
                        aria-controls="filterCollapse">
                        <i class="fas fa-filter me-2"></i>
                        <span class="fw-semibold">Bộ lọc tìm kiếm</span>
                    </button>
                </h5>
            </div>
            <div id="filterCollapse" class="collapse show">
                <div class="card-body pt-3">
                    <form method="GET" action="{{ route('sites.getHistoryOrder') }}">
                        <div class="row g-3">
                            <!-- Tìm kiếm theo ID hoặc SĐT -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="query" class="form-label mb-1">Tìm kiếm theo ID/SĐT:</label>
                                    <input name="query" id="query" type="text" class="form-control"
                                        placeholder="Nhập ID đơn hàng hoặc số điện thoại..."
                                        value="{{ request()->query('query') }}">
                                </div>
                            </div>

                            <!-- Lọc theo trạng thái -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div>Lọc theo trạng thái:</div>
                                    <select name="status" id="status" class="form-select">
                                        <option value="">-- Tất cả trạng thái --</option>
                                        @foreach ($statusList as $status)
                                            <option value="{{ $status }}"
                                                {{ request()->query('status') == $status ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Lọc theo ngày bắt đầu -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_from" class="form-label mb-1">Từ ngày:</label>
                                    <input name="date_from" id="date_from" type="date" class="form-control"
                                        value="{{ request()->query('date_from') }}">
                                </div>
                            </div>

                            <!-- Lọc theo ngày kết thúc -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_to" class="form-label mb-1">Đến ngày:</label>
                                    <input name="date_to" id="date_to" type="date" class="form-control"
                                        value="{{ request()->query('date_to') }}">
                                </div>
                            </div>

                            <!-- Nút hành động -->
                            <div class="col-12 mt-2">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i> Tìm kiếm
                                    </button>
                                    <a href="{{ route('sites.getHistoryOrder') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo me-1"></i> Đặt lại
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Hiển thị kết quả lọc -->
        @if (request()->hasAny(['query', 'status', 'date_from', 'date_to']))
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i>
                Đang hiển thị kết quả lọc
                @if (request()->query('query'))
                    - Tìm kiếm: "<strong>{{ request()->query('query') }}</strong>"
                @endif
                @if (request()->query('status'))
                    - Trạng thái: "<strong>{{ request()->query('status') }}</strong>"
                @endif
                @if (request()->query('date_from'))
                    - Từ ngày: "<strong>{{ request()->query('date_from') }}</strong>"
                @endif
                @if (request()->query('date_to'))
                    - Đến ngày: "<strong>{{ request()->query('date_to') }}</strong>"
                @endif
                ({{ $historyOrder->total() }} kết quả)
            </div>
        @endif

        <!-- Bảng lịch sử đơn hàng -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Tên khách hàng</th>
                        <th style="max-width: 200px;">Địa chỉ</th>
                        <th>SĐT</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày đặt</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($historyOrder as $item)
                        <tr id="orderRow{{ $item->id }}">
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->customer_name }}</td>
                            <td class="text-truncate" style="max-width: 200px;" title="{{ $item->address }}">
                                {{ Str::words($item->address, 5, '...') }}
                            </td>
                            <td>{{ $item->phone }}</td>
                            <td>{{ number_format($item->total, 0, ',', '.') }} đ</td>
                            <td>
                                <span id="status{{ $item->id }}"
                                    class="badge
                                    @if ($item->status == 'Chờ xử lý') bg-warning
                                    @elseif($item->status == 'Đã thanh toán' || $item->status == 'Giao hàng thành công') bg-success
                                    @elseif($item->status == 'Đã huỷ đơn hàng') bg-danger
                                    @elseif($item->status == 'Đang giao hàng' || $item->status == 'Đã xử lý') bg-info
                                    @else bg-secondary @endif">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td>{{ date('d/m/Y H:i', strtotime($item->created_at)) }}</td>

                            <td class="text-center" id="action{{ $item->id }}">
                                <div class="d-flex justify-content-center action-buttons">
                                    <a href="{{ route('sites.showOrderDetailOfCustomer', $item->id) }}"
                                        class="btn btn-sm btn-secondary">
                                        <i class="fa fa-eye"></i> Xem
                                    </a>

                                    <a href="{{ route('order.orderTracking', $item->id) }}"
                                        class="btn btn-sm btn-info ms-2">
                                        <i class="fa fa-truck"></i>
                                    </a>
                                    @if ($item->status === 'Chờ xử lý')
                                        <button type="button" class="btn btn-sm btn-danger ms-2"
                                            onclick="openCancelModal({{ $item->id }})">
                                            <i class="fa fa-times"></i> Hủy
                                        </button>
                                    @elseif ($item->status === 'Đã thanh toán' || $item->status === 'Giao hàng thành công')
                                        <button type="button" class="btn btn-sm btn-success ms-2"
                                            onclick="openSidebar({{ $item->id }})">
                                            <i class="fa fa-comments"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($historyOrder->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fa fa-box-open fa-2x"></i>
                                <p class="mt-2">Không có đơn hàng nào</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-content-center mt-3 mb-3">
        {{ $historyOrder->links() }}
    </div>

    <!-- Sidebar -->
    <div id="ratingSidebar" class="sidebar">
        <div class="sidebar-header">
            <h5 class="text-white fw-bold">Đánh Giá Sản Phẩm</h5>
            <button type="button" class="btn-close" onclick="closeSidebar()">X</button>
        </div>
        <div class="sidebar-body">
            <!-- Danh sách sản phẩm -->
            <div class="product-list">
            </div>
        </div>
    </div>

    <!-- Modal Xác nhận Hủy -->
    <div class="modal fade cancel-order-modal" id="cancelOrderModal" tabindex="-1"
        aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="cancelOrderModalLabel">Xác nhận hủy đơn hàng</h5>
                    <button type="button" class="btn-close-modal bg-danger border-0 text-white fw-bold"
                        data-bs-dismiss="modal" aria-label="Close">X</button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn hủy đơn hàng này không?</p>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Lý do hủy</label>
                        <textarea id="reason" class="form-control" placeholder="Nhập lý do hủy..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close-modal"
                        data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-danger" onclick="confirmCancel()">Xác nhận hủy</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('client/css/order-history.css') }}">
    <style>
        .card-header .btn-link {
            text-decoration: none;
            color: #495057;
        }

        .badge {
            font-size: 0.75em;
        }

        .alert-info {
            border-left: 4px solid #17a2b8;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
        }

        .card-header {
            padding: 0.75rem 1.25rem;
        }

        .btn-outline-secondary {
            border-color: #dee2e6;
        }

        /* Improved address column styling */
        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
            max-width: 100%;
        }

        /* Responsive table adjustments */
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .action-buttons {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .action-buttons .btn {
                margin-left: 0 !important;
            }
        }

        /* Hover effect for address to show full text */
        td.text-truncate:hover {
            position: relative;
            z-index: 1;
            white-space: normal;
            word-break: break-word;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: none;
        }
    </style>
@endsection

@section('js')
    @if (Session::has('success'))
        <script src="{{ asset('assets/js/message.js') }}"></script>
    @endif

    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        let currentOrderId = null;

        function openCancelModal(orderId) {
            currentOrderId = orderId;
            $('#reason').val(''); // Xóa nội dung cũ
            $('#cancelOrderModal').modal('show');
        }

        function showToast(type, message) {
            toastr[type](message);
        }

        function confirmCancel() {
            const reason = document.getElementById('reason').value;
            if (!reason.trim()) {
                showToast('error', 'Vui lòng nhập lý do hủy!');
                return;
            }

            $.ajax({
                url: "{{ route('sites.cancelOrder', ':id') }}".replace(':id', currentOrderId),
                type: "PUT",
                data: {
                    _token: "{{ csrf_token() }}",
                    reason: reason
                },
                success: function(response) {
                    showToast('success', response.message);
                    $("#status" + currentOrderId).text("Đã hủy").removeClass('bg-warning').addClass(
                    'bg-danger');
                    $("#action" + currentOrderId).html(`
                        <div class="action-buttons">
                            <a href="{{ route('sites.showOrderDetailOfCustomer', '') }}/${currentOrderId}"
                                class="btn btn-sm btn-secondary">
                                <i class="fa fa-eye"></i> Xem
                            </a>
                        </div>
                    `);
                    $('#cancelOrderModal').modal('hide');
                    currentOrderId = null;
                },
                error: function(xhr) {
                    showToast('error', 'Có lỗi xảy ra, vui lòng thử lại!');
                }
            });
        }

        $(document).ready(function() {
            $('.btn-close-modal').on("click", function() {
                $("#cancelOrderModal").modal("hide");
            });

            // Auto set date_to to today if date_from is selected but date_to is empty
            $('#date_from').on('change', function() {
                if ($(this).val() && !$('#date_to').val()) {
                    $('#date_to').val(new Date().toISOString().split('T')[0]);
                }
            });
        });
    </script>
    <script>
        function closeSidebar() {
            document.getElementById("ratingSidebar").classList.remove("active");
        }

        function openSidebar(orderId) {
            orderIdComment = orderId;
            document.getElementById("ratingSidebar").classList.add("active");

            fetch(`http://127.0.0.1:8000/api/rate-order/${orderIdComment}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status_code === 200 && data.data) {
                        let ratings = data.data;
                        console.log(ratings);

                        // Lấy danh sách sản phẩm
                        const productList = document.querySelector(".product-list");
                        productList.innerHTML = "";

                        // Duyệt qua từng sản phẩm và thêm HTML vào
                        ratings.forEach((rating) => {
                            const productItem = document.createElement("div");
                            productItem.classList.add("product-item", "d-flex", "align-items-start", "mb-3");

                            let ratingForm = '';
                            if (rating.content != null && rating.star != null) {
                                // Nếu đã đánh giá thì hiển thị thông tin thay vì form
                                ratingForm = `
                                <hr>
                                <div class="review-item border-bottom pb-2 mb-2">
                                    <h6>Khách hàng: ${rating.customer_name}</h6>
                                    <div class="text-warning mt-1">
                                        <small class="text-dark">Đánh giá: </small>
                                         ${"★".repeat(rating.star)}${"☆".repeat(5 - rating.star)}
                                         </div>
                                    <small class="">Ngày gửi: ${rating.created_at}</small>
                                    <p>Nội dung: ${rating.content}</p>
                                </div>
                            `;
                            } else {
                                // Nếu chưa đánh giá thì hiển thị form đánh giá
                                ratingForm = `
                                <form action="{{ route('comments.store') }}" class="ratingForm" method="POST">
                                    @csrf
                                    <div class="star-rating d-flex gap-1 mb-2">
                                        <input type="radio" id="star5-${rating.product_id}" name="star" value="5"><label for="star5-${rating.product_id}">★</label>
                                        <input type="radio" id="star4-${rating.product_id}" name="star" value="4"><label for="star4-${rating.product_id}">★</label>
                                        <input type="radio" id="star3-${rating.product_id}" name="star" value="3"><label for="star3-${rating.product_id}">★</label>
                                        <input type="radio" id="star2-${rating.product_id}" name="star" value="2"><label for="star2-${rating.product_id}">★</label>
                                        <input type="radio" id="star1-${rating.product_id}" name="star" value="1"><label for="star1-${rating.product_id}">★</label>
                                    </div>
                                    <input type="hidden" name="order_id" value="${orderIdComment}">
                                    <input type="hidden" name="product_id" value="${rating.product_id}">
                                    <textarea class="form-control mb-2" rows="2" name="content" placeholder="Viết nhận xét..." required>${rating.content ?? ''}</textarea>
                                    <input type="submit" class="btn btn-success w-100" value="Gửi đánh giá">
                                </form>
                            `;
                            }

                            const productContent = `
                            <div class="d-flex w-100 p-2 rounded shadow-sm align-items-center" style="background-color: #f8f9fa; gap: 20px;">
                                <div class="image-wrapper" style="flex-shrink: 0;">
                                    <img src="${rating.image}" alt="Sản phẩm" width="100" class="product-image">
                                </div>
                                <div class="product-info ms-3" style="flex-grow: 1;">
                                    <h6 class="product-name-comment fw-bold mb-1">${rating.product_name}</h6>
                                    <p class="product-size-comment mb-1"><span class="fw-semibold">Màu Sắc:</span> ${rating.color}</p>
                                    <p class="product-color-comment mb-1"><span class="fw-semibold">Size:</span> ${rating.size}</p>
                                    ${ratingForm}
                                </div>
                            </div>
                        `;
                            productItem.innerHTML = productContent;
                            productList.appendChild(productItem);

                            // Gắn sự kiện submit cho từng form mới được tạo
                            const form = productItem.querySelector('.ratingForm');
                            if (form) {
                                form.addEventListener('submit', function(event) {
                                    event.preventDefault();
                                    let formData = new FormData(form);

                                    fetch(form.action, {
                                            method: 'POST',
                                            body: formData,
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector(
                                                    'input[name="_token"]').value
                                            }
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            console.log("Dữ liệu nhận từ server:", data);
                                            if (data.success) {
                                                alert("Cảm ơn bạn đã đánh giá!");

                                                // Tạo nội dung đánh giá thay thế form
                                                let newReview = `
                                                <div class="review-item border-bottom pb-2 mb-2">
                                                    <h6>${data.review.user_name}</h6>
                                                    <div class="text-warning">${"★".repeat(data.review.star)}${"☆".repeat(5 - data.review.star)}</div>
                                                    <small class="text-muted">${data.review.created_at}</small>
                                                    <p>${data.review.content}</p>
                                                </div>
                                            `;

                                                // Thay thế form đánh giá bằng nội dung đánh giá mới
                                                form.parentElement.innerHTML = newReview;
                                                form.reset();
                                            } else {
                                                alert("Lỗi: " + (data.message ||
                                                    "Đánh giá không thành công!"));
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Lỗi:', error);
                                            alert("Lỗi kết nối, vui lòng thử lại!");
                                        });
                                });
                            }

                        });
                    }
                })
                .catch(error => console.error("Lỗi khi lấy đánh giá:", error));
        }
    </script>
@endsection

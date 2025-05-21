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

        <!-- Form tìm kiếm -->
        <div class="row mb-3">
            <div class="col-md-8 mx-auto">
                <form method="GET" action="{{ route('sites.getHistoryOrder') }}">
                    <div class="input-group">
                        <input name="query" type="number" class="form-control"
                            placeholder="Nhập ID đơn hàng hoặc số điện thoại...">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i> Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bảng lịch sử đơn hàng -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Tên khách hàng</th>
                        <th>Địa chỉ</th>
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
                            <td>{{ $item->address }}</td>
                            <td>{{ $item->phone }}</td>
                            <td>{{ number_format($item->total, 0, ',', '.') }} đ</td>
                            <td>
                                <span id="status{{ $item->id }}" class="badge bg-warning">{{ $item->status }}</span>
                            </td>
                            <td>{{ $item->created_at }}</td>
                            {{-- <td class="text-center" id="action{{ $item->id }}">
                                <div class="d-flex justify-content-center action-buttons">
                                    <a href="{{ route('sites.showOrderDetailOfCustomer', $item->id) }}"
                                        class="btn btn-sm btn-secondary">
                                        <i class="fa fa-eye"></i> Xem
                                    </a>
                                    @if ($item->status === 'Chờ xử lý')
                                        <button type="button" class="btn btn-sm btn-danger ms-2"
                                            onclick="openCancelModal({{ $item->id }})">
                                            <i class="fa fa-times"></i> Hủy
                                        </button>
                                    @elseif ($item->status === 'Đã thanh toán')
                                        <button type="button" class="btn btn-sm btn-success ms-2"
                                            onclick="openSidebar({{ $item->id }})">
                                            <i class="fa fa-comments"></i> Đánh giá
                                        </button>
                                    @endif
                                </div>
                            </td> --}}
                            <td class="text-center" id="action{{ $item->id }}">
                                <div class="d-flex justify-content-center action-buttons">
                                    <a href="{{ route('sites.showOrderDetailOfCustomer', $item->id) }}"
                                        class="btn btn-sm btn-secondary">
                                        <i class="fa fa-eye"></i> Xem
                                    </a>

                                    <a href="{{route('order.orderTracking')}}" class="btn btn-sm btn-info ms-2">
                                        <i class="fa fa-truck"></i>
                                    </a>
                                    @if ($item->status === 'Chờ xử lý')
                                        <button type="button" class="btn btn-sm btn-danger ms-2"
                                            onclick="openCancelModal({{ $item->id }})">
                                            <i class="fa fa-times"></i> Hủy
                                        </button>
                                    @elseif ($item->status === 'Đã thanh toán')
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
    <div class="modal fade cancel-order-modal" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel"
        aria-hidden="true">
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
                    <button type="button" class="btn btn-secondary btn-close-modal" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-danger" onclick="confirmCancel()">Xác nhận hủy</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('client/css/order-history.css') }}">
@endsection


@section('js')
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
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
                    $("#status" + currentOrderId).text("Đã hủy");
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
                                    <img src="uploads/${rating.image}" alt="Sản phẩm" class="product-image">
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

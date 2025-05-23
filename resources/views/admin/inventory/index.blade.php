@can('warehouse workers')
    @extends('admin.master')
    @section('title', 'Thông tin Phiếu nhập hàng')
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

            {{-- Search Form --}}
            <form method="GET" action="{{ route('inventory.search') }}" class="row g-2 align-items-center">
                @csrf
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" name="query" class="form-control"
                            placeholder="Nhập tên nhân viên hoặc ID phiếu nhập..." />
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('inventory.create') }}" class="btn btn-success me-2">
                        <i class="fas fa-plus-circle me-1"></i> Thêm mới
                    </a>
                    <a href="{{ route('admin.revenueInventory') }}" class="btn btn-warning">
                        <i class="fas fa-warehouse me-1"></i> Quản lý kho
                    </a>
                </div>
            </form>

            {{-- Table --}}
            <div class="table-responsive mt-4">
                <table class="table table-hover table-bordered align-middle text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Sản phẩm</th>
                            <th>Hình ảnh</th>
                            <th>Nhà cung cấp</th>
                            <th>Nhân viên lập</th>
                            <th>Số lượng</th>
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
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Thông tin phiếu nhập</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <p><strong>Mã phiếu nhập:</strong> <span id="inventory-id"></span></p>
                            <p><strong>Nhân viên lập phiếu:</strong> <span id="staff-name"></span></p>
                            <p><strong>Danh mục sản phẩm:</strong> <span id="category-name"></span></p>
                            <p><strong>Nhà cung cấp:</strong> <span id="provider-name"></span></p>
                            <p><strong>Ngày tạo:</strong> <span id="iventory-created"></span></p>
                            <p><strong>Ngày sửa:</strong> <span id="iventory-updated"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tên sản phẩm:</strong> <span id="product-name"></span></p>
                            <p><strong>Thương hiệu:</strong> <span id="product-brand"></span></p>
                            <p><strong>Hình ảnh:</strong> <br>
                                <img id="product-image" src="" width="100" class="rounded border mt-2">
                            </p>
                            <p><strong>Giá nhập:</strong> <span id="product-price"></span></p>
                            <p><strong>Màu sắc:</strong> <span id="colors"></span></p>
                            <p><strong>Size & Số lượng:</strong> <span id="size_and_quantity"></span></p>
                            <p><strong>Tổng số lượng nhập:</strong> <span id="total_quantity"></span></p>
                            <p><strong>Tổng tiền:</strong> <span id="total_price"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Đóng
                    </button>
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
            fetchInventories(1);
        });

        function fetchInventories(page) {
            $.ajax({
                url: `http://127.0.0.1:8000/api/inventory?page=${page}`,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if (response.status_code === 200) {
                        let data = response.data;
                        let tbody = $("table tbody");
                        tbody.empty();

                        $.each(data, function(index, inventory) {
                            $.each(inventory.detail, function(i, dl) {
                                let totalMoney = dl.price * dl.quantity;
                                let row = `
                            <tr>
                                <td>${inventory.id}</td>
                                <td>${dl.product.name}</td>
                                <td><img src="${dl.product.image}" width="45"></td>
                                <td>${inventory.provider.name}</td>
                                <td>${inventory.staff.name}</td>
                                <td>${dl.quantity}</td>
                                <td>${parseFloat(dl.price).toLocaleString()} đ</td>
                                <td>${totalMoney.toLocaleString()} đ</td>
                              <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                                    <button type="button" class="btn btn-secondary btn-sm btn-inventory-detail">Chi tiết</button>
                                    <form method="GET" action="{{ route('inventory.add_extra') }}">
                                        @csrf
                                        <input type="hidden" name="inventory_id" value="${inventory.id}">
                                        <input type="submit" class="btn btn-success btn-sm btn-add-extra" value="Nhập thêm">
                                    </form>
                                </div>
                            </td>

                            </tr>
                        `;
                                tbody.append(row);
                            });
                        });

                        renderPagination(response.pagination);
                    } else {
                        console.error("Lỗi API:", response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Lỗi khi lấy dữ liệu:", xhr.responseText);
                }
            });
        }

        function renderPagination(pagination) {
            let paginationDiv = $("#pagination");
            paginationDiv.empty();

            const current = pagination.current_page;
            const last = pagination.last_page;

            // Helper: tạo nút trang
            function createPageButton(page, text = null, disabled = false, active = false) {
                let btnClass = "btn btn-primary btn-sm mx-1";
                if (disabled) btnClass += " disabled";
                if (active) btnClass += " active";

                let displayText = text || page;

                if (disabled) {
                    return `<button class="${btnClass}" disabled>${displayText}</button>`;
                } else {
                    return `<button class="${btnClass}" onclick="fetchInventories(${page})">${displayText}</button>`;
                }
            }

            // Nút prev
            paginationDiv.append(createPageButton(current - 1, "<", current <= 1));

            // Hiển thị khoảng trang xung quanh current
            let delta = 2; // số trang bên trái và phải của current hiển thị

            let rangeStart = Math.max(1, current - delta);
            let rangeEnd = Math.min(last, current + delta);

            // Nếu cách trang đầu nhiều thì hiển thị nút 1 + ...
            if (rangeStart > 1) {
                paginationDiv.append(createPageButton(1));
                if (rangeStart > 2) {
                    paginationDiv.append('<span class="mx-1 align-self-center">...</span>');
                }
            }

            // Các trang trong khoảng
            for (let i = rangeStart; i <= rangeEnd; i++) {
                paginationDiv.append(createPageButton(i, null, false, i === current));
            }

            // Nếu cách trang cuối nhiều thì hiển thị ... + nút last
            if (rangeEnd < last) {
                if (rangeEnd < last - 1) {
                    paginationDiv.append('<span class="mx-1 align-self-center">...</span>');
                }
                paginationDiv.append(createPageButton(last));
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
        $(document).ready(function() {
            // dùng event delegation để bắt sự kiện click do giao diện table được tạo ra sau khi load trang
            $("table").on("click", ".btn-inventory-detail", function(e) {
                e.preventDefault();
                let row = $(this).closest("tr");
                let inventory_id = row.find("td:first").text().trim();
                $.ajax({
                    url: `http://127.0.0.1:8000/api/inventoryDetail/${inventory_id}`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.status_code === 200) {
                            let inventory_detail = response.data;
                            $('#inventory-id').text(inventory_detail.id);
                            $('#staff-name').text(inventory_detail.staff.name);
                            $('#provider-name').text(inventory_detail.provider.name);
                            $('#total_price').text(parseFloat(inventory_detail.total_price)
                                .toLocaleString() + " đ");
                            $('#iventory-created').text(inventory_detail.createdate);
                            $('#iventory-updated').text(inventory_detail.updatedate);
                            if (inventory_detail.detail.length > 0) {
                                let productDetail = inventory_detail.detail[0];
                                $('#product-name').text(productDetail.product.name);
                                $('#product-brand').text(productDetail.product.brand);
                                $('#product-price').text(parseFloat(productDetail.price)
                                    .toLocaleString() + " đ");
                                $('#total_quantity').text(productDetail.quantity);
                                $('#category-name').text(productDetail.product.category.name);
                                $('#product-image').attr("src",
                                    `uploads/${productDetail.product.image}`);
                                //Xử lý hiển thị size và số lượng
                                // if(productDetail.sizes) {
                                //     let sizeQuantityList = productDetail.sizes.split(',')
                                //         .map(sizeQty => {
                                //             let parts = sizeQty.split(
                                //             '-'); // Tách chuỗi dựa vào dấu '-'
                                //             let size = parts[0];
                                //             let qty = parts[1];
                                //             let color = parts[2];
                                //             // if (color) {
                                //             //     $('#colors').text(color);
                                //             // }
                                //             return `${size}: (${qty} cái)`;
                                //         }).join(', ');
                                //     $('#size_and_quantity').text(sizeQuantityList);
                                // }
                                if (productDetail.sizes) {
                                    let colorsSet = new Set(); // để tránh trùng màu
                                    let sizeQuantityList = productDetail.sizes.split(',').map(
                                        sizeQty => {
                                            let parts = sizeQty.split('-');
                                            let size = parts[0];
                                            let qty = parts[1];
                                            let color = parts[2];
                                            if (color) {
                                                colorsSet.add(color);
                                            }
                                            return `${size}: (${qty} cái)`;
                                        }).join(', ');

                                    $('#size_and_quantity').text(sizeQuantityList);
                                    $('#colors').text(Array.from(colorsSet).join(
                                        ', ')); // Hiển thị danh sách màu không trùng
                                }

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

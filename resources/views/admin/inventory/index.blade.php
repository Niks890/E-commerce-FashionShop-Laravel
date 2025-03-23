@can('warehouse workers')
    @extends('admin.master')
    @section('title', 'Thông tin Phiếu nhập hàng')
@section('content')
    @if (Session::has('success'))
        <div class="shadow-lg p-2 move-from-top js-div-dissappear" style="width: 26rem; display:flex; text-align:center">
            <i class="fas fa-check p-2 bg-success text-white rounded-circle pe-2 mx-2"></i>{{ Session::get('success') }}
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <div class="card-sub">
                <form method="GET" class="form-inline row" action="{{ route('inventory.search') }}">
                    @csrf
                    <div
                        class="col-9 navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button type="submit" class="btn btn-search pe-1">
                                    <i class="fa fa-search search-icon"></i>
                                </button>
                            </div>
                            <input name="query" type="text"
                                placeholder="Nhập vào tên nhân viên lập hay ID phiếu nhập..." class="form-control" />
                        </div>
                    </div>
                    <div class="col-3">
                        <a href="{{ route('inventory.create') }}" type="submit" class="btn btn-success"><i
                                class="fa fa-plus"></i>Thêm mới</a>
                    </div>
                </form>
            </div>
            <table class="table mt-3">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Sản phẩm</th>
                        <th scope="col">Hình ảnh</th>
                        <th scope="col">Nhà cung cấp</th>
                        <th scope="col">Nhân viên lập</th>
                        <th scope="col">Số lượng nhập</th>
                        <th scope="col">Giá nhập</th>
                        <th scope="col">Tổng tiền</th>
                        <th scope="col" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
    {{-- chưa có xử lý phân trang --}}
    <div id="pagination" class="mt-3 d-flex justify-content-center"></div>


    <!-- Modal InventoryDetail -->
    <div class="modal fade" id="inventoryDetail" tabindex="-1" aria-labelledby="inventoryDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="inventoryDetailLabel">Thông tin phiếu nhập hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Mã phiếu nhập:</strong> <span id="inventory-id"></span></p>
                                <p><strong>Nhân viên lập phiếu:</strong> <span id="staff-name"></span></p>
                                <p><strong>Danh mục sản phẩm:</strong> <span id="category-name"></span></p>
                                <p><strong>Nhà cung cấp:</strong> <span id="provider-name"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Tên sản phẩm:</strong> <span id="product-name"></span></p>
                                <p><strong>Thương hiệu:</strong> <span id="product-brand"></span></p>
                                <p><strong>Hình ảnh:</strong> <img id="product-image" src="" width="80"></p>
                                <p><strong>Giá nhập:</strong> <span id="product-price"></span></p>

                            </div>
                            <div class="col-md-6">
                                <p><strong>Màu sắc:</strong> <span id="colors"></span></p>
                                <p><strong>Size & Số lượng:</strong> <span id="size_and_quantity"></span></p>
                                <p><strong>Tổng số lượng nhập:</strong> <span id="total_quantity"></span></p>
                                <p><strong>Tổng tiền:</strong> <span id="total_price"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Ngày tạo:</strong> <span id="iventory-created"></span></p>
                                <p><strong>Ngày sửa:</strong> <span id="iventory-updated"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer d-flex justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
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
                    console.log(response); // Kiểm tra JSON trả về trong Console

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
                                <td><img src="uploads/${dl.product.image}" width="45"></td>
                                <td>${inventory.provider.name}</td>
                                <td>${inventory.staff.name}</td>
                                <td>${dl.quantity}</td>
                                <td>${parseFloat(dl.price).toLocaleString()} đ</td>
                                <td>${totalMoney.toLocaleString()} đ</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-secondary btn-sm btn-inventory-detail">Chi tiết</button>
                                    <form method="GET" action="{{ route('inventory.add_extra') }}">
                                        @csrf
                                        <input type="hidden" name="inventory_id" value="${inventory.id}">
                                        <input type="submit" class="btn btn-success btn-sm btn-add-extra" value="Nhập thêm">
                                    </form>
                                </td>
                            </tr>
                        `;
                                tbody.append(row);
                            });
                        });

                        // Gọi hàm render phân trang
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

            let prevDisabled = pagination.prev_page_url ? "" : "disabled";
            let nextDisabled = pagination.next_page_url ? "" : "disabled";

            let paginationHtml =
                `
                <button class="btn btn-primary btn-sm mx-1" ${prevDisabled} onclick="fetchInventories(${pagination.current_page - 1})" style="font-size: 20px"><</button>
                <span class="align-self-center">Trang ${pagination.current_page} / ${pagination.last_page}</span>
                <button class="btn btn-primary btn-sm mx-1" ${nextDisabled} onclick="fetchInventories(${pagination.current_page + 1})" style="font-size: 20px">></button>`;
            paginationDiv.append(paginationHtml);
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
                                if(productDetail.sizes) {
                                    let sizeQuantityList = productDetail.sizes.split(',')
                                        .map(sizeQty => {
                                            let parts = sizeQty.split(
                                            '-'); // Tách chuỗi dựa vào dấu '-'
                                            let size = parts[0];
                                            let qty = parts[1];
                                            let color = parts[2];
                                            if (color) {
                                                $('#colors').text(color);
                                            }
                                            return `${size}: (${qty} cái)`;
                                        }).join(', ');
                                    $('#size_and_quantity').text(sizeQuantityList);
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

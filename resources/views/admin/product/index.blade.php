@can('salers')
    @extends('admin.master')
    @section('title', 'Thông tin Sản phẩm')
    @section('content')
        @if (Session::has('success'))
            <div class="shadow-lg p-2 move-from-top js-div-dissappear" style="width: 26rem; display:flex; text-align:center">
                <i class="fas fa-check p-2 bg-success text-white rounded-circle pe-2 mx-2"></i>{{ Session::get('success') }}
            </div>
        @endif
        <div class="card">
            <div class="card-body">
                <div class="card-sub">
                    <form method="GET" class="form-inline row" action="{{ route('product.search') }}">
                        @csrf
                        <div
                            class="col-9 navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button type="submit" class="btn btn-search pe-1">
                                        <i class="fa fa-search search-icon"></i>
                                    </button>
                                </div>
                                <input name="query" type="text" placeholder="Nhập vào tên sản phẩm cần tìm..."
                                    class="form-control" />
                            </div>
                        </div>
                        {{-- <div class="col-3">
                                <a href="{{ route('product.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>Thêm mới</a>
                            </div> --}}
                    </form>
                </div>
                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Tên sản phẩm</th>
                            <th scope="col">Danh mục</th>
                            <th scope="col">Giá bán</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col">Ngày thêm</th>
                            <th scope="col">Hình ảnh</th>
                            <th scope="col" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $model)
                            <tr>
                                <td>{{ $model->id }}</td>
                                <td>{{ $model->product_name }}</td>
                                <td>{{ $model->Category->category_name }}</td>
                                <td>{{ number_format($model->price, 0, ',', '.') }} đ</td>
                                <td>{{ $model->status == 0 ? 'Ẩn' : 'Hiển thị' }}</td>
                                <td>{{ $model->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <img src="uploads/{{ $model->image }}" alt="{{ $model->image }}" width="45">
                                </td>
                                <td>
                                    <form method="post" action="{{ route('product.destroy', $model->id) }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-secondary btn-detail">
                                            <i class="fa fa-pen"></i> Xem chi tiết
                                        </button>
                                        <a href="{{ route('product.edit', $model->id) }}" class="btn btn-sm btn-primary"><i
                                                class="fa fa-edit pe-2"></i>Sửa</a>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        </hr>
        <div class="d-flex justify-content-center mt-3">
            {{ $data->links() }}
        </div>
        <!-- Modal productDetail -->
        <div class="modal fade" id="productDetail" tabindex="-1" aria-labelledby="productDetailLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-secondary text-white">
                        <h5 class="modal-title" id="productDetailLabel">Thông tin sản phẩm: <span
                                id="product-info"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <tbody>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Mã sản phẩm:</td>
                                        <td style="width: 70%;"><span id="product-id"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Tên sản phẩm</td>
                                        <td style="width: 70%;"><span id="product-name"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Thương hiệu</td>
                                        <td style="width: 70%;"><span id="product-brand"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Giá sản phẩm:</td>
                                        <td style="width: 70%;"><span id="product-price"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Hình ảnh:</td>
                                        <td style="width: 70%;"><img id="product-image" width="45"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Mô tả sản phẩm:</td>
                                        <td style="width: 70%;"><span id="product-description"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Danh mục:</td>
                                        <td style="width: 70%;"><span id="category-name"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Màu sắc:</td>
                                        <td style="width: 70%;"><span id="colors"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Size:</td>
                                        <td style="width: 70%;"><span id="sizes"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Ngày tạo:</td>
                                        <td style="width: 70%;"><span id="product-created"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Ngày sửa:</td>
                                        <td style="width: 70%;"><span id="product-updated"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Modal footer -->
                        <div class="modal-footer d-flex justify-content-center">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        </div>
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
            @if ($errors->any())
                $(document).ready(function() {
                    $('#productDetail').addClass("open");
                })
            @endif
        </script>

        <script>
            $(document).ready(function() {
                $(".btn-detail").click(function(event) {
                    event.preventDefault();
                    let row = $(this).closest("tr");
                    let productId = row.find("td:first").text().trim();
                    $.ajax({
                        url: `http://127.0.0.1:8000/api/product/${productId}`, //url, type, datatype, success,
                        type: "GET",
                        dataType: "json",
                        success: function(response) {
                            if (response.status_code === 200) {
                                let productInfo = response.data;
                                // console.log(productInfo);
                                $("#product-id").text(productInfo.id);
                                $("#product-name").text(productInfo.name);
                                $("#product-brand").text(productInfo.brand);
                                $("#product-price").text(productInfo.price);
                                $("#product-image").attr("src", `uploads/${productInfo.image}`);
                                $("#product-description").text(productInfo.description);
                                $("#category-name").text(productInfo.category.name);
                                let color = [], size_and_stock = [];
                                $.each(productInfo["product-variant"], function(i, variant) {
                                    color.push(variant.color);
                                    size_and_stock.push(`${variant.color}-${variant.size} (${variant.stock} cái)`);
                                });
                                // console.log(color, size_and_stock)
                                $("#colors").text([...new Set(color)].join(', '));
                                $("#sizes").text(size_and_stock.join(', '));
                                $("#product-created").text(new Date(productInfo.created_at)
                                    .toLocaleString(
                                        'vi-VN'));
                                $("#product-updated").text(new Date(productInfo.updated_at)
                                    .toLocaleString('vi-VN'));
                                $("#productDetail").modal("show");
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
    @endsection
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan



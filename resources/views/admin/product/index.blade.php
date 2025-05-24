@can('salers')
    @extends('admin.master')

    @section('title', 'Thông tin Sản phẩm')

@section('content')
    @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow js-div-dissappear" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            {{-- Search Bar with Filters --}}
            <form method="GET" action="{{ route('product.search') }}" id="filterForm" class="mb-3">
                <div class="row g-2 align-items-end">
                    {{-- Input tìm kiếm --}}
                    <div class="col-lg-5 col-md-12"> {{-- Tăng kích thước cột để cân đối --}}
                        <label for="query" class="form-label visually-hidden">Tìm tên sản phẩm</label>
                        <div class="input-group">
                            <input type="text" name="query" id="query" class="form-control"
                                placeholder="Tìm tên sản phẩm..." value="{{ request('query') }}">
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Bộ lọc Danh mục --}}
                    <div class="col-lg-4 col-md-6 col-sm-6"> {{-- Tăng kích thước cột để cân đối --}}
                        <label for="category" class="form-label">Danh mục</label>
                        <select name="category" id="category" class="form-select">
                            <option value="">Tất cả danh mục</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Bộ lọc Giá --}}
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <label for="price_range" class="form-label">Giá</label>
                        <select name="price_range" id="price_range" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="0-100000" {{ request('price_range') == '0-100000' ? 'selected' : '' }}>Dưới
                                100.000đ</option>
                            <option value="100001-500000" {{ request('price_range') == '100001-500000' ? 'selected' : '' }}>
                                100.000đ - 500.000đ</option>
                            <option value="500001-1000000"
                                {{ request('price_range') == '500001-1000000' ? 'selected' : '' }}>500.000đ - 1.000.000đ
                            </option>
                            <option value="1000001-max" {{ request('price_range') == '1000001-max' ? 'selected' : '' }}>Trên
                                1.000.000đ</option>
                        </select>
                    </div>
                </div>

                {{-- Hàng mới cho các nút chức năng --}}
                <div class="row g-2 mt-3 justify-content-end"> {{-- Thêm mt-3 để tạo khoảng cách với hàng trên --}}
                    <div class="col-auto"> {{-- Sử dụng col-auto để nút có kích thước tự động --}}
                        <button type="button" id="clearFilters" class="btn btn-secondary">
                            <i class="fa fa-times"></i> Reset bộ lọc
                        </button>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('inventory.create') }}" class="btn btn-success">
                            <i class="fa fa-plus"></i> Nhập Hàng
                        </a>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.revenueInventory') }}" class="btn btn-warning">
                            <i class="fa fa-list"></i> Quản lý kho
                        </a>
                    </div>
                </div>
            </form>
            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Trạng thái</th>
                            <th>Ngày thêm</th>
                            <th>Ảnh</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $model)
                            <tr>
                                <td>{{ $model->id }}</td>
                                <td>{{ $model->product_name }}</td>
                                <td>{{ $model->category->category_name }}</td>
                                <td>{{ number_format($model->price, 0, ',', '.') }} đ</td>
                                <td>
                                    <span class="badge bg-{{ $model->status == 1 ? 'success' : 'secondary' }}">
                                        {{ $model->status == 1 ? 'Hiển thị' : 'Ẩn' }}
                                    </span>
                                </td>
                                <td>{{ $model->created_at->format('d/m/Y') }}</td>
                                <td><img src="{{ $model->image }}" alt="" width="45" class="rounded">
                                </td>
                                <td class="text-center">
                                    <form method="post" action="{{ route('product.destroy', $model->id) }}"
                                        class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-info btn-detail" title="Xem chi tiết">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <a href="{{ route('product.edit', $model->id) }}" class="btn btn-sm btn-primary"
                                            title="Sửa">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Không tìm thấy sản phẩm nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $data->links() }}
            </div>
        </div>
    </div>


    <div class="modal fade" id="productDetail" tabindex="-1" aria-labelledby="productDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="productDetailLabel">Thông tin sản phẩm: <span id="product-info"></span></h5>
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
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}">
@endsection

@section('js')
    @if (Session::has('success'))
        <script src="{{ asset('assets/js/message.js') }}"></script>
    @endif

    <script>
        $(document).ready(function() {
            // Tự động submit form khi thay đổi danh mục hoặc giá
            $('#category, #price_range').on('change', function() {
                $('#filterForm').submit();
            });

            // Xử lý nút "Xóa Bộ Lọc"
            $('#clearFilters').on('click', function() {
                $('#query').val(''); // Xóa nội dung ô tìm kiếm
                $('#category').val(''); // Đặt lại danh mục về "Tất cả"
                $('#price_range').val(''); // Đặt lại giá về "Tất cả"
                $('#filterForm').submit(); // Gửi lại form để xóa tất cả các bộ lọc
            });

            $(".btn-detail").click(function(event) {
                event.preventDefault();
                let productId = $(this).closest("tr").find("td:first").text().trim();
                $.ajax({
                    url: `http://127.0.0.1:8000/api/product/${productId}`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.status_code === 200) {
                            const p = response.data;
                            $("#product-id").text(p.id);
                            $("#product-name").text(p.name);
                            $("#product-brand").text(p.brand);
                            $("#product-price").text(p.price);
                            $("#product-image").attr("src", `${p.image}`);
                            $("#product-description").text(p.description);
                            $("#category-name").text(p.category.name);

                            let colors = [...new Set(p["product-variant"].map(v => v.color))];
                            let sizes = p["product-variant"].map(v =>
                                `${v.color}-${v.size} (${v.stock} cái)`);

                            $("#colors").text(colors.join(', '));
                            $("#sizes").text(sizes.join(', '));
                            $("#product-created").text(new Date(p.created_at).toLocaleString(
                                'vi-VN'));
                            $("#product-updated").text(new Date(p.updated_at).toLocaleString(
                                'vi-VN'));

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

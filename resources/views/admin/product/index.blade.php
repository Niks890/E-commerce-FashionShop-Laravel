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
    @if (Session::has('error'))
        <div class="shadow-lg p-2 move-from-top js-div-dissappear" style="width: 25rem; display:flex; text-align:center">
            <i class="fas fa-times p-2 bg-danger text-white rounded-circle pe-2 mx-2"></i>{{ Session::get('error') }}
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
                            <option value="1000001-max" {{ request('price_range') == '1000001-max' ? 'selected' : '' }}>
                                Trên
                                1.000.000đ</option>
                        </select>
                    </div>
                </div>

                {{-- Hàng mới cho bộ lọc trạng thái --}}
                <div class="row g-2 mt-2 align-items-end"> {{-- Thêm mt-2 để tạo khoảng cách --}}
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Trên kệ (hiển thị)
                            </option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Trong kho (ẩn)
                            </option>
                            <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Chờ duyệt vào kho (ẩn)
                            </option>
                        </select>
                    </div>


                    {{-- Bộ lọc Khuyến mãi MỚI --}}
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <label for="promotion_status" class="form-label">Khuyến mãi</label>
                        <select name="promotion_status" id="promotion_status" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="has_promotion"
                                {{ request('promotion_status') == 'has_promotion' ? 'selected' : '' }}>Có khuyến mãi
                            </option>
                            <option value="no_promotion"
                                {{ request('promotion_status') == 'no_promotion' ? 'selected' : '' }}>
                                Không có khuyến mãi</option>
                        </select>
                    </div>

                    {{-- Bộ lọc Stock MỚI --}}
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <label for="stock_range" class="form-label">Tồn kho</label>
                        <select name="stock_range" id="stock_range" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="out_of_stock" {{ request('stock_range') == 'out_of_stock' ? 'selected' : '' }}>
                                Hết hàng (0)</option>
                            <option value="low_stock" {{ request('stock_range') == 'low_stock' ? 'selected' : '' }}>Sắp hết
                                hàng (1-10)</option>
                            <option value="in_stock" {{ request('stock_range') == 'in_stock' ? 'selected' : '' }}>Còn hàng
                                (>10)</option>
                            <option value="all_stock_available"
                                {{ request('stock_range') == 'all_stock_available' ? 'selected' : '' }}>Còn hàng (Tổng
                                Stock > 0)</option>
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
                            <th>Giá Gốc / Khuyến mãi</th>
                            <th>Giá</th>
                            <th>Stock</th>
                            <th>Trạng thái</th>
                            <th>Khuyến mãi</th>
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
                                <td>
                                    @if ($model->has_active_discount)
                                        <s class="text-muted">{{ number_format($model->price, 0, ',', '.') }} đ</s><br>
                                        <span
                                            class="text-danger fw-bold">{{ number_format($model->discounted_price, 0, ',', '.') }}
                                            đ</span>
                                    @else
                                        {{ number_format($model->price, 0, ',', '.') }} đ
                                    @endif
                                </td>
                                <td>{{ number_format($model->price, 0, ',', '.') }} đ</td>
                                <td>
                                    @php
                                        $totalStock = $model->productVariants->sum('stock');
                                    @endphp
                                    @if ($totalStock == 0)
                                        <span class="badge bg-danger">Hết hàng/Chờ duyệt</span>
                                    @elseif ($totalStock > 0 && $totalStock <= 10)
                                        <span class="badge bg-warning">Sắp hết ({{ $totalStock }})</span>
                                    @else
                                        <span class="badge bg-success">Còn hàng ({{ $totalStock }})</span>
                                    @endif
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ $model->status == 1 ? 'success' : ($model->status == 2 ? 'warning' : 'secondary') }}">
                                        {{ $model->status == 1 ? 'Trên kệ (hiện)' : ($model->status == 2 ? 'Chờ duyệt vào kho (ẩn)' : 'Trong kho(ẩn)') }}
                                    </span>
                                </td>
                                <td>
                                    @if ($model->has_active_discount)
                                        <span class="badge bg-warning">Có KM
                                            ({{ $model->discount->percent_discount * 100 }}%)
                                        </span>
                                    @else
                                        <span class="badge bg-info">KM đã hết hạn hoặc không có</span>
                                    @endif
                                </td>
                                <td>{{ $model->created_at->format('d/m/Y') }}</td>
                                <td><img src="{{ $model->image }}" alt="" width="45" class="rounded">
                                </td>
                                <td class="text-center">
                                    <form method="post" action="{{ route('product.destroy', $model->id) }}"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')

                                        <!-- Luôn hiển thị nút Xem chi tiết -->
                                        <button type="button" class="btn btn-sm btn-info btn-detail"
                                            title="Xem chi tiết">
                                            <i class="fa fa-eye"></i>
                                        </button>

                                        @if ($model->status != 2)
                                            <!-- Nếu KHÔNG phải trạng thái chờ duyệt (2) thì hiển thị Sửa/Xóa -->
                                            <a href="{{ route('product.edit', $model->id) }}"
                                                class="btn btn-sm btn-primary" title="Sửa">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
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
                    <h5 class="modal-title" id="productDetailLabel">Thông tin sản phẩm: <span id="product-info"></span>
                    </h5>
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
                                    <td class="fw-bold text-start" style="width: 30%;">SKU:</td>
                                    <td style="width: 70%;"><span id="product-sku"></span></td>
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
    @if (Session::has('success') || Session::has('error'))
        <script src="{{ asset('assets/js/message.js') }}"></script>
    @endif

    <script>
        $(document).ready(function() {
            // Tự động submit form khi thay đổi danh mục, giá hoặc trạng thái
            $('#category, #price_range, #status, #promotion_status, #stock_range').on('change', function() {
                $('#filterForm').submit();
            });

            // Xử lý nút "Xóa Bộ Lọc"
            $('#clearFilters').on('click', function() {
                $('#query').val(''); // Xóa nội dung ô tìm kiếm
                $('#category').val(''); // Đặt lại danh mục về "Tất cả"
                $('#price_range').val(''); // Đặt lại giá về "Tất cả"
                $('#status').val(''); // Đặt lại trạng thái về "Tất cả"
                $('#promotion_status').val('');
                $('#stock_range').val('');
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
                            $("#product-sku").text(p.sku);
                            $("#product-name").text(p.name);
                            $("#product-brand").text(p.brand);
                            let priceHtml = '';
                            // console.log(p.discount);
                            if (p.discount && p.discount.status === 'active') {
                                // Chuyển đổi ngày tháng từ API sang đối tượng Date để so sánh
                                const startDate = new Date(p.discount.start_date);
                                const endDate = new Date(p.discount.end_date);
                                const now = new Date();

                                if (now >= startDate && now <= endDate) {
                                    let originalPrice = p.price;
                                    let discountPercentage = p.discount.percent_discount;
                                    let discountedPrice = originalPrice - (originalPrice *
                                        discountPercentage);

                                    priceHtml =
                                        `<s class="text-muted">${originalPrice.toLocaleString('vi-VN')} đ</s><br><span class="text-danger fw-bold">${discountedPrice.toLocaleString('vi-VN')} đ</span>`;
                                } else {
                                    priceHtml = `${p.price.toLocaleString('vi-VN')} đ`;
                                }
                            } else {
                                priceHtml = `${p.price.toLocaleString('vi-VN')} đ`;
                            }
                            $("#product-price").html(priceHtml);
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

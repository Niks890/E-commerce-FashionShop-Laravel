@can('salers')
@extends('admin.master')
@section('title', 'Thông tin Danh mục')

@section('content')
    @if (Session::has('success'))
        <div class="shadow-lg p-2 move-from-top js-div-dissappear" style="width: 26rem; display:flex; text-align:center">
            <i class="fas fa-check p-2 bg-success text-white rounded-circle pe-2 mx-2"></i>{{ Session::get('success') }}
        </div>
    @endif
    @if (Session::has('error'))
        <div class="shadow-lg p-2 move-from-top js-div-dissappear" style="width: 25rem; display:flex; text-align:center">
            <i class="fas fa-times p-2 bg-danger text-white rounded-circle pe-2 mx-2"></i>{{ Session::get('error') }}
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <div class="card-sub">
                <form method="GET" class="form-inline row" action="{{ route('category.search') }}">
                    {{-- @csrf --}}
                    <div class="col-9 navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button type="submit" class="btn btn-search pe-1">
                                    <i class="fa fa-search search-icon"></i>
                                </button>
                            </div>
                            <input name="query" type="text" placeholder="Nhập vào tên danh mục cần tìm..."
                                class="form-control" />
                        </div>
                    </div>
                    <div class="col-12 col-lg-3 mt-2 mt-lg-0">
                        <a href="{{ route('category.create') }}" type="submit" class="btn btn-success w-100">
                            <i class="fa fa-plus"></i> Thêm mới
                        </a>
                    </div>
                </form>
            </div>

            <!-- Add this wrapper div -->
            <div class="table-responsive mt-3">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Tên danh mục</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col">Số lượng</th>
                            <th scope="col">Ngày thêm</th>
                            <th scope="col" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $model)
                            <tr>
                                <td>{{ $model->id }}</td>
                                <td>{{ $model->category_name }}</td>
                                <td>{{ $model->status == 0 ? 'Ẩn' : 'Hiển thị' }}</td>
                                <td>{{ $model->Products->count() }}</td>
                                <td>{{ $model->created_at->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <form method="post" action="{{ route('category.destroy', $model->id) }}">
                                        @csrf @method('DELETE')
                                        <div class="d-flex flex-wrap justify-content-center gap-2">
                                            <a class="btn btn-sm btn-info btn-view-products" href="javascript:void(0);"
                                                data-id="{{ $model->id }}">
                                                <i class="fa fa-eye pe-2"></i>Xem
                                            </a>
                                            <a class="btn btn-sm btn-primary" href="{{ route('category.edit', $model->id) }}">
                                                <i class="fa fa-edit pe-2"></i>Sửa
                                            </a>
                                            <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Bạn có chắc muốn xóa không?')">
                                                <i class="fa fa-trash pe-2"></i>
                                                Xóa
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{ $data->links() }}



    <!-- Modal hiển thị sản phẩm -->
<div class="modal fade" id="productsModal" tabindex="-1" aria-labelledby="productsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productsModalLabel">Danh sách sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ảnh sản phẩm</th>
                                <th>Tên sản phẩm</th>
                                <th>Giá</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            <!-- Nội dung sản phẩm sẽ được load bằng AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
    <style>
        /* Add this CSS to make table responsive */
        @media (max-width: 767.98px) {
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table {
                white-space: nowrap;
            }

            /* Make buttons stack vertically on small screens */
            .btn {
                white-space: nowrap;
                margin-bottom: 5px;
            }

            /* Adjust search form for mobile */
            .nav-search {
                width: 100%;
            }

            .form-control {
                width: 100%;
            }
        }
    </style>
@endsection
@section('js')
    @if (Session::has('success') || Session::has('error'))
        <script src="{{ asset('assets/js/message.js') }}"></script>
    @endif

    <script>
        $(document).ready(function() {
            // Xử lý khi click vào nút xem sản phẩm
            $('.btn-view-products').click(function() {
                var categoryId = $(this).data('id');

                // Hiển thị modal
                var modal = new bootstrap.Modal(document.getElementById('productsModal'));
                modal.show();

                // Load dữ liệu sản phẩm bằng AJAX
                $.ajax({
                    url: '/admin/category/' + categoryId + '/products',
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            // Cập nhật tiêu đề modal
                            $('#productsModalLabel').text('Sản phẩm thuộc danh mục: ' + response.data.category_name);

                            // Xóa nội dung cũ
                            $('#productsTableBody').empty();

                            // Thêm sản phẩm vào bảng
                            if (response.data.products.length > 0) {
                                $.each(response.data.products, function(index, product) {
                                    var statusText = product.status == 1 ? 'Hiển thị' : 'Ẩn';
                                    $('#productsTableBody').append(
                                        '<tr>' +
                                        '<td>' + product.id + '</td>' +
                                        '<td><img src="' + product.image + '" alt="' + product.product_name + '" width="50"></td>' +
                                        '<td>' + product.product_name + '</td>' +
                                        '<td>' + formatPrice(product.price) + '</td>' +
                                        '<td>' + statusText + '</td>' +
                                        '</tr>'
                                    );
                                });
                            } else {
                                $('#productsTableBody').append(
                                    '<tr><td colspan="4" class="text-center">Không có sản phẩm nào trong danh mục này</td></tr>'
                                );
                            }
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Có lỗi xảy ra khi tải dữ liệu sản phẩm');
                    }
                });
            });

            // Hàm định dạng giá
            function formatPrice(price) {
                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
            }
        });
    </script>
@endsection
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

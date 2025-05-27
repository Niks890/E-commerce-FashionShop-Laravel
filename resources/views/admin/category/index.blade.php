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
                    <div
                        class="col-9 navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
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
                    <div class="col-3">
                        <a href="{{ route('category.create') }}" type="submit" class="btn btn-success"><i
                                class="fa fa-plus"></i>Thêm mới</a>
                    </div>
                </form>
            </div>
            <table class="table mt-3">
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
                                    {{-- Nút xem sản phẩm thuộc danh mục --}}
                                    <a class="btn btn-sm btn-info btn-view-products" href="#"
                                        data-id="{{ $model->id }}">
                                        <i class="fa fa-eye pe-2"></i>Xem
                                    </a>
                                    <a class="btn btn-sm btn-primary" href="{{ route('category.edit', $model->id) }}"><i
                                            class="fa fa-edit pe-2"></i>Sửa</a>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Bạn có chắc muốn xóa không?')">
                                        <i class="fa fa-trash pe-2"></i>
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{ $data->links() }}


    <!-- Modal hiển thị sản phẩm -->
    {{-- <div class="modal fade" id="productsModal" tabindex="-1" role="dialog" aria-labelledby="productsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productsModalLabel">Sản phẩm thuộc danh mục</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            <!-- Nội dung sản phẩm sẽ được load bằng AJAX -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div> --}}
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
@endsection

@section('js')
    @if (Session::has('success') || Session::has('error'))
        <script src="{{ asset('assets/js/message.js') }}"></script>
    @endif

    {{-- <script>
        $(document).ready(function() {
            $('.btn-view-products').click(function(e) {
                e.preventDefault();
                var categoryId = $(this).data('id');

                $('#productsModal').modal('show');

                // Sử dụng route API
                $.get('/api/categories/' + categoryId + '/products', function(response) {
                    var html = '';
                    var products = response.data.products;
                    console.log(products);

                    if (products.length > 0) {
                        $.each(products, function(key, product) {
                            html += '<tr>' +
                                '<td>' + product.id + '</td>' +
                                '<td>' + product.product_name + '</td>' +
                                '<td>' + product.price.toLocaleString() + ' VNĐ</td>' +
                                '<td>' + (product.status == 1 ? 'Hiển thị' : 'Ẩn') +
                                '</td>' +
                                '</tr>';
                        });
                    } else {
                        html =
                            '<tr><td colspan="5" class="text-center">Không có sản phẩm nào trong danh mục này</td></tr>';
                    }

                    $('#productsTableBody').html(html);
                    $('#productsModalLabel').text('Sản phẩm thuộc danh mục: ' + response.data
                        .category_name);
                }).fail(function() {
                    $('#productsTableBody').html(
                        '<tr><td colspan="5" class="text-center">Lỗi khi tải dữ liệu</td></tr>');
                });
            });
        });
    </script> --}}
@endsection
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

@can('salers')
    @extends('admin.master')
    @section('title', 'Thông tin Khuyến mãi')
@section('content')
    @if (Session::has('success'))
        <div class="shadow-lg p-2 move-from-top js-div-dissappear" style="width: 25rem; display:flex; text-align:center">
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
                <form method="GET" class="form-inline row" action="{{ route('discount.search') }}">
                    @csrf
                    <div
                        class="col-9 navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button type="submit" class="btn btn-search pe-1">
                                    <i class="fa fa-search search-icon"></i>
                                </button>
                            </div>
                            <input name="query" type="text" placeholder="Nhập vào tên chương trình khuyến mãi..."
                                class="form-control" />
                        </div>
                    </div>
                    <div class="col-3">
                        <button type="button" class="btn btn-success add-new-modal btn-create"><i
                                class="fa fa-plus"></i>Thêm
                            mới</button>
                    </div>
                </form>
            </div>
            <table class="table mt-3">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Tên chương trình</th>
                        <th scope="col">Phần trăm khuyến mãi</th>
                        <th scope="col">Ngày bắt đầu</th>
                        <th scope="col">Ngày kết thúc</th>
                        <th scope="col" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $model)
                        <tr>
                            <td>{{ $model->id }}</td>
                            <td>{{ $model->name }}</td>
                            <td>{{ round($model->percent_discount, 2) * 100 }}%</td>
                            <td>{{ $model->start_date->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $model->end_date->format('d/m/Y H:i:s') }}</td>
                            <td class="text-center">
                                <form method="post" action="{{ route('discount.destroy', $model->id) }}">
                                    @csrf @method('DELETE')
                                    <a class="btn btn-sm btn-secondary btn-detail" href=""><i
                                            class="fa fa-edit pe-2"></i>Chi
                                        tiết</a>
                                    <a class="btn btn-sm btn-primary btn-edit" href=""><i
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
    <div class="d-flex justify-content-center mt-3">
        {{ $data->links() }}
    </div>

    <!--Modal Thêm khuyến mãi-->
    <form id="modal-discount" class="modal modal-add js-modal" method="POST" action="{{ route('discount.store') }}">
        @csrf
        <input type="hidden" name="_method" value="POST">
        <div class="modal-container-add js-modal-container p-3">
            <div class="modal-close js-modal-close">
                <i class="fas fa-times"></i>
            </div>
            <div class="modal-header d-flex align-item-center justify-content-center fw-bold" style="font-size: 1.5rem">
                Thêm Chương trình Khuyến mãi
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name">Tên Chương trình Khuyến mãi:</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="">
                    @error('name')
                        <small class="text-danger error_validate">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="percent_discount">Phần trăm khuyến mãi:</label>
                    <input type="text" name="percent_discount" id="percent_discount" class="form-control" placeholder="">
                    @error('percent_discount')
                        <small class="text-danger error_validate">{{ $message }}</small>
                    @enderror
                </div>
                <div class="row p-3">
                    <div class="col-6 form-group">
                        <i class="far fa-calendar pe-2"></i><label for="start_date">Ngày bắt đầu:</label>
                        <input type="datetime-local" name="start_date" id="start_date" class="form-control" placeholder="">
                        @error('start_date')
                            <small class="text-danger error_validate">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-6 form-group">
                        <i class="far fa-calendar pe-2"></i><label for="end_date">Ngày kết thúc:</label>
                        <input type="datetime-local" name="end_date" id="end_date" class="form-control" placeholder="">
                        @error('end_date')
                            <small class="text-danger error_validate">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-primary me-3 my-2" name="" value="Lưu thông tin" />
            </div>
        </div>
    </form>
    <!--Modal Thêm khuyến mãi-->

    <!-- Modal Xem chi tiết -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="detailModalLabel"><i class="fas fa-gift mr-2"></i> Chi tiết
                        khuyến mãi</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <i class="fas fa-tags text-primary" style="font-size: 60px;"></i>
                        </div>
                        <div class="col-md-8">
                            <p><strong>ID:</strong> <span id="promo-id" class="text-muted"></span></p>
                            <p><strong>Tên chương trình:</strong> <span id="promo-name"
                                    class="fw-bold text-primary"></span></p>
                            <p><strong>Phần trăm khuyến mãi:</strong> <span id="promo-percent"
                                    class="badge bg-success"></span></p>
                            <p><strong>Ngày bắt đầu:</strong> <span id="promo-start" class="text-muted"></span></p>
                            <p><strong>Ngày kết thúc:</strong> <span id="promo-end" class="text-muted"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i
                            class="fas fa-times"></i> Đóng</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Xem chi tiết -->

@endsection


@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}" />
@endsection

@section('js')
    @if (Session::has('success') || Session::has('error'))
        <script src="{{ asset('assets/js/message.js') }}"></script>
    @endif
    <script src="{{ asset('assets/js/modal.js') }}"></script>
    <script>
        @if ($errors->any())
            $(document).ready(function() {
                $('#modal-discount').addClass("open");
                $('.btn-edit').click(function(e) {
                    $('.error_validate').text("");
                })
            })
        @endif
    </script>

    <script>
        $(document).ready(function() {
            $(".btn-detail").click(function(event) {
                event.preventDefault();
                let row = $(this).closest("tr");
                let promoId = row.find("td:first").text().trim();
                $.ajax({
                    url: `http://127.0.0.1:8000/api/discount/${promoId}`, //url, type, datatype, success,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.status_code === 200) {
                            let promo = response.data;
                            $("#promo-id").text(promo.id);
                            $("#promo-name").text(promo.name);
                            $("#promo-percent").text((parseFloat(promo.percent_discount) *
                                100) + "%");
                            $("#promo-start").text(new Date(promo.start_date).toLocaleString(
                                'vi-VN'));
                            $("#promo-end").text(new Date(promo.end_date).toLocaleString(
                                'vi-VN')); //text->h1,..h7, p, span,...
                            $("#detailModal").modal("show");
                            // console.log(response);
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
            $('.btn-edit').click(function(e) {
                e.preventDefault();
                let modalEdit = $('#modal-discount');
                modalEdit.addClass('open');
                $('.modal-header').text("Sửa thông tin khuyến mãi");
                let row = $(this).closest("tr");
                let promoId = row.find("td:first").text().trim();
                let actionUpdate = "{{ route('discount.update', ':id') }}".replace(':id', promoId);
                $("input[name='_method']").val("PUT");
                // console.log(actionUpdate);
                modalEdit.attr('action', actionUpdate);
                $.ajax({
                    url: `http://127.0.0.1:8000/api/discount/${promoId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status_code === 200) {
                            let promo = response.data;
                            $('#name').val(promo.name); //val->input, select,...
                            $('#percent_discount').val(promo.percent_discount);
                            $('#start_date').val(new Date(promo.start_date).toISOString().slice(
                                0, 16)); //yyyy-mm-dd hh-mm-ss
                            $('#end_date').val(new Date(promo.end_date).toISOString().slice(0,
                                16));
                        } else {
                            alert('Dữ liệu không tìm thấy!');
                        }
                    },
                    error: function() {
                        alert('Đã có lỗi xảy ra.');
                    }
                });
            })
        });
    </script>
@endsection
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

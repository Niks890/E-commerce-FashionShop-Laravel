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
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tên chương trình</th>
                        <th>% KM</th>
                        <th>Bắt đầu</th>
                        <th>Kết thúc</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $model)
                        <tr>
                            <td>{{ $model->id }}</td>
                            <td class="fw-semibold">{{ $model->name }}</td>
                            <td><span class="badge bg-success">{{ round($model->percent_discount, 2) * 100 }}%</span></td>
                            <td>{{ $model->start_date->format('d/m/Y H:i') }}</td>
                            <td>{{ $model->end_date->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-info btn-detail"><i class="fas fa-eye"></i></a>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary btn-edit"><i
                                            class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{ route('discount.destroy', $model->id) }}"
                                        onsubmit="return confirm('Xóa?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
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



    <!-- Modal Thêm/Sửa -->
    <div class="modal fade" id="discountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form id="discountForm" class="modal-content" method="POST" action="">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="discountModalLabel">Thêm Khuyến mãi</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row g-3 px-4">
                    <div class="col-12">
                        <label for="name" class="form-label">Tên chương trình</label>
                        <input type="text" name="name" id="name" class="form-control">
                    </div>
                    <div class="col-12">
                        <label for="percent_discount" class="form-label">Phần trăm khuyến mãi</label>
                        <input type="text" name="percent_discount" id="percent_discount" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Ngày bắt đầu</label>
                        <input type="datetime-local" name="start_date" id="start_date" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="end_date" class="form-label">Ngày kết thúc</label>
                        <input type="datetime-local" name="end_date" id="end_date" class="form-control">
                    </div>
                </div>
                <div class="modal-footer px-4 py-3">
                    <button type="submit" class="btn btn-success">Lưu</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                </div>
            </form>
        </div>
    </div>

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
            const discountModal = new bootstrap.Modal(document.getElementById('discountModal'));

            $('.btn-create').click(function() {
                $('#discountForm').attr('action', "{{ route('discount.store') }}");
                $('#discountForm input[name=_method]').val('POST');
                $('#discountModalLabel').text('Thêm Khuyến mãi');
                $('#discountForm')[0].reset();
                discountModal.show();
            });

            $('.btn-edit').click(function(e) {
                e.preventDefault();
                const row = $(this).closest("tr");
                const promoId = row.find("td:first").text().trim();
                const updateUrl = "{{ route('discount.update', ':id') }}".replace(':id', promoId);

                $('#discountForm').attr('action', updateUrl);
                $('#discountForm input[name=_method]').val('PUT');
                $('#discountModalLabel').text('Sửa Khuyến mãi');

                $.ajax({
                    url: `/api/discount/${promoId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        const promo = response.data;
                        $('#name').val(promo.name);
                        $('#percent_discount').val(promo.percent_discount * 100);
                        $('#start_date').val(new Date(promo.start_date).toISOString().slice(0,
                            16));
                        $('#end_date').val(new Date(promo.end_date).toISOString().slice(0, 16));
                        discountModal.show();
                    },
                    error: function() {
                        alert('Lỗi lấy dữ liệu khuyến mãi!');
                    }
                });
            });
        });
    </script>
@endsection
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

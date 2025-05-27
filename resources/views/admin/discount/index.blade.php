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
                    {{-- @csrf --}}
                    <div class="col-md-9 d-flex align-items-center"> {{-- Adjusted column for better alignment --}}
                        <div class="input-group flex-grow-1"> {{-- Allow input group to grow --}}
                            <div class="input-group-prepend">
                                <button type="submit" class="btn btn-search pe-1">
                                    <i class="fa fa-search search-icon"></i>
                                </button>
                            </div>
                            <input name="query" type="text" placeholder="Nhập vào tên chương trình khuyến mãi..."
                                class="form-control" value="{{ request('query') }}" /> {{-- Keep old query value --}}
                        </div>

                        <div class="ms-3"> {{-- Added margin-left for spacing --}}
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Còn hạn
                                </option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Đã hết hạn
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 text-end"> {{-- Align to end for "Add new" button --}}
                        <button type="button" class="btn btn-success add-new-modal btn-create"><i class="fa fa-plus"></i>
                            Thêm mới</button>
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
                        <th>Trạng thái</th>
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
                            <td>
                                {{-- Display dynamic status using the accessor --}}
                                @php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch ($model->calculated_status) {
                                        case 'active':
                                            $statusClass = 'bg-success';
                                            $statusText = 'Đang hiệu lực';
                                            break;
                                        case 'inactive':
                                            $statusClass = 'bg-danger';
                                            $statusText = 'Đã hết hạn';
                                            break;
                                        default:
                                            $statusClass = 'bg-dark'; // Fallback
                                            $statusText = 'Không xác định';
                                            break;
                                    }
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-info btn-detail"><i
                                            class="fas fa-eye"></i></a>
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
                    <div class="row mb-4">
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
                            <p><strong>Trạng thái:</strong> <span id="promo-status" class="fw-bold badge-info"></span></p>
                        </div>
                    </div>

                    <!-- Add this new section for products -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="fw-bold mb-3 border-bottom pb-2">Sản phẩm áp dụng</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover" id="products-table">
                                    <thead>
                                        <tr>
                                            <th>Hình ảnh</th>
                                            <th>Tên sản phẩm</th>
                                            <th>Giá gốc</th>
                                            <th>Giá sau KM</th>
                                        </tr>
                                    </thead>
                                    <tbody id="products-list">
                                        <!-- Products will be inserted here by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
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

@endsection


@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}" />

    <style>
        /* Add this to your modal.css or message.css */
        #products-table {
            font-size: 0.9rem;
        }

        #products-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        #products-table img {
            object-fit: cover;
        }

        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
    </style>

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
            // $(".btn-detail").click(function(event) {
            //     event.preventDefault();
            //     let row = $(this).closest("tr");
            //     let promoId = row.find("td:first").text().trim();
            //     $.ajax({
            //         url: `http://127.0.0.1:8000/api/discount/${promoId}`, //url, type, datatype, success,
            //         type: "GET",
            //         dataType: "json",
            //         success: function(response) {
            //             if (response.status_code === 200) {
            //                 let promo = response.data;
            //                 $("#promo-id").text(promo.id);
            //                 $("#promo-name").text(promo.name);
            //                 $("#promo-percent").text((parseFloat(promo.percent_discount) *
            //                     100) + "%");
            //                 $("#promo-start").text(new Date(promo.start_date).toLocaleString(
            //                     'vi-VN'));
            //                 $("#promo-end").text(new Date(promo.end_date).toLocaleString(
            //                     'vi-VN')); //text->h1,..h7, p, span,...
            //                 $("#promo-status").text(promo.status);
            //                 $("#detailModal").modal("show");
            //                 // console.log(response);
            //             } else {
            //                 alert("Không thể lấy dữ liệu chi tiết!");
            //             }
            //         },
            //         error: function() {
            //             alert("Đã có lỗi xảy ra, vui lòng thử lại!");
            //         }
            //     });
            // });

            $(".btn-detail").click(function(event) {
                event.preventDefault();
                let row = $(this).closest("tr");
                let promoId = row.find("td:first").text().trim();
                $.ajax({
                    url: `http://127.0.0.1:8000/api/discount/${promoId}`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.status_code === 200) {
                            let promo = response.data;
                            // Set basic discount info
                            $("#promo-id").text(promo.id);
                            $("#promo-name").text(promo.name);
                            $("#promo-percent").text((parseFloat(promo.percent_discount) *
                                100) + "%");
                            $("#promo-start").text(new Date(promo.start_date).toLocaleString(
                                'vi-VN'));
                            $("#promo-end").text(new Date(promo.end_date).toLocaleString(
                                'vi-VN'));
                            $("#promo-status").text(promo.status);

                            // Clear previous products
                            $("#products-list").empty();

                            // Add products to the table
                            if (promo.products && promo.products.length > 0) {
                                promo.products.forEach(function(product) {
                                    const discountedPrice = product.price * (1 - promo
                                        .percent_discount);
                                    const productRow = `
                                <tr>
                                    <td>
                                        ${product.image ?
                                            `<img src="${product.image}" alt="${product.product_name}" class="img-thumbnail" style="width: 50px; height: 50px;">` :
                                            '<i class="fas fa-box-open fa-2x text-muted"></i>'}
                                    </td>
                                    <td>${product.product_name}</td>
                                    <td>${product.price.toLocaleString('vi-VN')}đ</td>
                                    <td class="text-danger fw-bold">
                                        ${discountedPrice.toLocaleString('vi-VN')}đ
                                    </td>
                                </tr>
                            `;
                                    $("#products-list").append(productRow);
                                });
                            } else {
                                $("#products-list").append(`
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Không có sản phẩm nào trong chương trình khuyến mãi này
                                </td>
                            </tr>
                        `);
                            }

                            $("#detailModal").modal("show");
                        } else {
                            alert("Không thể lấy dữ liệu chi tiết!");
                        }
                    },
                    error: function() {
                        alert("Đã có lỗi xảy ra, vui lòng thử lại!");
                    }
                });
            });


            $('select[name="status"]').change(function() {
                $(this).closest('form').submit(); // Gửi form khi dropdown thay đổi
            });

            // Lắng nghe sự kiện "keyup" trên ô tìm kiếm sau một khoảng trễ
            let searchTimeout = null;
            $('input[name="query"]').keyup(function() {
                clearTimeout(searchTimeout); // Xóa timeout cũ nếu có
                const form = $(this).closest('form');
                searchTimeout = setTimeout(function() {
                    form.submit(); // Gửi form sau khi người dùng ngừng gõ một lúc
                }, 500); // 500ms (0.5 giây) trễ
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
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
@endsection
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

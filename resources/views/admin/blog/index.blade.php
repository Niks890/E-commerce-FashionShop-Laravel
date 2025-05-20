@can('managers')
    @extends('admin.master')
    @section('title', 'Thông tin bài viết')
    @section('content')

<div class="container py-4">

    @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="max-width: 400px; margin: auto;">
            <i class="fas fa-check-circle me-2"></i> {{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (Session::has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="max-width: 400px; margin: auto;">
            <i class="fas fa-exclamation-circle me-2"></i> {{ Session::get('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3 gap-3">
                <form method="GET" action="{{ route('blog.search') }}" class="d-flex flex-grow-1">
                    @csrf
                    <div class="input-group w-100">
                        <input name="query" type="search" class="form-control" placeholder="Nhập tags hoặc tiêu đề bài viết..." aria-label="Tìm kiếm bài viết" />
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>

                <button type="button" class="btn btn-success flex-shrink-0" data-bs-toggle="modal" data-bs-target="#addBlogModal">
                    <i class="fa fa-plus me-1"></i> Thêm mới
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tiêu đề</th>
                            <th>Nội dung</th>
                            <th>Ảnh</th>
                            <th>Tags</th>
                            <th>ID nhân viên</th>
                            <th>Status</th>
                            <th class="text-center" style="min-width: 150px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $model)
                            <tr>
                                <td>{{ $model->id }}</td>
                                <td class="text-truncate" style="max-width: 150px;" title="{{ $model->title }}">{{ $model->title }}</td>
                                <td class="text-truncate" style="max-width: 250px;" title="{{ strip_tags($model->content) }}">{{ Str::limit(strip_tags($model->content), 50, '...') }}</td>
                                <td>
                                    @if($model->image)
                                        <img src="{{ $model->image }}" alt="Ảnh bài viết" class="rounded" style="width: 60px; height: auto;">
                                    @else
                                        <span class="text-muted">Không có</span>
                                    @endif
                                </td>
                                <td class="text-truncate" style="max-width: 100px;">{{ $model->tags }}</td>
                                <td>{{ $model->staff_id }}</td>
                                <td>
                                    @if($model->status == 1)
                                        <span class="badge bg-success">Hiển thị</span>
                                    @else
                                        <span class="badge bg-secondary">Ẩn</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form method="POST" action="{{ route('blog.destroy', $model->id) }}" onsubmit="return confirm('Bạn có chắc muốn xóa không?');" class="d-flex justify-content-center gap-2 flex-wrap">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-info btn-detail" data-bs-toggle="modal" data-bs-target="#detailBlogModal" data-id="{{ $model->id }}" title="Chi tiết">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning btn-update" data-bs-toggle="modal" data-bs-target="#updateBlogModal" data-id="{{ $model->id }}" title="Sửa">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $data->links() }}
            </div>
        </div>
    </div>

</div>

    <!-- Modal blogAdd-->
    <div class="modal fade" id="addBlogModal" tabindex="-1" aria-labelledby="addBlogModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBlogModalLabel">Thêm Bài Viết Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('blog.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Nội dung</label>
                            <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-md-6">
                                <label for="image" class="form-label">Ảnh</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <img src="" alt="" width="100"
                                    class="img-preview preview-img-item d-none">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tags" class="form-label">Tags</label>
                            <input type="text" data-role="tagsinput" name="blog_tag" class="form-control"
                                value="" required>
                        </div>
                        <div class="mb-3">
                            <input type="hidden" class="form-control" value="{{ auth()->user()->id - 1 }}"
                                id="staff_id" name="staff_id">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-control" id="status" name="status">
                                <option value="1">Hiển thị</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <input type="submit" class="btn btn-primary" value="Lưu thông tin">
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal blogUpdate-->
    <div class="modal fade" id="updateBlogModal" tabindex="-1" aria-labelledby="updateBlogModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateBlogModalLabel">Cập Nhật Bài Viết</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateBlogForm" action="{{ route('blog.update', ':id') }}" method="POST"
                    enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề:</label>
                            <input type="text" class="form-control" id="blog-title-update" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Nội dung:</label>
                            <textarea class="form-control" id="blog-content-update" name="content" rows="10" required></textarea>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-md-6">
                                <label for="image" class="form-label">Ảnh:</label>
                                <input type="file" class="form-control" name="image_update" accept="image/*"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <img src="" id="blog-image-update" alt="" width="100"
                                    class="img-preview-update preview-img-item-update">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="blog_tag" class="form-label">Tags:</label>
                            <input type="text" id="blog-tag-update" name="blog_tag" class="form-control" data-role="tagsinput" required>
                        </div>
                        <div class="mb-3">
                            <input type="hidden" class="form-control" value="{{ auth()->user()->id - 1 }}"
                                id="staff-id-update" name="staff_id">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-control" id="blog-status-update" name="status" required>
                                <option value="1">Hiển thị</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <input type="submit" class="btn btn-primary" value="Lưu thông tin">
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal blogDetail --}}
    <div class="modal fade" id="detailBlogModal" tabindex="-1" aria-labelledby="detailBlogModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="detailBlogModalLabel">Chi Tiết Bài Viết<span id="staff-info"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">ID</label>
                        <input type="text" class="form-control" id="blog-id" name="id" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề</label>
                        <input type="text" class="form-control" id="blog-title" name="title" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Nội dung</label>
                        <textarea class="form-control" id="blog-content" name="content" rows="10" cols="10" readonly></textarea>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="image" class="form-label">Ảnh</label>
                            <img id="blog-image" width="100" name="image">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags</label>
                        <input type="text" id="blog-tag" name="blog_tag" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="staff_name" class="form-label">Nhân viên</label>
                        <input type="text" class="form-control" id="staff-name" name="staff_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <input type="text" class="form-control" id="blog-status" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Ngày thêm</label>
                        <input type="text" class="form-control" id="blog-createDate" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Ngày cập nhật</label>
                        <input type="text" class="form-control" id="blog-updateDate" readonly>
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
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-tagsinput.css') }}" />
    <style>
        /* Responsive fix cho table */
        @media (max-width: 576px) {
            .table-responsive {
                overflow-x: auto;
            }
            table td, table th {
                white-space: nowrap;
            }
        }
    </style>
@endsection



@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-tagsinput.css') }}" />
@endsection

@section('js')
    @if (Session::has('success') || Session::has('error'))
        <script src="{{ asset('assets/js/message.js') }}"></script>
    @endif

    <script src="{{ asset('assets/js/plugin/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>

    <script>
        document.querySelector('input[name="image"]').addEventListener('change', function(e) {
            const [file] = e.target.files
            if (file) {
                document.querySelector('.preview-img-item').classList.remove('d-none')
                document.querySelector('.img-preview').src = URL.createObjectURL(file)
            }
        })


        document.querySelector('input[name="image-update"]').addEventListener('change', function(e) {
            const [file] = e.target.files
            if (file) {
                document.querySelector('.img-preview-update').src = URL.createObjectURL(file)
            }
        })
    </script>



    <script>
        $(document).ready(function() {
            $(".btn-detail").click(function(event) {
                event.preventDefault();

                let blogId = $(this).data("id");
                $.ajax({
                    url: `http://127.0.0.1:8000/api/blog_detail/${blogId}`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.status_code === 200) {
                            console.log(response);
                            let blogInfo = response.data;
                            console.log(blogInfo);
                            $("#blog-id").val(blogInfo.id);
                            $("#blog-title").val(blogInfo.title);
                            $("#blog-content").val(blogInfo.content);
                            $("#blog-image").attr("src", `${blogInfo.image}`);
                            $("#blog-tag").val(blogInfo.tags);
                            $("#staff-name").val(blogInfo.staff.name);
                            $("#blog-status").val(blogInfo.status === 1 ? "Hiển thị" : "Ẩn");
                            $("#blog-createDate").val(new Date(blogInfo.created_at)
                                .toLocaleString('vi-VN'));
                            $("#blog-updateDate").val(new Date(blogInfo.updated_at)
                                .toLocaleString('vi-VN'));
                        } else {
                            alert("Không thể lấy dữ liệu chi tiết!");
                        }
                    },
                    error: function() {
                        alert("Đã có lỗi xảy ra, vui lòng thử lại!");
                    }
                });
            });
            $(".btn-update").click(function(event) {
                // event.preventDefault();
                let blogId = $(this).data("id");
                let formAction = $("#updateBlogForm").attr("action").replace(':id', blogId);
                $("#updateBlogForm").attr("action", formAction);
                $.ajax({
                    url: `http://127.0.0.1:8000/api/blog_detail/${blogId}`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.status_code === 200) {
                            let blogInfo = response.data;
                            $("#blog-title-update").val(blogInfo.title);
                            $("#blog-content-update").val(blogInfo.content);
                            $(".preview-img-item-update").attr("src", `${blogInfo.image}`);
                            $("#blog-tag-update").tagsinput('removeAll');
                            $("#blog-tag-update").tagsinput('add', blogInfo.tags);
                            $("#staff-name-update").val(blogInfo.staff.name);
                            $("#blog-status-update").val(blogInfo.status);
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





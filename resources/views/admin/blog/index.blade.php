@can('managers')
    @extends('admin.master')
    @section('title', 'Thông tin bài viết')
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
                <form method="GET" class="form-inline row" action="{{ route('blog.search') }}">
                    @csrf
                    <div
                        class="col-9 navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button type="submit" class="btn btn-search pe-1">
                                    <i class="fa fa-search search-icon"></i>
                                </button>
                            </div>
                            <input name="query" type="text" placeholder="Nhập vào tags hoặc tên bài viết..."
                                class="form-control" />
                        </div>
                    </div>
                    <div class="col-3">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#addBlogModal">
                            <i class="fa fa-plus"></i> Thêm mới
                        </button>
                    </div>
                </form>
            </div>
            <table class="table mt-3">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Tiêu đề</th>
                        <th scope="col">Nội dung</th>
                        <th scope="col">Ảnh</th>
                        <th scope="col">Tags</th>
                        <th scope="col">ID nhân viên</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $model)
                        <tr>
                            <td>{{ $model->id }}</td>
                            <td>{{ $model->title }}</td>
                            <td>{{ Str::limit($model->content, 50, '...') }}</td>
                            <td><img src="uploads/{{ $model->image }}" width="50" alt=""></td>
                            <td>{{ $model->tags }}</td>
                            <td>{{ $model->staff_id }}</td>
                            <td>{{ $model->status }}</td>
                            <td class="text-center">
                                <form method="post" action="{{ route('blog.destroy', $model->id) }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-secondary btn-detail"
                                        data-bs-toggle="modal" data-bs-target="#detailBlogModal"
                                        data-id="{{ $model->id }}">
                                        <i class="fa fa-eye"></i> Chi tiết
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary btn-update" data-bs-toggle="modal"
                                        data-bs-target="#updateBlogModal" data-id="{{ $model->id }}">
                                        <i class="fa fa-plus"></i> Sửa
                                    </button>
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
                            $("#blog-image").attr("src", `uploads/${blogInfo.image}`);
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
                            $(".preview-img-item-update").attr("src", `uploads/${blogInfo.image}`);
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

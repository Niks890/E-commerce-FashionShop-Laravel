@can('managers')
    @extends('admin.master')
    @section('title', 'Thông tin Nhân viên')
    @section('content')
        @if (Session::has('success'))
            <div class="shadow-lg p-2 move-from-top js-div-dissappear" style="width: 25rem; display:flex; text-align:center">
                <i class="fas fa-check p-2 bg-success text-white rounded-circle pe-2 mx-2"></i>{{ Session::get('success') }}
            </div>
        @endif
        <div class="card">
            <div class="card-body">
                <div class="card-sub">
                    <form method="GET" class="form-inline row" action="{{ route('staff.search') }}">
                        @csrf
                        <div
                            class="col-9 navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button type="submit" class="btn btn-search pe-1">
                                        <i class="fa fa-search search-icon"></i>
                                    </button>
                                </div>
                                <input name="query" type="text" placeholder="Nhập vào tên nhân viên..."
                                    class="form-control" />
                            </div>
                        </div>
                        <div class="col-3">
                            <a href="{{ route('staff.create') }}" type="submit" class="btn btn-success"><i
                                    class="fa fa-plus"></i>Thêm mới</a>
                        </div>
                    </form>
                </div>
                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Họ tên nhân viên</th>
                            <th scope="col">Số điện thoại</th>
                            <th scope="col">Địa chỉ</th>
                            <th scope="col">Email</th>
                            <th scope="col">Giới tính</th>
                            {{-- <th scope="col">Tài khoản</th>
                                <th scope="col">Mật khẩu</th> --}}
                            <th scope="col">Chức vụ</th>
                            {{-- <th scope="col">Role</th> --}}
                            <th scope="col">Trạng thái</th>
                            {{-- <th scope="col">Ngày Thêm</th> --}}
                            <th scope="col" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $model)
                            <tr>
                                <td>{{ $model->id }}</td>
                                <td>{{ $model->name }}</td>
                                <td>{{ $model->phone }}</td>
                                <td>{{ $model->address }}</td>
                                <td>{{ $model->email }}</td>
                                <td>{{ $model->sex == 1 ? 'Nam' : 'Nữ' }}</td>
                                {{-- <td>{{ $model->username }}</td>
                                    <td>{{ $model->password }}</td> --}}
                                <td>{{ $model->position }}</td>
                                {{-- <td>{{ $model->role }}</td> --}}
                                <td>{{ $model->status }}</td>
                                {{-- <td>{{ $model->created_at->format('d/m/Y') }}</td> --}}
                                <td class="text-center">
                                    <form method="post" action="{{ route('staff.destroy', $model->id) }}">
                                        @csrf @method('DELETE')
                                        <a class="btn btn-sm btn-secondary btn-detail" href=""><i
                                                class="fa fa-edit pe-2"></i>Chi
                                            tiết</a>
                                        <a class="btn btn-sm btn-primary" href="{{ route('staff.edit', $model->id) }}"><i
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



        <!-- Modal staffDetail -->
        <div class="modal fade" id="staffDetail" tabindex="-1" aria-labelledby="staffDetailLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-secondary text-white">
                        <h5 class="modal-title" id="staffDetailLabel">Thông tin cá nhân của nhân viên: <span
                                id="staff-info"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <tbody>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Mã nhân viên:</td>
                                        <td style="width: 70%;"><span id="staff-id"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Tên nhân viên</td>
                                        <td style="width: 70%;"><span id="staff-name"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Giới Tính:</td>
                                        <td style="width: 70%;"><span id="staff-sex"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Địa chỉ:</td>
                                        <td style="width: 70%;"><span id="staff-address"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Số điện thoại:</td>
                                        <td style="width: 70%;"><span id="staff-phone"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Email:</td>
                                        <td style="width: 70%;"><span id="staff-email"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Chức vụ hiện tại:</td>
                                        <td style="width: 70%;"><span id="staff-position"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Trạng thái công việc:</td>
                                        <td style="width: 70%;"><span id="staff-status"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Ngày thêm:</td>
                                        <td style="width: 70%;"><span id="staff-createDate"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start" style="width: 30%;">Ngày cập nhật:</td>
                                        <td style="width: 70%;"><span id="staff-updateDate"></span></td>
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

    {{-- cách 1 hoạt động bt --}}
    {{-- <script>
        @if ($errors->any())
            $(document).ready(function() {
                $('#staffDetail').addClass("open");
            })
        @endif
    </script>
    <script>
        $(document).ready(function() {
            $(".btn-detail").click(function(event) {
                event.preventDefault();
                let row = $(this).closest("tr");
                let staffId = row.find("td:first").text().trim();
                $.ajax({
                    url: `http://127.0.0.1:8000/api/staff/${staffId}`, //url, type, datatype, success,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.status_code === 200) {
                            let staffInfo = response.data;
                            $("#staff-info").text(staffInfo.name);
                            $("#staff-id").text(staffInfo.id);
                            $("#staff-name").text(staffInfo.name);
                            $("#staff-sex").text(staffInfo.sex === 1 ? 'Nam' : 'Nữ');
                            $("#staff-address").text(staffInfo.address);
                            $("#staff-phone").text(staffInfo.phone);
                            $("#staff-email").text(staffInfo.email);
                            $("#staff-position").text(staffInfo.position);
                            $("#staff-status").text(staffInfo.status);
                            $("#staff-createDate").text(new Date(staffInfo.created_at).toLocaleString(
                                'vi-VN'));
                            $("#staff-updateDate").text(new Date(staffInfo.updated_at).toLocaleString('vi-VN'));
                            $("#staffDetail").modal("show");
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
    </script> --}}

    {{-- cách 2 Test thử  thấy cũng oke--}}
    <script>
        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('staffDetail').classList.add("open");
            });
        @endif
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let btnDetails = document.querySelectorAll(".btn-detail");
            btnDetails.forEach(button => {
                button.addEventListener("click", async (event) => {
                    event.preventDefault();
                    let row = button.closest("tr");
                    let staffId = row.querySelector("td:first-child").textContent.trim();
                    try {
                        let response = await fetch(`http://127.0.0.1:8000/api/staff/${staffId}`);
                        let result = await response.json();

                        if (result.status_code === 200) {
                            let staff = result.data;
                            document.getElementById("staff-id").textContent = staff.id;
                            document.getElementById("staff-name").textContent = staff.name;
                            document.getElementById("staff-sex").textContent = staff.sex === 1 ?"Nam" : "Nữ";
                            document.getElementById("staff-address").textContent = staff.address;
                            document.getElementById("staff-phone").textContent = staff.phone;
                            document.getElementById("staff-email").textContent = staff.email;
                            document.getElementById("staff-position").textContent = staff.position;
                            document.getElementById("staff-status").textContent = staff.status;
                            document.getElementById("staff-createDate").textContent = new Date(staff.created_at).toLocaleString('vi-VN');
                            document.getElementById("staff-updateDate").textContent = new Date(staff.updated_at).toLocaleString('vi-VN');
                            let modal = new bootstrap.Modal(document.getElementById("staffDetail"));
                            modal.show();
                        } else {
                            alert("Không thể lấy dữ liệu chi tiết!");
                        }
                    } catch (error) {
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

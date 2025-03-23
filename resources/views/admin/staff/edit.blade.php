@can('managers')
    @extends('admin.master')
    @section('title', 'Sửa thông tin')
    @section('back-page')
        <a class="text-primary" onclick="window.history.back()">
            <i class="fas fa-chevron-left ms-3"></i>
            <p class="d-inline text-decoration-underline" style="cursor: pointer">Quay lại</p>
        </a>
    @endsection
    @section('content')
        <form method="POST" action="{{ route('staff.update', $staff->id) }}">
            @csrf @method('PUT')
            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
            <div class="form-group">
                <label for="">Họ tên nhân viên:</label>
                <input type="text" name="name" id="" class="form-control" placeholder="" value="{{ $staff->name }}">
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label for="">Số điện thoại:</label>
                <input type="text" name="phone" id="" class="form-control" placeholder="" value="{{ $staff->phone }}">
                @error('phone')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label for="">Địa chỉ:</label>
                <input type="text" name="address" id="" class="form-control" placeholder="" value="{{ $staff->address }}">
                @error('address')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label for="">Email</label>
                <input type="email" name="email" id="" class="form-control" placeholder="" value="{{ $staff->email }}">
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <div class="row m-2">
                    <h7 class="col-2 mt-2">Giới tính:</h7>
                    <div class="form-check col-2">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="sex" value="1" @checked( $staff->sex == 1 )>
                            Nam
                        </label>
                    </div>
                    <div class="form-check col-2">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="sex" value="0" @checked( $staff->sex == 0 )>
                            Nữ
                        </label>
                    </div>
                </div>
                @error('sex')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label for="">Chức vụ:</label>
                <select class="form-control" name="position">
                    <option value="Quản lý" @selected($staff->position === "Quản lý")>Quản lý</option>
                    <option value="Nhân viên bán hàng" @selected($staff->position === "Nhân viên bán hàng")>Nhân viên bán hàng</option>
                    <option value="Nhân viên kho" @selected($staff->position === "Nhân viên kho")>Nhân viên kho</option>
                </select>
                @error('position')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label for="">Trạng thái làm việc:</label>
                <input type="text" name="status" id="" class="form-control" placeholder="" value="{{ $staff->status }}">
                @error('status')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <input class="btn btn-primary" name="" type="submit" value="Lưu thông tin">
        </form>
    @endsection
@else
    {{ abort(403, 'Bạn không có quyền truy cập trang này') }}
@endcan

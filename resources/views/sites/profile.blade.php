@extends('sites.master')
@section('title', 'Hồ sơ cá nhân')
@section('content')
    @if (Session::has('updateprofile'))
        <div class="shadow-lg p-2 move-from-top js-div-dissappear" style="width: 21rem; margin-top: 120px; display:flex; text-align:center;">
            <i
                class="fas fa-check p-2 bg-success text-white rounded-circle pe-2 mx-2"></i>{{ Session::get('updateprofile') }}
        </div>
    @endif
    <div class="container rounded bg-white">
        <div class="row">
            <div class="col-md-3 border-right">
                <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                    @php
                    $avatarUrl = asset('client/img/avatar-user.png'); // Ảnh mặc định

                    if (Auth::guard('customer')->check() && Auth::guard('customer')->user() !== null) {
                        $user = Auth::guard('customer')->user();
                        $avatar = $user->image;

                        if (!empty($avatar)) {
                            if (filter_var($avatar, FILTER_VALIDATE_URL)) {
                                // Nếu ảnh là URL (Google/Facebook)
                                $avatarUrl = $avatar;
                            } elseif (file_exists(public_path('client/img/' . $avatar))) {
                                // Nếu ảnh được lưu trong thư mục client/img và tồn tại
                                $avatarUrl = asset('client/img/' . $avatar);
                            }
                        }
                    }
                    @endphp

                <img class="rounded-circle mt-5" width="150px" src="{{ $avatarUrl }}">

                    <span class="font-weight-bold"></span>
                    <span class="text-black-50"></span>
                </div>
            </div>
            <div class="col-md-9 border-right">
                <form action="{{ route('user.update_profile', Auth::guard('customer')->user()->id) }}" method="post">
                    @csrf @method('PUT')
                    <div class="p-3 py-5">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <h4 class="text-center">Hồ sơ cá nhân</h4>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label class="labels">Họ và tên</label>
                                <input type="text" class="form-control" name="name" value="{{ $customer->name }}">
                                @error('name')
                                    <small class="text-danger validate-error">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="labels">Email</label>
                                <input type="text" class="form-control" name="email" value="{{ $customer->email }}">
                                @error('email')
                                    <small class="text-danger validate-error">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="labels">Số điện thoại</label>
                                <input type="text" class="form-control" name="phone" value="{{ $customer->phone }}">
                                @error('phone')
                                    <small class="text-danger validate-error">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="labels">Địa chỉ</label>
                                <input type="text" class="form-control" name="address" value="{{ $customer->address }}">
                                @error('address')
                                    <small class="text-danger validate-error">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row mt-3 password-edit">
                            <div class="col-md-6">
                                <label class="labels">Mật khẩu cũ</label>
                                <div class="d-flex">
                                    <input type="text" class="form-control old-password" value="************" readonly>
                                    <i class="fas fa-edit p-2" id="btn-edit-profile" style="cursor: pointer;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <input class="btn btn-primary profile-button" type="submit" value="Lưu thông tin">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/message.css') }}" />
@endsection

@section('js')
    @if (Session::has('updateprofile'))
        <script src="{{ asset('assets/js/message.js') }}"></script>
    @endif

    <script>
        document.getElementById("btn-edit-profile").addEventListener("click", function(e) {
            if (confirm("Bạn muốn sửa mật khẩu?")) {
                let container = document.querySelector('.password-edit');
                let inputNewPassword = document.createElement('div');
                inputNewPassword.classList.add('col-md-6');
                inputNewPassword.innerHTML = `
                                    <label class="labels">Mật khẩu mới</label>
                                    <input type="text" class="form-control" name="new_password" value="">`;
                container.appendChild(inputNewPassword);
            }
        });
    </script>
@endsection

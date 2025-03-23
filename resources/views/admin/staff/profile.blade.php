@extends('admin.master')
@section('title', 'Hồ sơ cá nhân')
@section('content')
    @if (Session::has('success'))
        <div class="shadow-lg p-2 move-from-top js-div-dissappear" style="width: 25rem; display:flex; text-align:center">
            <i class="fas fa-check p-2 bg-success text-white rounded-circle pe-2 mx-2"></i>{{ Session::get('success') }}
        </div>
    @endif
    <div class="container rounded bg-white">
        <div class="row">
            <div class="col-md-3 border-right">
                <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                    <img class="rounded-circle mt-5" width="150px"
                        src="https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg">
                    <span class="font-weight-bold">{{ $staff->name }}</span>
                    <span class="text-black-50">{{ $staff->email }}</span>
                </div>
            </div>
            <div class="col-md-5 border-right">
                <form action="{{ route('staff.update_staff', $staff->id) }}" method="post">
                    @csrf @method('PUT')
                    <div class="p-3 py-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-right">Profile Settings</h4>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label class="labels">Name</label>
                                <input type="text" class="form-control" name="name"
                                    value="{{ old('name', $staff->name) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="labels">Email</label>
                                <input type="text" class="form-control" name="email"
                                    value="{{ old('email', $staff->email) }}">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="labels">Mobile Number</label>
                                <input type="text" class="form-control" name="phone"
                                    value="{{ old('phone', $staff->phone) }}">
                            </div>
                            <div class="col-md-12">
                                <label class="labels">Address</label>
                                <input type="text" class="form-control" name="address"
                                    value="{{ old('address', $staff->address) }}">
                            </div>
                            <div class="col-md-12">
                                <label class="labels">Sex</label>
                                <select name="sex" class="form-control">
                                    <option value="1" {{ $staff->sex == 1 ? 'selected' : '' }}>Nam</option>
                                    <option value="0" {{ $staff->sex == 0 ? 'selected' : '' }}>Nữ</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="labels">Position</label>
                                <input type="text" class="form-control" name="position"
                                    value="{{ old('position', $staff->position) }}" readonly>
                            </div>
                            <div class="col-md-12">
                                <label class="labels">Status</label>
                                <input type="text" class="form-control" name="status"
                                    value="{{ old('status', $staff->status) }}">
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <input class="btn btn-primary profile-button" type="submit" value="Save Profile">
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
    @if (Session::has('success'))
        <script src="{{ asset('assets/js/message.js') }}"></script>
    @endif
@endsection

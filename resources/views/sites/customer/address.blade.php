@extends('sites.master', ['hideChatbox' => true])
@section('title', 'Quản lý địa chỉ giao hàng')
@section('content')
<div class="container mt-4">
    <h3 class="mb-3">Danh sách địa chỉ giao hàng</h3>
    <div class="mb-3">
        <button class="btn btn-success" id="show-add-form">Thêm địa chỉ mới</button>
    </div>
    {{-- Danh sách địa chỉ --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Địa chỉ chi tiết</th>
                <th>Mặc định</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            {{-- xử lý trường hợp mảng rổng bằng forelse --}}
            @forelse ($addresses as $address)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $address->full_address }}</td>
                <td>
                    @if ($address->is_default)
                        <span class="badge badge-success">Mặc định</span>
                    @else
                        {{-- <form method="POST" action="{{ route('addresses.setDefault', $address->id) }}"> --}}
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary">Đặt làm mặc định</button>
                        </form>
                    @endif
                </td>
                <td>
                    <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $address->id }}">Sửa</button>
                    <form method="POST" action="{{ route('addresses.destroy', $address->id) }}" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xóa địa chỉ này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">Chưa có địa chỉ giao hàng nào!</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Form thêm/sửa địa chỉ --}}
    <div id="address-form-area" style="display:none;">
        <h4 id="form-title">Thêm địa chỉ mới</h4>
        {{-- <form id="address-form" method="POST" action="{{ route('addresses.store') }}"> --}}
            @csrf
            <input type="hidden" name="address_id" id="address_id">
            <div class="form-group">
                <label for="province">Tỉnh/Thành phố</label>
                <input type="text" class="form-control" name="province_name" id="province_name" required>
            </div>
            <div class="form-group">
                <label for="ward">Phường/Xã</label>
                <input type="text" class="form-control" name="ward_name" id="ward_name" required>
            </div>
            <div class="form-group">
                <label for="street_address">Số nhà, tên đường</label>
                <input type="text" class="form-control" name="street_address" id="street_address" required>
            </div>
            <div class="form-group">
                <label for="full_address">Địa chỉ đầy đủ</label>
                <input type="text" class="form-control" name="full_address" id="full_address" required>
            </div>
            <div class="mt-2">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <button type="button" class="btn btn-secondary" id="cancel-btn">Hủy</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
@endsection

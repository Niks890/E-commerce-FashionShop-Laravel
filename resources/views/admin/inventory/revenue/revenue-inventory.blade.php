@extends('admin.master')

@section('title', 'Quản lý tồn kho hiện tại')
@section('content')
<div class="container mt-4">
    <h3 class="mb-3">📦 Danh sách tồn kho hiện tại</h3>

<form method="GET" action="{{ route('admin.revenueInventory') }}" class="row g-3 mb-4">
    <div class="col-md-3">
        <input type="text" name="product_name" class="form-control" placeholder="Tên sản phẩm" value="{{ request('product_name') }}">
    </div>
    <div class="col-md-2">
        <input type="text" name="color" class="form-control" placeholder="Màu sắc" value="{{ request('color') }}">
    </div>
    <div class="col-md-2">
        <input type="text" name="size" class="form-control" placeholder="Kích cỡ" value="{{ request('size') }}">
    </div>
    <div class="col-md-2">
 <select name="stock_status" class="form-control">
    <option value="">-- Tất cả tồn kho --</option>
    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Gần hết hàng (&lt; 5)</option>
    <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Hết hàng (&lt;= 0)</option>
    <option value="available" {{ request('stock_status') == 'available' ? 'selected' : '' }}>Còn hàng (&gt; 0)</option>
</select>

    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">Lọc</button>
    </div>
</form>


    {{-- Bảng dữ liệu --}}
    <div class="table-responsive">
<table class="table table-bordered table-hover table-striped align-middle">
    <thead class="thead-dark">
        <tr>
            <th>#</th>
            <th>Tên sản phẩm</th>
            <th>Mã SP</th>
            <th>Màu</th>
            <th>Size</th>
            <th>Số lượng tồn</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($inventoryRevenueCurrent as $item)
        <tr>
            <td>{{ ($inventoryRevenueCurrent->currentPage() - 1) * $inventoryRevenueCurrent->perPage() + $loop->iteration }}</td>
            <td>{{ $item->product_name }}</td>
            <td>{{ $item->product_id }}</td>
            <td>{{ $item->color }}</td>
            <td>{{ $item->size }}</td>
            <td>
                {{ $item->stock }}
                @if ($item->stock < 5)
                    <span class="text-danger" title="Tồn kho thấp">
                        <i class="fas fa-exclamation-triangle ms-1"></i>
                    </span>
                @endif
            </td>
            <td>
                <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-success">
                   Đến trang nhập hàng
                </a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center text-muted">Không có dữ liệu tồn kho phù hợp</td>
        </tr>
        @endforelse
    </tbody>
</table>

    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center">
        {{ $inventoryRevenueCurrent->links() }}
    </div>
</div>
@endsection

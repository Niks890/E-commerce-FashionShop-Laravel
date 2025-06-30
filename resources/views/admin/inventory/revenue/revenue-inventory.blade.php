@extends('admin.master')

@section('title', 'Quản lý tồn kho hiện tại')
@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-boxes"></i> Danh sách tồn kho hiện tại</h3>
            <form method="GET" action="{{ route('admin.revenueInventory') }}" style="display: inline;">
                @foreach(request()->except('export_pdf') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button type="submit" name="export_pdf" value="1" class="btn btn-light">
                    <i class="fas fa-file-pdf"></i> Xuất PDF
                </button>
            </form>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('admin.revenueInventory') }}" class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="product_name" class="form-control" placeholder="Tên sản phẩm" value="{{ request('product_name') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-palette"></i></span>
                        <input type="text" name="color" class="form-control" placeholder="Màu sắc" value="{{ request('color') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-ruler-combined"></i></span>
                        <input type="text" name="size" class="form-control" placeholder="Kích cỡ" value="{{ request('size') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-warehouse"></i></span>
                        <select name="stock_status" class="form-control">
                            <option value="">-- Tất cả --</option>
                            <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Gần hết hàng (<5)</option>
                            <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Hết hàng (≤0)</option>
                            <option value="available" {{ request('stock_status') == 'available' ? 'selected' : '' }}>Còn hàng (>0)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Lọc
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.revenueInventory') }}" class="btn btn-outline-secondary w-100" title="Reset bộ lọc">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>

            {{-- Bảng dữ liệu --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped align-middle" id="inventoryTable">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">Mã SP</th>
                            <th width="30%">Tên sản phẩm</th>
                            <th width="10%">Màu</th>
                            <th width="10%">Size</th>
                            <th width="12%">Tổng Số lượng tồn</th>
                            <th>Available Stock</th>
                            <th width="15%">Trạng thái</th>
                            <th width="15%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($inventoryRevenueCurrent as $item)
                        <tr>
                            <td>{{ $item->product_id }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->color }}</td>
                            <td>{{ $item->size }}</td>
                            <td class="text-center fw-bold {{ $item->stock <= 0 ? 'text-danger' : ($item->stock < 5 ? 'text-warning' : 'text-success') }}">
                                {{ $item->stock }}
                            </td>
                            <td class="text-center fw-bold {{ $item->available_stock <= 0 ? 'text-danger' : ($item->available_stock < 5 ? 'text-warning' : 'text-success') }}">
                                {{ $item->available_stock }}
                            </td>
                            <td>
                                @if($item->stock <= 0)
                                    <span class="badge bg-danger">Hết hàng</span>
                                @elseif($item->stock < 5)
                                    <span class="badge bg-warning text-dark">Gần hết</span>
                                @else
                                    <span class="badge bg-success">Còn hàng</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Nhập hàng
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">Không có dữ liệu tồn kho phù hợp</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Phân trang --}}
            @if($inventoryRevenueCurrent->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Hiển thị {{ $inventoryRevenueCurrent->firstItem() }} đến {{ $inventoryRevenueCurrent->lastItem() }} trong tổng số {{ $inventoryRevenueCurrent->total() }} mục
                </div>
                <div>
                    {{ $inventoryRevenueCurrent->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

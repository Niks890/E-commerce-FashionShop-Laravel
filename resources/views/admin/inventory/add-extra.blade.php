@can('warehouse workers')
    @extends('admin.master')
    @section('title', 'Tạo phiếu nhập thêm sản phẩm đã có trong kho')
@section('back-page')
    <div class="d-flex align-items-center mb-3">
        <button class="btn btn-outline-primary rounded-pill px-3 py-2 shadow-sm" onclick="window.history.back()"
            style="transition: all 0.3s ease; border: 2px solid #007bff;">
            <i class="fas fa-arrow-left me-2"></i>
            <span class="fw-semibold">Quay lại</span>
        </button>
    </div>
@endsection
@section('content')
    <form id="formCreateInventory" method="POST" action="{{ route('inventory.post_add_extra') }}"
        enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ auth()->user()->id - 1 }}">

        <div class="card shadow-lg mb-4 rounded-3">
            <div class="card-header bg-gradient-primary text-white py-3 rounded-top-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-truck me-2"></i>Thông tin nhà cung cấp và ghi chú</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="provider_name_display" class="form-label fw-bold">Nhà cung cấp:</label>
                    <input type="text" id="provider_name_display" class="form-control form-control-lg" readonly
                        placeholder="Tên nhà cung cấp">
                    <input type="hidden" name="provider_id" id="provider_id_hidden">
                </div>
                <div class="mb-3">
                    <label for="note" class="form-label fw-bold">Ghi chú:</label>
                    <textarea id="note" name="note" class="form-control" rows="3" placeholder="Nhập ghi chú cho phiếu nhập"
                        required></textarea>
                </div>
            </div>
        </div>

        {{-- <div class="card shadow-lg mb-4 rounded-3">
            <div class="card-header bg-gradient-primary text-white py-3 rounded-top-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-search me-2"></i>Tìm kiếm sản phẩm</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="product_search" class="form-label fw-bold">Chọn sản phẩm:</label>
                    <select id="product_search" class="form-select select2-product">
                        <option value="">-- Chọn sản phẩm --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div> --}}

        <!-- Trong file blade, phần tìm kiếm sản phẩm -->
        <div class="card shadow-lg mb-4 rounded-3">
            <div class="card-header bg-gradient-primary text-white py-3 rounded-top-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-search me-2"></i>Tìm kiếm sản phẩm</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="product_search" class="form-label fw-bold">Chọn sản phẩm:</label>
                    <select id="product_search" class="form-select select2-product">
                        <option value="">-- Chọn sản phẩm --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Thêm nút load danh sách -->
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" id="loadAllProductsBtn">
                        <i class="fas fa-list me-2"></i>Load danh sách sản phẩm
                    </button>
                </div>
            </div>
        </div>

        <div id="products_container" class="card shadow-lg mb-4 rounded-3">
            <div class="card-header bg-gradient-primary text-white py-3 rounded-top-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-boxes me-2"></i>Thông tin sản phẩm trong phiếu nhập</h5>
            </div>
            <div class="card-body">
                <div class="btn-group-bulk mb-4">
                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" id="selectAllProductsBtn">
                        <i class="fas fa-check-double me-2"></i>Chọn tất cả
                    </button>
                    <button type="button" class="btn btn-secondary rounded-pill px-4 shadow-sm"
                        id="deselectAllProductsBtn">
                        <i class="fas fa-times me-2"></i>Bỏ chọn tất cả
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" width="50px" class="text-center">Chọn</th>
                                <th scope="col" width="150px">Sản phẩm</th>
                                <th scope="col" width="100px">Hình ảnh</th>
                                <th scope="col" width="120px">Thông tin cơ bản</th>
                                <th scope="col" class="text-center">Biến thể hiện có</th>
                                <th scope="col" width="250px">Thông tin nhập thêm</th>
                            </tr>
                        </thead>
                        <tbody id="products-tbody">
                            {{-- Product rows will be inserted here by JavaScript --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center align-items-center">
            <button type="submit" class="btn btn-success rounded-pill px-5 py-3 shadow-lg mt-3 fw-bold">
                <i class="fas fa-save me-2"></i>Tạo phiếu nhập
            </button>
        </div>
    </form>

    <!-- Modal for quantity input -->
    <div class="modal fade" id="modal-quantity" tabindex="-1" aria-labelledby="modal-quantity-label" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header bg-primary text-white text-center rounded-top-4">
                    <h5 class="modal-title fw-bold" id="modal-quantity-label">Nhập số lượng</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="quantity_variant" class="form-label fw-semibold">Số lượng:</label>
                        <input id="quantity_variant" class="form-control form-control-lg" type="number"
                            name="quantity_variant" value="1" min="1">
                    </div>
                </div>
                <div class="modal-footer justify-content-center border-top-0 pt-0">
                    <button type="button" class="btn btn-success text-white btn-quantity-submit rounded-pill px-4 py-2">
                        <i class="fas fa-check me-2"></i>Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for color selection -->
    <div class="modal fade" id="modal-colors" tabindex="-1" aria-labelledby="modal-colors-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header bg-primary text-white text-center rounded-top-4">
                    <h5 class="modal-title fw-bold" id="modal-colors-label">Chọn màu sắc</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="text" id="new-color-input" class="form-control" placeholder="Thêm màu mới">
                            <button class="btn btn-primary" id="add-color-btn">Thêm</button>
                        </div>
                    </div>

                    <div class="color-options row">
                        <!-- Color options will be inserted here by JavaScript -->
                    </div>

                    <div class="selected-colors mt-3">
                        <h6 class="fw-bold">Màu đã chọn:</h6>
                        <div id="selected-colors-container" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between border-top-0 pt-0">
                    <button type="button" class="btn btn-danger text-white btn-colors-clear rounded-pill px-4 py-2">
                        <i class="fas fa-trash me-2"></i>Xóa tất cả
                    </button>
                    <button type="button" class="btn btn-success text-white btn-colors-submit rounded-pill px-4 py-2">
                        <i class="fas fa-check me-2"></i>Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="allProductsModal" tabindex="-1" aria-labelledby="allProductsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header bg-primary text-white text-center rounded-top-4">
                    <h5 class="modal-title fw-bold" id="allProductsModalLabel">Danh sách sản phẩm</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Thêm phần tìm kiếm và lọc -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" id="productSearchInput" class="form-control"
                                    placeholder="Tìm kiếm sản phẩm...">
                                <button class="btn btn-outline-secondary" type="button" id="searchProductBtn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <select class="form-select" id="stockFilterSelect">
                                    <option value="all">Tất cả stock</option>
                                    <option value="in_stock">Còn hàng (stock > 0)</option>
                                    <option value="out_of_stock">Hết hàng (stock = 0)</option>
                                    <option value="low_stock">Stock thấp (< 10)</option>
                                </select>
                                <button class="btn btn-outline-secondary" type="button" id="applyFilterBtn">
                                    <i class="fas fa-filter"></i> Lọc
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped align-middle" id="allProductsTable">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" width="50px" class="text-center">Chọn</th>
                                    <th scope="col" width="150px">Sản phẩm</th>
                                    <th scope="col" width="100px">Hình ảnh</th>
                                    <th scope="col" width="120px">Thông tin cơ bản</th>
                                    <th scope="col" class="text-center">Biến thể hiện có</th>
                                </tr>
                            </thead>
                            <tbody id="all-products-tbody">
                                <!-- Danh sách sản phẩm sẽ được load ở đây -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Phân trang và nút xem thêm -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted" id="productCountText">Hiển thị 0/0 sản phẩm</div>
                        <button type="button" class="btn btn-outline-primary" id="loadMoreProductsBtn">
                            <i class="fas fa-chevron-down me-2"></i>Xem thêm
                        </button>
                    </div>
                </div>
                <div class="modal-footer justify-content-between border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 py-2" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Đóng
                    </button>
                    <button type="button" class="btn btn-success text-white rounded-pill px-4 py-2"
                        id="addSelectedProductsBtn">
                        <i class="fas fa-check me-2"></i>Thêm sản phẩm đã chọn
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    {{-- file css phiếu nhập thêm --}}
    <link rel="stylesheet" href="{{ asset('assets/css/inventory-add_extra.css') }}">
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- file js quan trọng phiếu nhập thêm --}}
    <script src="{{ asset('assets/js/inventory-extra.js') }}"></script>
@endsection
@else
{{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

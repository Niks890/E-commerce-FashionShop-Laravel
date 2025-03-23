@can('warehouse workers')
    @extends('admin.master')
    @section('title', 'Thêm Phiếu nhập')
    @section('back-page')
        <a class="text-primary" onclick="window.history.back()">
            <i class="fas fa-chevron-left ms-3"></i>
            <p class="d-inline text-decoration-underline" style="cursor: pointer">Quay lại</p>
        </a>
    @endsection
    @section('content')
        <form id="formCreateInventory" method="POST" action="{{ route('inventory.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{ auth()->user()->id - 1 }}">

            <div class="row form-group">
                <div class="col-6 ">
                    <label for="">Tên sản phẩm:</label>
                    <input type="text" name="product_name" id="" class="form-control" placeholder="">
                    @error('product_name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="">Hình ảnh:</label>
                    <input type="file" name="image" id="fileInput" class="form-control" placeholder="" accept="image/*">
                    @error('image')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <div id="preview">
                        <img class="img-thumbnail rounded m-3 d-none" id="previewImg" src="" alt=""
                            width="200">
                    </div>
                </div>
            </div>

            <div class="row form-group">
                <div class="col-6">
                    <label for="">Danh mục:</label>
                    <select class="form-control" name="category_id">
                        <option>--Chọn danh mục--</option>
                        @foreach ($cats as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="">Chọn nhà cung cấp:</label>
                    <select class="form-control" name="provider_id">
                        <option>--Chọn nhà cung cấp--</option>
                        @foreach ($providers as $provider)
                            <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                        @endforeach
                    </select>
                    @error('provider_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row form-group">
                <div class="col-6">
                    <label for="">Giá nhập:</label>
                    <input type="number" name="price" id="priceInput" class="form-control" placeholder="">
                    @error('price')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="">Màu:</label>
                    <input type="text" name="color" id="colorInput" class="form-control" placeholder="VD: Vàng, Đỏ, Xanh, Tím,...">
                    @error('color')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row form-group">
                <div class="col-6">
                    <label>Chọn kích cỡ:</label>
                    <select class="form-control" name="sizes[]" id="sizes" multiple="multiple">
                        <option value="XS">XS</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        <option value="XXL">XXL</option>
                    </select>
                    @error('sizes')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <input type="hidden" name="formatted_sizes" id="formatted_sizes"> <!-- Ẩn để lưu giá trị -->
                <div class="col-6">
                    <label for="">Thương hiệu:</label>
                    <input type="text" name="brand_name" id="brand_name" class="form-control">
                    @error('brand_name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <input class="btn btn-primary m-3" name="" type="submit" value="Lưu thông tin">
        </form>

        <div class="modal fade" id="modal-quantity" tabindex="-1" aria-labelledby="modal-quantity-label" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content shadow-lg border-0 rounded-4">
                    <div class="modal-header text-center">
                        <h5 class="modal-title fw-bold" id="modal-quantity-label"></h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="quantity_variant">Số lượng:</label>
                        <input id="quantity_variant" class="form-control" type="number" name="quantity_variant" value="1" min="1">
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-success text-white btn-quantity-submit" data-bs-dismiss="modal"><i
                                class="fas fa-check"></i>Ok</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('js')
        <script src="{{ asset('assets/js/plugin/select2/select2.full.min.js') }}"></script>
        <script>
            $(document).ready(function() {
                $("#fileInput").change(function(e) {
                    var file = e.target.files[0];
                    var validTypes = ["image/jpg", "image/jpeg", "image/png", "image/gif", "image/webp", "image/avif"];

                    if (file) {
                        if (validTypes.includes(file.type)) {
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                $("#previewImg").attr("src", e.target.result).show();
                                $("#previewImg").removeClass('d-none');
                            };
                            reader.readAsDataURL(file);
                        } else {
                            $(this).val("");
                            $("#previewImg").hide();
                        }
                    }
                });
            });
        </script>
        {{-- <script>
            $(document).ready(function() {
                $('#priceInput').on('input', function() {
                    let value = $(this).val().replace(/,/g, '');
                    if (!isNaN(value) && value !== '') {
                        $(this).val(Number(value).toLocaleString('en-US'));
                    }
                });
            });
        </script> --}}

        <script>
            $(document).ready(function () {
                let selectedSize = null;
                let sizesWithQuantities = {}; // Lưu size và số lượng

                // Khởi tạo Select2 với custom adapter
                $("#sizes").select2({
                    placeholder: "Chọn kích cỡ",
                    allowClear: true,
                    templateSelection: function (selection) {
                        if (sizesWithQuantities[selection.id]) {
                            return `${selection.id}-${sizesWithQuantities[selection.id]}`;
                        }
                        return selection.text;
                    }
                });

                // Khi chọn một size, mở modal nhập số lượng
                $("#sizes").on("select2:select", function (e) {
                    selectedSize = e.params.data.id;
                    $('#modal-quantity-label').text("Nhập số lượng cho size " + selectedSize);
                    $("#quantity_variant").val(sizesWithQuantities[selectedSize] || ""); // Hiển thị số lượng nếu đã có
                    $("#modal-quantity").modal("show");
                });

                // Khi nhấn "OK" trong modal
                $(".btn-quantity-submit").on("click", function () {
                    let quantity = $("#quantity_variant").val();
                    if (quantity && quantity > 0) {
                        sizesWithQuantities[selectedSize] = quantity; // Lưu số lượng
                        $("#sizes").trigger("change"); // Cập nhật hiển thị Select2
                        $("#modal-quantity").modal("hide");
                    } else {
                        alert("Vui lòng nhập số lượng hợp lệ!");
                    }
                });

                $("#formCreateInventory").on("submit", function (e) {
                    let formattedSizes = [];
                    for (let size in sizesWithQuantities) {
                        formattedSizes.push(`${size}-${sizesWithQuantities[size]}`);
                    }
                    $("#formatted_sizes").val(formattedSizes.join(","));
                    if (formattedSizes.length === 0) {
                        alert("Vui lòng chọn ít nhất một size và nhập số lượng!");
                        e.preventDefault();
                    }
                });

            });
        </script>
    @endsection
@else
    {{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan


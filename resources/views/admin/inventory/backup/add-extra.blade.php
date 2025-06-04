@can('warehouse workers')
    @extends('admin.master')
    @section('title', 'Nhập thêm')
    @section('back-page')
        <div class="d-flex align-items-center mb-3">
            <button class="btn btn-outline-primary rounded-pill px-3 py-2 shadow-sm"
                onclick="window.history.back()" style="transition: all 0.3s ease; border: 2px solid #007bff;">
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

            <div class="row form-group">
                <div class="col-6 ">
                    <label for="">Tên sản phẩm:</label>
                    <input type="text" name="product_name_disabled" id="product_name_disabled" class="form-control" placeholder="" disabled>
                    <input type="hidden" name="product_id" id="product_id">
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
                    <select class="form-control" name="category_id_disabled" disabled>
                        @foreach ($cats as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="category_id" id="category_id">
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
                    <input type="number" name="price" id="priceInput" class="form-control">
                    @error('price')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="">Màu:</label>
                    <input type="text" name="color" id="colorInput" class="form-control" placeholder="VD: Vàng, Đỏ, Xanh, Tím,...">
                    <small class="colors-available"></small>
                    @error('color')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <div class="row form-group form-sizes-and-quantities">
                <div class="col-6">
                    <label>Chọn kích cỡ và số lượng:</label>
                    <select class="form-control" name="sizes[]" id="sizes" multiple="multiple">
                        <option value="XS">XS</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        <option value="XXL">XXL</option>
                    </select>
                    <small class="sizes-available"></small>
                    @error('sizes')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <input type="hidden" name="formatted_sizes" id="formatted_sizes"> <!-- Ẩn để lưu giá trị -->
                <div class="col-6">
                    <label for="">Thương hiệu:</label>
                    <input type="text" name="brand_name" id="brand_name" class="form-control" readonly>
                </div>
            </div>
            <input class="btn btn-primary m-3" name="" type="submit" value="Lưu thông tin">
        </form>

        <div class="modal fade" id="modal-quantity" tabindex="-1" aria-labelledby="modal-quantity-label" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content shadow-lg border-0 rounded-4">
                    <div class="modal-header text-center">
                        <h5 class="modal-title fw-bold" id="modal-quantity-label"></h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="quantity_variant">Số lượng:</label>
                        <input id="quantity_variant" class="form-control" type="number" name="quantity_variant"
                            value="1" min="1">
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-success text-white btn-quantity-submit"
                            data-bs-dismiss="modal"><i class="fas fa-check"></i>Ok</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('js')
        <script src="{{ asset('assets/js/plugin/select2/select2.full.min.js') }}"></script>
        <script>
            $(document).ready(function() {
                //Xử lý input nhập file hình ảnh
                $("#fileInput").change(function(e) {
                    var file = e.target.files[0];
                    var validTypes = ["image/jpg", "image/jpeg", "image/png", "image/gif", "image/webp"];

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

        @php
        if (isset($_GET['inventory_id']) && $_GET['inventory_id']) {
            $inventory_id = $_GET['inventory_id'];
        } else {
            $inventory_id = null;
        }
        @endphp

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                fetch(`http://127.0.0.1:8000/api/inventory/${@json($inventory_id)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status_code === 200) {
                            const productData = data.data.detail[0].product;
                            const providerData = data.data.provider;
                            const detailData = data.data.detail[0];

                            // Điền dữ liệu vào form
                            document.getElementById("product_name_disabled").value = productData.name || "";
                            document.getElementById("product_id").value = productData.id || "";
                            document.querySelector("select[name='category_id_disabled']").value = productData.category.id ||
                                "";
                            document.getElementById('category_id').value = productData.category.id || "";
                            document.querySelector("select[name='provider_id']").value = providerData.id || "";
                            document.getElementById("priceInput").value = detailData.price || "";
                            document.getElementById("brand_name").value = productData.brand || "";

                            // Điền danh sách màu sắc
                            document.querySelector(".colors-available").textContent = "(Các màu sắc được nhập về: " +
                                [...new Set(detailData.product["product-variant"]
                                    .map(variant => variant.color))]
                                    .join(", ") + ")";
                            // Điền danh sách kích cỡ
                            const sizesSelect = document.getElementById("sizes");
                            const sizes = detailData.sizes.split(", ");
                            let container = document.querySelector(".form-sizes-and-quantities");
                            detailData.product["product-variant"].forEach(variant => {
                                let input = document.createElement("input");
                                input.classList = "previous_sizes_and_stocks";
                                input.type = "hidden";
                                input.name = `variant[${variant.size}]`;
                                input.value = variant.stock;
                                container.appendChild(input);
                                // console.log(input);
                            });
                            const sizesInput = document.querySelector(".sizes-available");
                            sizesInput.textContent = "(Các kích cỡ đã nhập về: " +
                                detailData.product["product-variant"]
                                .map(variant =>
                                    `${variant.color}-${variant.size} (${variant.stock !== null ? variant.stock : 0} cái)`)
                                .join(", ") + ")";

                            // Hiển thị hình ảnh sản phẩm
                            document.getElementById("previewImg").src = `${productData.image}`;
                            const previewImg = document.getElementById("previewImg");
                            previewImg.src = `${productData.image}`;
                            previewImg.classList.remove("d-none");
                        }
                    })
                    .catch(error => console.error("Lỗi khi lấy API:", error));
            });
        </script>



        <script>
            $(document).ready(function() {
                let selectedSize = null;
                let sizesWithQuantities = {}; // Lưu size và số lượng
                // let previousStock = {}; // Lưu số lượng cũ của size

                setTimeout(function() {
                    $(".previous_sizes_and_stocks").each(function() {
                        let size = $(this).attr("name").split("[")[1].split("]")[0];
                        let stock = $(this).val();
                        previousStock[size] = parseInt(stock);
                        // console.log(previousStock);
                    });
                }, 2000);


                $("#sizes").select2({
                    placeholder: "Chọn kích cỡ",
                    allowClear: true,
                    templateSelection: function(selection) {
                        if (sizesWithQuantities[selection.id]) {
                            return `${selection.id}-${sizesWithQuantities[selection.id]}`;
                        }
                        return selection.text;
                    }
                });

                // Khi chọn một size, mở modal nhập số lượng
                $("#sizes").on("select2:select", function(e) {
                    selectedSize = e.params.data.id;
                    $('#modal-quantity-label').text("Nhập số lượng cho size " + selectedSize);
                    $("#quantity_variant").val(sizesWithQuantities[selectedSize] ||
                    ""); // Hiển thị số lượng nếu đã có
                    $("#modal-quantity").modal("show");
                });

                // Khi nhấn "OK" trong modal
                $(".btn-quantity-submit").on("click", function() {
                    let quantity = parseInt($("#quantity_variant").val()) || 0; // Lấy số lượng nhập vào
                    if (quantity > 0) {
                        // Nếu size đã có trong previousStock, cộng dồn số lượng
                        // let existingStock = previousStock[selectedSize] || 0;
                        sizesWithQuantities[selectedSize] = quantity

                        $("#sizes").trigger("change"); // Cập nhật hiển thị Select2
                        $("#modal-quantity").modal("hide");
                    } else {
                        alert("Vui lòng nhập số lượng hợp lệ!");
                    }
                });

                $("#formCreateInventory").on("submit", function(e) {
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

@can('salers')
    @extends('admin.master')
    @section('title', 'Sửa Thông Tin Sản phẩm')
    @section('back-page')
        <a class="text-primary" onclick="window.history.back()">
            <i class="fas fa-chevron-left ms-3"></i>
            <p class="d-inline text-decoration-underline" style="cursor: pointer">Quay lại</p>
        </a>
    @endsection
    @section('content')
        <form method="POST" action="{{ route('product.update', $product->id) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row form-group">
                <div class="col-5">
                    <label for="">Tên sản phẩm:</label>
                    <input type="text" name="name" id="" class="form-control" placeholder=""
                        value="{{ $product->product_name }}">
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-4">
                    <label for="">Giá niêm yết:</label>
                    <input type="number" name="price" id="" class="form-control" placeholder="" value="{{ $product->price }}">
                    @error('price')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-3">
                    <label for="">Cập nhật thông tin cho từng kích cỡ:</label><br />
                    <button type="button" name="sizes_modal_open" id="" class="btn btn-success btn-update-size">Cập
                        nhật</button>
                </div>
            </div>

            <div class="row form-group">
                <div class="col-5">
                    <label for="">Thương hiệu:</label>
                    <input type="text" name="brand" id="" class="form-control" placeholder=""
                        value="{{ $product->brand }}" disabled>
                    @error('brand')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-7">
                    <label for="">Thêm chương trình khuyến mãi:</label>
                    <select class="form-control" name="discount_id">
                        <option value="">--Chọn chương trình khuyến mãi--</option>
                        @foreach ($discounts as $discount)
                            <option value="{{ $discount->id }}" @selected($discount->id == $product->discount_id)>{{ $discount->name }}</option>
                        @endforeach
                    </select>
                    @error('discount_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row form-group">
                <div class="col-5">
                    <label for="">Danh mục:</label>
                    <select class="form-control" name="category_id" disabled>
                        @foreach ($cats as $cat)
                            <option value="{{ $cat->id }}" @selected($cat->id == $product->category_id)>{{ $cat->category_name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-7">
                    <label for="">Hình ảnh:</label>
                    <input type="file" name="image" id="" class="form-control fileInput" placeholder="" accept="images/*">
                    <input type="hidden" name="image_path"  value="{{ $product->image }}">
                    <img class="previewImg" src="uploads/{{ $product->image }}" alt="{{ $product->image }}" width="150">
                    <br />
                    @error('image')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="">Tag:</label>
                <input type="text" data-role="tagsinput" name="product_tags" class="form-control" value="{{ $product->tags }}">
            </div>
            @error('product_tags')
                <small class="text-danger">{{ $message }}</small>
            @enderror

            <div class="row form-group">
                <div class="col-6">
                    <label for="">Chất liệu:</label>
                    <input type="text" name="material" id="" class="form-control" placeholder="" value="{{ $product->material }}">
                </div>
                @error('material')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
                <div class="col-6">
                    <label for="">Màu sắc:</label>
                    <input type="text" name="color" id="" class="form-control" placeholder="" disabled value="{{ $productVariants[0]->color }}">
                </div>
                @error('color')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="row form-group">
                <div class="col-7">
                    <label for="">Mô tả:</label>
                    <textarea class="form-control" name="description" placeholder="Mô tả sản phẩm...">{{$product->description}}</textarea>
                </div>
                @error('description')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
                <div class="col-5">
                    <label for="">Mô tả ngắn:</label>
                    <textarea class="form-control" name="short_description" placeholder="Mô tả ngắn...">{{$product->short_description}}</textarea>
                </div>
                @error('short_description')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="status" value="1"
                            @checked($product->status == 1)>
                        Hiển thị
                    </label>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="status" value="0"
                            @checked($product->status == 0)>
                        Ẩn
                    </label>
                </div>
                @error('status')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- Start Update Size Modal -->
            <div id="modal-update-size" class="modal-update-size js-modal">
                @csrf
                <div class="modal-container-update js-modal-container p-3">
                    <div class="modal-close js-modal-close">
                        <i class="fas fa-times"></i>
                    </div>
                    <div class="modal-header d-flex align-item-center justify-content-center fw-bold"
                        style="font-size: 1.5rem">
                        Cập nhật thông tin cho từng kích cỡ
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            @foreach ($productVariants as $productVariant)
                                <div class="sidebar-wrapper scrollbar scrollbar-inner">
                                    <div class="sidebar-content">
                                        <ul class="nav nav-secondary" id="parentSizeList">
                                            <li class="nav-item">
                                                <a class="text-dark" data-bs-toggle="collapse"
                                                    href="#{{ $productVariant->id . $productVariant->size }}">
                                                    <p class="d-inline fw-bold size-text" style="font-size: 1.25rem">
                                                        {{ $productVariant->size }} - {{ $productVariant->color }}
                                                    </p>
                                                    <span class="caret"></span>
                                                </a>
                                                <div class="collapse row"
                                                    id="{{ $productVariant->id . $productVariant->size }}" data-bs-parent="#parentSizeList">
                                                    <div class="col-6">
                                                        <label for="">+ Hình ảnh:</label>
                                                        <input type="file" name="image_variant[{{ $productVariant->id }}]"
                                                            id="" class="form-control fileInput" placeholder=""
                                                            accept="images/*">
                                                        <img class="previewImg" src="{{ asset('uploads/' . $productVariant->image) }}"
                                                            alt="" width="45">
                                                        <input class="image_path_hidden" type="hidden" name="image_path_variant[{{ $productVariant->id }}]"
                                                            value="{{ $productVariant->image }}">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="d-inline" for="">+ Giá:</label>
                                                        <input type="number"
                                                            name="price_variant[{{ $productVariant->id }}]"
                                                            value="{{ $productVariant->price }}" id=""
                                                            class="form-control" placeholder="Nhập giá cho kích cỡ này...">
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-primary me-3 my-2" name="" value="Lưu thông tin" />
                    </div>
                </div>
            </div>
            <!-- End Update Size Modal -->

            <input class="btn btn-primary" name="" type="submit" value="Lưu thông tin">
        </form>
    @endsection

    @section('css')
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-tagsinput.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}" />
    @endsection

    @section('js')
        <script src="{{ asset('assets/js/plugin/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>

        <script>
            $(document).ready(function() {
                $(".fileInput").change(function(e) {
                    var file = e.target.files[0];
                    var validTypes = ["image/jpg", "image/jpeg", "image/png", "image/gif", "image/webp", "image/avif"];

                    if (file) {
                        if (validTypes.includes(file.type)) {
                            var reader = new FileReader();
                            reader.onload = (event) => {
                                // Tìm ảnh preview tương ứng chỉ trong phần tử cha chứa nó
                                $(this).parent().find(".previewImg").attr("src", event.target.result).show();
                            };
                            reader.readAsDataURL(file);
                        } else {
                            $(this).val(""); // Reset input file nếu không hợp lệ
                            $(this).parent().find(".previewImg").hide();
                        }
                    }
                });
            });

        </script>
        <script>
            $(document).ready(function(e) {
                $('.btn-update-size').click(function() {
                    $('.js-modal').addClass("open");
                });
                $('.js-modal-close').click(function() {
                    $('.js-modal').removeClass("open");
                });
            });
        </script>
    @endsection
@else
    {{ abort(403, 'Bạn không có quyền truy cập trang này!') }}
@endcan

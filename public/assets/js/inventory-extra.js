
document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const inventory_id = urlParams.get('inventory_id');

    document.getElementById('note').value = `nhập thêm từ phiếu #${inventory_id}`;

    if (!inventory_id) {
        console.error("Không tìm thấy inventory_id trong URL.");
        alert("Không tìm thấy ID phiếu nhập. Vui lòng thử lại.");
        window.history.back();
        return;
    }

    let productsData = [];
    let currentProductIndex = null;
    let selectedColors = [];
    let customColors = [];

    const defaultColors = [{
        name: 'Đen',
        code: '#000000'
    },
    {
        name: 'Trắng',
        code: '#ffffff'
    },
    {
        name: 'Xám',
        code: '#808080'
    },
    {
        name: 'Xanh navy',
        code: '#000080'
    },
    {
        name: 'Xanh dương',
        code: '#0000ff'
    },
    {
        name: 'Đỏ',
        code: '#ff0000'
    },
    {
        name: 'Hồng',
        code: '#ffc0cb'
    },
    {
        name: 'Vàng',
        code: '#ffff00'
    },
    {
        name: 'Xanh lá',
        code: '#008000'
    },
    {
        name: 'Nâu',
        code: '#a52a2a'
    },
    {
        name: 'Be',
        code: '#f5f5dc'
    },
    {
        name: 'Cam',
        code: '#ffa500'
    },
    {
        name: 'Tím',
        code: '#800080'
    }
    ];

    // Hàm hiển thị các màu có sẵn
    function renderColorOptions() {
        const container = $('.color-options');
        container.empty();

        defaultColors.concat(customColors).forEach(color => {
            container.append(`
                <div class="col-4 col-md-3 mb-3">
                    <div class="color-option" data-color="${color.name}" style="background-color: ${color.code};">
                        <span class="delete-color" data-color="${color.name}">×</span>
                    </div>
                    <span>${color.name}</span>
                </div>
            `);
        });
    }

    // Hàm tạo màu ngẫu nhiên cho màu mới
    function getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    // Fetch inventory data
    fetch(`http://127.0.0.1:8000/api/inventory/${inventory_id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.status_code === 200) {
                const inventoryData = data.data;

                document.getElementById('provider_name_display').value = inventoryData.provider.name ||
                    "";
                document.getElementById('provider_id_hidden').value = inventoryData.provider.id || "";

                const groupedProducts = {};
                inventoryData.detail.forEach(item => {
                    if (!groupedProducts[item.product.id]) {
                        groupedProducts[item.product.id] = {
                            product: item.product,
                            all_product_variants: item.product['product-variant'] || [],
                            variants_in_slip: []
                        };
                    }

                    const parts = item.sizes.split('-');
                    let variantSizeFromSlip = '';
                    let variantColorFromSlip = '';

                    if (parts.length >= 3) {
                        variantSizeFromSlip = parts[0].trim();
                        variantColorFromSlip = parts[2].trim();
                    } else {
                        console.warn('Unexpected "sizes" format:', item.sizes);
                        variantColorFromSlip = item.color || '';
                        variantSizeFromSlip = item.sizes.trim();
                    }

                    groupedProducts[item.product.id].variants_in_slip.push({
                        color: variantColorFromSlip,
                        sizes: variantSizeFromSlip,
                        quantity_in_slip: item.quantity,
                        price: item.price
                    });
                });

                const uniqueProducts = Object.values(groupedProducts);

                productsData = uniqueProducts.map((item, index) => ({
                    product_id: item.product.id,
                    product_name: item.product.name,
                    product_image: item.product.image,
                    category_name: item.product.category.name,
                    brand_name: item.product.brand,
                    all_product_variants: item.all_product_variants,
                    variants_in_slip: item.variants_in_slip,
                    new_colors: [],
                    new_price: '',
                    new_sizes_quantities: {}
                }));

                const productsTbody = document.getElementById('products-tbody');
                productsTbody.innerHTML = '';

                productsData.forEach((productItem, index) => {
                    const slipVariantsMap = new Map();
                    productItem.variants_in_slip.forEach(v => {
                        slipVariantsMap.set(`${v.color}-${v.sizes}`, {
                            quantity_in_slip: v.quantity_in_slip,
                            price: v.price
                        });
                    });

                    const row = document.createElement('tr');
                    row.id = `product-row-${index}`;
                    row.innerHTML = `
                        <td class="text-center">
                            <input type="checkbox" class="product-check form-check-input" data-index="${index}" checked>
                            <input type="hidden" name="products[${index}][product_id]" value="${productItem.product_id}">
                        </td>
                        <td>
                            <strong>${productItem.product_name}</strong>
                        </td>
                        <td>
                            <img src="${productItem.product_image}" class="product-img" alt="${productItem.product_name}">
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <small><strong>Danh mục:</strong> ${productItem.category_name}</small>
                                <small><strong>Thương hiệu:</strong> ${productItem.brand_name}</small>
                            </div>
                        </td>
                        <td>
                            <div class="variant-scroll" style="max-height: 150px; overflow-y: auto;">
                                <table class="variant-table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Màu</th>
                                            <th>Size</th>
                                            <th>Stock hiện tại</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    ${productItem.all_product_variants.map(variant => `
                                        <tr>
                                            <td>${variant.color}</td>
                                            <td><span class="badge bg-secondary">${variant.size}</span></td>
                                            <td>${variant.stock || 0}</td>
                                        </tr>
                                    `).join('')}
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-link p-0 mt-2 btn-last-prices" data-index="${index}">
                                <i class="fas fa-search-dollar"></i> Xem giá nhập gần nhất
                            </button>
                        </td>
                        <td>
                            <div class="compact-form">
                                <div class="form-group mb-2">
                                    <label class="form-label fw-bold small">Giá nhập:</label>
                                    <input type="number" name="products[${index}][new_price]"
                                           class="form-control form-control-sm new-price-input"
                                           data-index="${index}"
                                           placeholder="Giá nhập"
                                           min="0">
                                </div>
                                <div class="form-group mb-2">
                                    <label class="form-label fw-bold small">Màu mới:</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary w-100 btn-select-colors"
                                            data-index="${index}">
                                        <i class="fas fa-palette me-1"></i> Chọn màu
                                    </button>
                                    <div class="selected-colors-display mt-1" data-index="${index}"></div>
                                    <input type="hidden" name="products[${index}][new_colors]"
                                           class="formatted-new-colors"
                                           data-index="${index}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label fw-bold small">Size & SL:</label>
                                    <select class="form-select form-select-sm select-sizes"
                                            name="products[${index}][new_sizes][]"
                                            multiple="multiple"
                                            data-index="${index}">
                                        ${['XS', 'S', 'M', 'L', 'XL', 'XXL'].map(size =>
                        `<option value="${size}">${size}</option>`
                    ).join('')}
                                    </select>
                                    <input type="hidden" name="products[${index}][formatted_new_sizes]"
                                           class="formatted-new-sizes"
                                           data-index="${index}">
                                </div>
                            </div>
                        </td>
                    `;
                    productsTbody.appendChild(row);
                });

                // Gắn sự kiện cho nút xem giá nhập gần nhất
                $('.btn-last-prices').off('click').on('click', function () {
                    const idx = $(this).data('index');
                    showLastPricesModal(productsData[idx]);
                });

                // Initialize select2 for sizes
                $('.select-sizes').select2({
                    theme: "bootstrap-5",
                    placeholder: "Chọn size",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#products_container'),
                    templateSelection: formatSizeSelection,
                    templateResult: formatSizeResult
                });

                // Render color options
                renderColorOptions();

                // Xử lý thêm màu mới
                $('#add-color-btn').click(function () {
                    const colorName = $('#new-color-input').val().trim();
                    if (colorName && !defaultColors.concat(customColors).some(c => c.name ===
                        colorName)) {
                        customColors.push({
                            name: colorName,
                            code: getRandomColor()
                        });
                        renderColorOptions();
                        $('#new-color-input').val('');
                    }
                });

                // Xử lý xóa màu
                $(document).on('click', '.delete-color', function (e) {
                    e.stopPropagation();
                    const colorName = $(this).data('color');
                    customColors = customColors.filter(c => c.name !== colorName);

                    // Xóa khỏi danh sách đã chọn nếu có
                    const index = selectedColors.indexOf(colorName);
                    if (index !== -1) {
                        selectedColors.splice(index, 1);
                        updateSelectedColorsDisplay();
                    }

                    renderColorOptions();
                });

                // Xử lý xóa tất cả màu đã chọn
                $('.btn-colors-clear').click(function () {
                    selectedColors = [];
                    updateSelectedColorsDisplay();
                });

                // Color selection modal handlers
                $(document).on('click', '.btn-select-colors', function () {
                    currentProductIndex = $(this).data('index');
                    selectedColors = productsData[currentProductIndex].new_colors || [];
                    updateSelectedColorsDisplay();
                    $('#modal-colors').modal('show');
                });

                $(document).on('click', '.color-option', function () {
                    const color = $(this).data('color');
                    const index = selectedColors.indexOf(color);

                    if (index === -1) {
                        selectedColors.push(color);
                        $(this).addClass('selected');
                    } else {
                        selectedColors.splice(index, 1);
                        $(this).removeClass('selected');
                    }

                    updateSelectedColorsDisplay();
                });

                $('.btn-colors-submit').click(function () {
                    if (currentProductIndex !== null) {
                        productsData[currentProductIndex].new_colors = selectedColors;
                        updateProductColorDisplay(currentProductIndex);
                        $('#modal-colors').modal('hide');
                    }
                });

                // Quantity modal handlers
                $('.select-sizes').on("select2:select", function (e) {
                    const selectedSize = e.params.data.id;
                    const productIndex = $(this).data('index');

                    // Kiểm tra nếu size đã được chọn
                    if (productsData[productIndex].new_sizes_quantities[selectedSize]) {
                        alert(
                            `Size ${selectedSize} đã được chọn. Vui lòng chọn size khác hoặc cập nhật số lượng cho size này.`
                        );
                        $(this).val(Object.keys(productsData[productIndex]
                            .new_sizes_quantities)).trigger('change');
                        return;
                    }

                    // Phần còn lại giữ nguyên
                    $('#modal-quantity-label').text(`Nhập số lượng cho size ${selectedSize}`);
                    $("#quantity_variant").val(
                        productsData[productIndex].new_sizes_quantities[selectedSize] || 1
                    );

                    $('#modal-quantity').data('product-index', productIndex);
                    $('#modal-quantity').data('selected-size', selectedSize);
                    $("#modal-quantity").modal("show");
                });

                $(".btn-quantity-submit").on("click", function () {
                    const quantity = parseInt($("#quantity_variant").val()) || 0;
                    const productIndex = $('#modal-quantity').data('product-index');
                    const selectedSize = $('#modal-quantity').data('selected-size');

                    if (quantity > 0) {
                        productsData[productIndex].new_sizes_quantities[selectedSize] =
                            quantity;
                        updateProductSizesDisplay(productIndex);
                        $("#modal-quantity").modal("hide");
                    } else {
                        alert("Vui lòng nhập số lượng hợp lệ!");
                    }
                });

                $('.select-sizes').on("select2:unselect", function (e) {
                    const unselectedSize = e.params.data.id;
                    const productIndex = $(this).data('index');
                    delete productsData[productIndex].new_sizes_quantities[unselectedSize];
                    updateProductSizesDisplay(productIndex);
                });

                // Product selection handlers
                document.getElementById('selectAllProductsBtn').addEventListener('click', function () {
                    document.querySelectorAll('.product-check').forEach(checkbox => {
                        checkbox.checked = true;
                        const index = checkbox.dataset.index;
                        toggleProductRow(index, true);
                    });
                });

                document.getElementById('deselectAllProductsBtn').addEventListener('click', function () {
                    document.querySelectorAll('.product-check').forEach(checkbox => {
                        checkbox.checked = false;
                        const index = checkbox.dataset.index;
                        toggleProductRow(index, false);

                        // Reset data when deselecting
                        productsData[index].new_colors = [];
                        productsData[index].new_price = '';
                        productsData[index].new_sizes_quantities = {};

                        // Update UI
                        $(`#product-row-${index} .selected-colors-display`).empty();
                        $(`#product-row-${index} .formatted-new-colors`).val('');
                        $(`#product-row-${index} .new-price-input`).val('');
                        $(`#product-row-${index} .select-sizes`).val(null).trigger(
                            'change');
                        $(`#product-row-${index} .formatted-new-sizes`).val('');
                    });
                });

                document.querySelectorAll('.product-check').forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        const index = this.dataset.index;
                        const isChecked = this.checked;
                        toggleProductRow(index, isChecked);

                        if (!isChecked) {
                            // Reset data when unchecking
                            productsData[index].new_colors = [];
                            productsData[index].new_price = '';
                            productsData[index].new_sizes_quantities = {};

                            // Update UI
                            $(`#product-row-${index} .selected-colors-display`).empty();
                            $(`#product-row-${index} .formatted-new-colors`).val('');
                            $(`#product-row-${index} .new-price-input`).val('');
                            $(`#product-row-${index} .select-sizes`).val(null).trigger(
                                'change');
                            $(`#product-row-${index} .formatted-new-sizes`).val('');
                        }
                    });
                });

                // Form submission
                $("#formCreateInventory").on("submit", function (e) {
                    e.preventDefault();

                    const note = $('#note').val().trim();

                    // Remove any existing hidden input
                    $(this).find('input[name="products_to_add"]').remove();

                    const selectedProductsToSend = [];
                    let validationError = false;
                    let hasSelectedProducts = false;

                    // Loop through all products
                    productsData.forEach((productItem, index) => {
                        const isChecked = $(`#product-row-${index} .product-check`).is(
                            ':checked');

                        if (isChecked) {
                            hasSelectedProducts = true;

                            // Get values from form
                            productItem.new_price = $(
                                `#product-row-${index} .new-price-input`).val();
                            productItem.new_colors = $(
                                `#product-row-${index} .formatted-new-colors`).val()
                                ?.split(',') || [];

                            const hasNewPrice = productItem.new_price && parseFloat(
                                productItem.new_price) > 0;
                            const hasNewSizesQuantities = Object.keys(productItem
                                .new_sizes_quantities).length > 0;
                            const hasNewColors = productItem.new_colors.length > 0;

                            const hasNewData = hasNewPrice || hasNewSizesQuantities ||
                                hasNewColors;

                            if (hasNewData) {
                                if ((hasNewColors || hasNewPrice) && !
                                    hasNewSizesQuantities) {
                                    alert(
                                        `Lỗi: Vui lòng chọn size và số lượng cho sản phẩm "${productItem.product_name}"`
                                    );
                                    validationError = true;
                                    return;
                                }

                                if (hasNewSizesQuantities && (!hasNewColors || !
                                    hasNewPrice)) {
                                    alert(
                                        `Lỗi: Vui lòng nhập đủ màu và giá cho sản phẩm "${productItem.product_name}"`
                                    );
                                    validationError = true;
                                    return;
                                }

                                selectedProductsToSend.push({
                                    product_id: productItem.product_id,
                                    new_colors: productItem.new_colors,
                                    new_price: productItem.new_price,
                                    new_sizes_quantities: productItem
                                        .new_sizes_quantities
                                });
                            }
                        }
                    });


                    let hasDuplicateSizes = false;

                    selectedProductsToSend.forEach(product => {
                        const sizes = Object.keys(product.new_sizes_quantities);
                        const uniqueSizes = new Set(sizes);

                        if (sizes.length !== uniqueSizes.size) {
                            hasDuplicateSizes = true;
                            const productName = productsData.find(p => p.product_id ===
                                product.product_id)?.product_name || '';
                            alert(
                                `Lỗi: Sản phẩm "${productName}" có size bị trùng lặp. Vui lòng kiểm tra lại.`
                            );
                        }
                    });

                    if (hasDuplicateSizes) {
                        alert("Lỗi: Có size bị trùng lặp. Vui lòng kiểm tra lại.");
                        return;
                    }

                    if (validationError) {
                        return;
                    }

                    if (!hasSelectedProducts || selectedProductsToSend.length === 0) {
                        alert("Vui lòng chọn ít nhất một sản phẩm và nhập thông tin bổ sung!");
                        return;
                    }

                    // Create hidden input with selected products data
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'products_to_add';
                    input.value = JSON.stringify(selectedProductsToSend);
                    this.appendChild(input);


                    if (note) {
                        const noteInput = document.createElement('input');
                        noteInput.type = 'hidden';
                        noteInput.name = 'note';
                        noteInput.value = note;
                        this.appendChild(noteInput);
                    }


                    // Submit form
                    this.submit();
                });
            }
        })
        .catch(error => {
            console.error("Lỗi khi lấy API:", error);
            alert(
                "Không thể tải thông tin phiếu nhập. Vui lòng kiểm tra kết nối mạng hoặc thử lại sau."
            );
        });

    // Hàm định dạng hiển thị size trong dropdown (có số lượng)
    function formatSizeSelection(state) {
        if (!state.id) {
            return state.text;
        }

        const $option = $(state.element);
        const size = state.id;
        const productIndex = $option.closest('select').data('index');
        const qty = productsData[productIndex]?.new_sizes_quantities[size] || 0;

        if (qty > 0) {
            return $(`<span>${size} <span class="badge bg-success ms-1">${qty}</span></span>`);
        }
        return state.text;
    }

    // Hàm định dạng hiển thị size trong kết quả tìm kiếm dropdown
    function formatSizeResult(state) {
        if (!state.id) {
            return state.text;
        }

        const $option = $(state.element);
        const size = state.id;
        const productIndex = $option.closest('select').data('index');
        const qty = productsData[productIndex]?.new_sizes_quantities[size] || 0;

        if (qty > 0) {
            return $(`<span>${size} <small class="text-muted">(Đã chọn: ${qty})</small></span>`);
        }
        return state.text;
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }

    function toggleProductRow(index, isEnabled) {
        $(`#product-row-${index} input:not([type="hidden"]), #product-row-${index} select`)
            .prop('disabled', !isEnabled);
        $(`#product-row-${index} .select-sizes`).select2(isEnabled ? 'enable' : 'disable');
        $(`#product-row-${index} .btn-select-colors`).prop('disabled', !isEnabled);
    }

    function updateSelectedColorsDisplay() {
        const container = $('#selected-colors-container');
        container.empty();

        selectedColors.forEach(color => {
            const colorCode = getColorCode(color);
            container.append(`
                <div class="selected-color-badge">
                    <div class="color-preview" style="background-color: ${colorCode};"></div>
                    ${color}
                </div>
            `);
        });

        // Highlight selected options in modal
        $('.color-option').removeClass('selected');
        selectedColors.forEach(color => {
            $(`.color-option[data-color="${color}"]`).addClass('selected');
        });
    }

    function updateProductColorDisplay(index) {
        const displayContainer = $(`#product-row-${index} .selected-colors-display`);
        const hiddenInput = $(`#product-row-${index} .formatted-new-colors`);

        displayContainer.empty();
        hiddenInput.val(selectedColors.join(','));

        selectedColors.forEach(color => {
            const colorCode = getColorCode(color);
            displayContainer.append(`
                <span class="badge me-1 mb-1" style="background-color: ${colorCode}; color: ${getTextColor(colorCode)}">
                    ${color}
                </span>
            `);
        });
    }

    function updateProductSizesDisplay(index) {
        const formattedSizesInput = $(`#product-row-${index} .formatted-new-sizes`);
        const selectElement = $(`#product-row-${index} .select-sizes`);

        // Kiểm tra trùng lặp size
        const sizeCounts = {};
        Object.keys(productsData[index].new_sizes_quantities).forEach(size => {
            sizeCounts[size] = (sizeCounts[size] || 0) + 1;
        });

        const duplicateSizes = Object.keys(sizeCounts).filter(size => sizeCounts[size] > 1);
        if (duplicateSizes.length > 0) {
            alert(`Lỗi: Size ${duplicateSizes.join(', ')} đã được chọn nhiều lần. Vui lòng kiểm tra lại.`);
            // Xóa các size trùng
            duplicateSizes.forEach(size => {
                delete productsData[index].new_sizes_quantities[size];
            });
            // Cập nhật lại select2
            selectElement.val(Object.keys(productsData[index].new_sizes_quantities)).trigger('change');
        }

        // Phần còn lại giữ nguyên
        formattedSizesInput.val(
            Object.entries(productsData[index].new_sizes_quantities)
                .map(([size, qty]) => `${size}-${qty}`)
                .join(',')
        );

        // Cập nhật hiển thị trên select2
        selectElement.find('option').each(function () {
            const size = $(this).val();
            const qty = productsData[index].new_sizes_quantities[size] || 0;

            if (qty > 0) {
                $(this).text(`${size} (${qty})`);
            } else {
                $(this).text(size);
            }
        });

        // Cập nhật hiển thị badge số lượng trên các tag đã chọn
        setTimeout(() => {
            selectElement.next('.select2-container').find('.select2-selection__choice').each(
                function () {
                    const $choice = $(this);
                    const size = $choice.attr('title');
                    const qty = productsData[index].new_sizes_quantities[size] || 0;

                    // Xóa badge cũ nếu có
                    $choice.find('.size-quantity-badge').remove();

                    if (qty > 0) {
                        // Thêm badge mới
                        $choice.append(`
                        <span class="size-quantity-badge">
                            <span class="quantity-display">${qty}</span>
                        </span>
                    `);
                    }
                });
        }, 100);

        selectElement.trigger("change");
    }

    function getColorCode(colorName) {
        // Kiểm tra trong defaultColors trước
        const defaultColor = defaultColors.find(c => c.name === colorName);
        if (defaultColor) return defaultColor.code;

        // Kiểm tra trong customColors
        const customColor = customColors.find(c => c.name === colorName);
        if (customColor) return customColor.code;

        // Nếu không tìm thấy, trả về màu mặc định
        return '#cccccc';
    }

    function getTextColor(bgColor) {
        // Convert hex to RGB
        const r = parseInt(bgColor.substr(1, 2), 16);
        const g = parseInt(bgColor.substr(3, 2), 16);
        const b = parseInt(bgColor.substr(5, 2), 16);

        // Calculate luminance
        const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

        // Return black or white depending on luminance
        return luminance > 0.5 ? '#000000' : '#ffffff';
    }


    $('#product_search').select2({
        theme: "bootstrap-5",
        placeholder: "Tìm kiếm sản phẩm",
        allowClear: true,
        width: '100%',
        ajax: {
            url: '/api/products-with-search/search',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data) {
                // Make sure to handle the response structure correctly
                if (data.status_code === 200) {
                    return {
                        results: data.results.map(product => ({
                            id: product.id,
                            text: product.text || product.product_name
                        }))
                    };
                }
                return {
                    results: []
                };
            },
            cache: true
        },
        minimumInputLength: 1
    });

    $('#product_search').on('change', function () {
        const productId = $(this).val();
        if (!productId) return;

        // Gọi API lấy thông tin sản phẩm
        fetch(`/api/products-with-variants/${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status_code === 200) {
                    const product = data.data;

                    // Kiểm tra xem sản phẩm đã có trong danh sách chưa
                    const existingIndex = productsData.findIndex(p => p.product_id ==
                        productId);
                    if (existingIndex >= 0) {
                        alert('Sản phẩm này đã được thêm vào danh sách');
                        return;
                    }

                    // Thêm sản phẩm vào mảng productsData
                    const newProduct = {
                        product_id: product.id,
                        product_name: product.name,
                        product_image: product.image,
                        category_name: product.category?.name || 'N/A',
                        brand_name: product.brand || 'N/A',
                        all_product_variants: product['product-variant'] || product
                            .productVariant || [],
                        variants_in_slip: [],
                        new_colors: [],
                        new_price: '',
                        new_sizes_quantities: {}
                    };

                    productsData.push(newProduct);

                    // Render lại bảng
                    renderProductsTable();

                    // Reset select2
                    $('#product_search').val(null).trigger('change');
                }
            })
            .catch(error => {
                console.error('Error fetching product:', error);
                alert('Không thể lấy thông tin sản phẩm');
            });
    });


    function renderProductsTable() {
        const productsTbody = document.getElementById('products-tbody');
        productsTbody.innerHTML = '';

        productsData.forEach((productItem, index) => {
            const row = document.createElement('tr');
            row.id = `product-row-${index}`;
            row.innerHTML = `
                <td class="text-center">
                    <input type="checkbox" class="product-check form-check-input" data-index="${index}" checked>
                    <input type="hidden" name="products[${index}][product_id]" value="${productItem.product_id}">
                </td>
                <td>
                    <strong>${productItem.product_name}</strong>
                </td>
                <td>
                    <img src="${productItem.product_image}" class="product-img" alt="${productItem.product_name}">
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <small><strong>Danh mục:</strong> ${productItem.category_name}</small>
                        <small><strong>Thương hiệu:</strong> ${productItem.brand_name}</small>
                    </div>
                </td>
                <td>
                    <div class="variant-scroll" style="max-height: 150px; overflow-y: auto;">
                        <table class="variant-table table-sm">
                            <thead>
                                <tr>
                                    <th>Màu</th>
                                    <th>Size</th>
                                    <th>Stock hiện tại</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${productItem.all_product_variants.map(variant => `
                                    <tr>
                                        <td>${variant.color}</td>
                                        <td><span class="badge bg-secondary">${variant.size}</span></td>
                                        <td>${variant.stock || 0}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-link p-0 mt-2 btn-last-prices" data-index="${index}">
                        <i class="fas fa-search-dollar"></i> Xem giá nhập gần nhất
                    </button>
                </td>
                <td>
                    <div class="compact-form">
                        <div class="form-group mb-2">
                            <label class="form-label fw-bold small">Giá nhập:</label>
                            <input type="number" name="products[${index}][new_price]"
                                class="form-control form-control-sm new-price-input"
                                data-index="${index}"
                                placeholder="Giá nhập"
                                min="0">
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label fw-bold small">Màu mới:</label>
                            <button type="button" class="btn btn-sm btn-outline-primary w-100 btn-select-colors"
                                    data-index="${index}">
                                <i class="fas fa-palette me-1"></i> Chọn màu
                            </button>
                            <div class="selected-colors-display mt-1" data-index="${index}"></div>
                            <input type="hidden" name="products[${index}][new_colors]"
                                class="formatted-new-colors"
                                data-index="${index}">
                        </div>
                        <div class="form-group">
                            <label class="form-label fw-bold small">Size & SL:</label>
                            <select class="form-select form-select-sm select-sizes"
                                    name="products[${index}][new_sizes][]"
                                    multiple="multiple"
                                    data-index="${index}">
                                ${['XS', 'S', 'M', 'L', 'XL', 'XXL'].map(size =>
                `<option value="${size}">${size}</option>`
            ).join('')}
                            </select>
                            <input type="hidden" name="products[${index}][formatted_new_sizes]"
                                class="formatted-new-sizes"
                                data-index="${index}">
                        </div>
                    </div>
                </td>
            `;
            productsTbody.appendChild(row);
        });

        // Khởi tạo lại select2 cho các select size
        $('.select-sizes').select2({
            theme: "bootstrap-5",
            placeholder: "Chọn size",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#products_container'),
            templateSelection: formatSizeSelection,
            templateResult: formatSizeResult
        });

        // Bind lại event handlers cho các sản phẩm mới
        bindProductEventHandlers();

        // Gắn sự kiện cho nút xem giá nhập gần nhất
        $('.btn-last-prices').off('click').on('click', function () {
            const idx = $(this).data('index');
            showLastPricesModal(productsData[idx]);
        });
    }



    // --- Định nghĩa modal hiển thị giá nhập gần nhất ---
    function showLastPricesModal(productItem) {
        const variants = productItem.all_product_variants || [];

        // Gọi API lấy giá nhập gần nhất cho từng variant
        fetch(`/api/products/${productItem.product_id}/last-prices`)
            .then(response => response.json())
            .then(data => {
                let variantPrices = {};
                if (data.status_code === 200) {
                    data.variant_prices.forEach(v => {
                        // Nếu trả về theo product_variant_id thì có thể mapping theo color-size cũng được
                        variantPrices[`${v.color}-${v.size}`] = v.last_price;
                    });
                }

                let html = `
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Màu</th>
                                <th>Size</th>
                                <th>Giá nhập gần nhất</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${variants.map(variant => `
                                <tr>
                                    <td>${variant.color}</td>
                                    <td>${variant.size}</td>
                                    <td>${variantPrices[`${variant.color}-${variant.size}`] ? formatCurrency(variantPrices[`${variant.color}-${variant.size}`]) : '<span class="text-muted">---</span>'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;

                $('#lastPricesModalLabel').text(`Giá nhập gần nhất: ${productItem.product_name}`);
                $('#lastPricesModalBody').html(html);
                $('#lastPricesModal').modal('show');
            })
            .catch(error => {
                $('#lastPricesModalLabel').text('Lỗi');
                $('#lastPricesModalBody').html('<div class="text-danger">Không thể tải dữ liệu giá nhập gần nhất.</div>');
                $('#lastPricesModal').modal('show');
            });
    }

    // Hàm bind event handlers cho sản phẩm mới
    function bindProductEventHandlers() {
        // Color selection
        $('.btn-select-colors').off('click').on('click', function () {
            currentProductIndex = $(this).data('index');
            selectedColors = productsData[currentProductIndex].new_colors || [];
            updateSelectedColorsDisplay();
            $('#modal-colors').modal('show');
        });

        // Size selection
        $('.select-sizes').off('select2:select').on("select2:select", function (e) {
            const selectedSize = e.params.data.id;
            const productIndex = $(this).data('index');

            if (productsData[productIndex].new_sizes_quantities[selectedSize]) {
                alert(
                    `Size ${selectedSize} đã được chọn. Vui lòng chọn size khác hoặc cập nhật số lượng cho size này.`
                );
                $(this).val(Object.keys(productsData[productIndex].new_sizes_quantities)).trigger(
                    'change');
                return;
            }

            $('#modal-quantity-label').text(`Nhập số lượng cho size ${selectedSize}`);
            $("#quantity_variant").val(productsData[productIndex].new_sizes_quantities[
                selectedSize] || 1);

            $('#modal-quantity').data('product-index', productIndex);
            $('#modal-quantity').data('selected-size', selectedSize);
            $("#modal-quantity").modal("show");
        });

        $('.select-sizes').off('select2:unselect').on("select2:unselect", function (e) {
            const unselectedSize = e.params.data.id;
            const productIndex = $(this).data('index');
            delete productsData[productIndex].new_sizes_quantities[unselectedSize];
            updateProductSizesDisplay(productIndex);
        });

        // Product checkbox
        $('.product-check').off('change').on('change', function () {
            const index = this.dataset.index;
            const isChecked = this.checked;
            toggleProductRow(index, isChecked);

            if (!isChecked) {
                // Reset data when unchecking
                productsData[index].new_colors = [];
                productsData[index].new_price = '';
                productsData[index].new_sizes_quantities = {};

                // Update UI
                $(`#product-row-${index} .selected-colors-display`).empty();
                $(`#product-row-${index} .formatted-new-colors`).val('');
                $(`#product-row-${index} .new-price-input`).val('');
                $(`#product-row-${index} .select-sizes`).val(null).trigger('change');
                $(`#product-row-${index} .formatted-new-sizes`).val('');
            }
        });
    }


    // Biến toàn cục
    let allProductsData = []; // Lưu trữ tất cả sản phẩm từ API
    let displayedProductsCount = 5; // Số sản phẩm hiển thị ban đầu
    let currentSearchTerm = ''; // Từ khóa tìm kiếm hiện tại
    let currentStockFilter = 'all'; // Bộ lọc stock hiện tại

    // Khởi tạo modal khi nhấn nút "Load danh sách sản phẩm"
    document.getElementById('loadAllProductsBtn').addEventListener('click', function () {
        // Reset các giá trị khi mở modal
        displayedProductsCount = 5;
        currentSearchTerm = '';
        currentStockFilter = 'all';
        document.getElementById('productSearchInput').value = '';
        document.getElementById('stockFilterSelect').value = 'all';

        $('#allProductsModal').modal('show');
        loadAllProducts();
    });

    // Hàm tải tất cả sản phẩm từ API
    function loadAllProducts() {
        fetch('/api/products-with-variants')
            .then(response => response.json())
            .then(data => {
                if (data.status_code === 200) {
                    allProductsData = data.data;
                    renderFilteredProducts();
                } else {
                    alert('Không thể tải danh sách sản phẩm');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Lỗi khi tải danh sách sản phẩm');
            });
    }

    // Hàm render sản phẩm đã lọc
    function renderFilteredProducts() {
        // Lọc sản phẩm theo search term và stock
        let filteredProducts = allProductsData.filter(product => {
            // Lọc theo tìm kiếm
            const matchesSearch = currentSearchTerm === '' ||
                product.name.toLowerCase().includes(currentSearchTerm.toLowerCase()) ||
                (product.brand && product.brand.toLowerCase().includes(currentSearchTerm
                    .toLowerCase()));

            // Lọc theo stock
            let matchesStock = true;
            if (currentStockFilter !== 'all') {
                const hasStock = product['product-variant']?.some(variant => {
                    switch (currentStockFilter) {
                        case 'in_stock':
                            return variant.stock > 0;
                        case 'out_of_stock':
                            return variant.stock === 0;
                        case 'low_stock':
                            return variant.stock > 0 && variant.stock < 10;
                        default:
                            return true;
                    }
                });
                matchesStock = hasStock;
            }

            return matchesSearch && matchesStock;
        });

        // Hiển thị số lượng sản phẩm đã lọc
        updateProductCount(filteredProducts.length);

        // Render sản phẩm
        const tbody = document.getElementById('all-products-tbody');
        tbody.innerHTML = '';

        filteredProducts.slice(0, displayedProductsCount).forEach((product) => {
            const row = createProductRow(product);
            tbody.appendChild(row);
        });
    }

    // Hàm tạo HTML cho một hàng sản phẩm
    function createProductRow(product) {
        const row = document.createElement('tr');

        // Kiểm tra xem sản phẩm đã có trong danh sách chính chưa
        const isAlreadyAdded = productsData.some(p => p.product_id == product.id);

        row.innerHTML = `
        <td class="text-center">
            <input type="checkbox" class="product-select-checkbox"
                   data-product-id="${product.id}"
                   ${isAlreadyAdded ? 'disabled' : ''}>
            ${isAlreadyAdded ? '<i class="fas fa-check text-success ms-1"></i>' : ''}
        </td>
        <td>
            <strong>${product.name}</strong>
        </td>
        <td>
            <img src="${product.image || '/images/default-product.png'}"
                 class="product-img" alt="${product.name}"
                 onerror="this.src='/images/default-product.png'">
        </td>
        <td>
            <div class="d-flex flex-column">
                <small><strong>Danh mục:</strong> ${product.category?.name || 'N/A'}</small>
                <small><strong>Thương hiệu:</strong> ${product.brand || 'N/A'}</small>
            </div>
        </td>
        <td>
            <div class="variant-scroll" style="max-height: 150px; overflow-y: auto;">
                <table class="variant-table table-sm">
                    <thead>
                        <tr>
                            <th>Màu</th>
                            <th>Size</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${renderProductVariants(product['product-variant'])}
                    </tbody>
                </table>
            </div>
        </td>
    `;
        return row;
    }

    // Hàm render các biến thể của sản phẩm
    function renderProductVariants(variants) {
        if (!variants || variants.length === 0) {
            return '<tr><td colspan="3" class="text-center">Không có biến thể</td></tr>';
        }

        let html = '';
        const variantsToShow = variants.slice(0, 10);

        variantsToShow.forEach(variant => {
            const stockClass = variant.stock <= 0 ? 'text-danger' : variant.stock < 10 ?
                'text-warning' : 'text-success';
            html += `
            <tr>
                <td>${variant.color || 'N/A'}</td>
                <td><span class="badge bg-secondary">${variant.size || 'N/A'}</span></td>
                <td class="${stockClass}">${variant.stock || 0}</td>
            </tr>
        `;
        });

        if (variants.length > 10) {
            html += `
            <tr>
                <td colspan="3" class="text-center">
                    <small>+ ${variants.length - 10} biến thể khác</small>
                </td>
            </tr>
        `;
        }

        return html;
    }

    // Hàm cập nhật số lượng sản phẩm hiển thị
    function updateProductCount(totalProducts) {
        const showingCount = Math.min(displayedProductsCount, totalProducts);
        const countText = document.getElementById('productCountText');
        const loadMoreBtn = document.getElementById('loadMoreProductsBtn');

        countText.textContent = `Hiển thị ${showingCount}/${totalProducts} sản phẩm`;

        if (displayedProductsCount >= totalProducts) {
            loadMoreBtn.style.display = 'none';
            countText.innerHTML += ' <span class="text-muted">(đã hiển thị tất cả)</span>';
        } else {
            loadMoreBtn.style.display = 'block';
        }
    }

    // Xử lý tìm kiếm sản phẩm
    document.getElementById('searchProductBtn').addEventListener('click', function () {
        currentSearchTerm = document.getElementById('productSearchInput').value;
        displayedProductsCount = 5;
        renderFilteredProducts();
    });

    // Xử lý khi nhấn Enter trong ô tìm kiếm
    document.getElementById('productSearchInput').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            currentSearchTerm = this.value;
            displayedProductsCount = 5;
            renderFilteredProducts();
        }
    });

    // Xử lý lọc theo stock
    document.getElementById('applyFilterBtn').addEventListener('click', function () {
        currentStockFilter = document.getElementById('stockFilterSelect').value;
        displayedProductsCount = 5;
        renderFilteredProducts();
    });

    // Xử lý nút "Xem thêm"
    document.getElementById('loadMoreProductsBtn').addEventListener('click', function () {
        displayedProductsCount += 5;
        renderFilteredProducts();
    });

    // Xử lý thêm sản phẩm đã chọn vào danh sách chính
    document.getElementById('addSelectedProductsBtn').addEventListener('click', function () {
        const selectedCheckboxes = document.querySelectorAll(
            '#allProductsTable .product-select-checkbox:checked:not(:disabled)');

        if (selectedCheckboxes.length === 0) {
            alert('Vui lòng chọn ít nhất một sản phẩm chưa được thêm');
            return;
        }

        const productIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.dataset
            .productId);

        // Thêm các sản phẩm đã chọn vào danh sách chính
        productIds.forEach(productId => {
            const product = allProductsData.find(p => p.id == productId);
            if (product) {
                const existingIndex = productsData.findIndex(p => p.product_id == product
                    .id);
                if (existingIndex === -1) {
                    productsData.push({
                        product_id: product.id,
                        product_name: product.name,
                        product_image: product.image ||
                            '/images/default-product.png',
                        category_name: product.category?.name || 'N/A',
                        brand_name: product.brand || 'N/A',
                        all_product_variants: product['product-variant'] || [],
                        variants_in_slip: [],
                        new_colors: [],
                        new_price: '',
                        new_sizes_quantities: {}
                    });
                }
            }
        });

        // Render lại bảng sản phẩm chính
        renderProductsTable();

        // Đóng modal
        $('#allProductsModal').modal('hide');

        // Cuộn đến phần danh sách sản phẩm chính
        document.getElementById('products_container').scrollIntoView({
            behavior: 'smooth'
        });
    });


    // Xử lý khi nhấn nút thêm sản phẩm đã chọn
    document.getElementById('addSelectedProductsBtn').addEventListener('click', function () {
        const selectedCheckboxes = document.querySelectorAll(
            '#allProductsTable .product-select-checkbox:checked');

        if (selectedCheckboxes.length === 0) {
            alert('Vui lòng chọn ít nhất một sản phẩm');
            return;
        }

        const productIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.dataset
            .productId);

        // Gọi API để lấy thông tin chi tiết các sản phẩm đã chọn
        Promise.all(productIds.map(id => fetch(`/api/products-with-variants/${id}`).then(res => res
            .json())))
            .then(results => {
                results.forEach(result => {
                    if (result.status_code === 200) {
                        const product = result.data;

                        // Kiểm tra xem sản phẩm đã có trong danh sách chưa
                        const existingIndex = productsData.findIndex(p => p
                            .product_id == product
                                .id);
                        if (existingIndex >= 0) {
                            return; // Bỏ qua nếu đã tồn tại
                        }

                        // Thêm sản phẩm vào mảng productsData
                        productsData.push({
                            product_id: product.id,
                            product_name: product.name,
                            product_image: product.image,
                            category_name: product.category?.name || 'N/A',
                            brand_name: product.brand || 'N/A',
                            all_product_variants: product['product-variant'] ||
                                [],
                            variants_in_slip: [],
                            new_colors: [],
                            new_price: '',
                            new_sizes_quantities: {}
                        });
                    }
                });

                // Render lại bảng sản phẩm
                renderProductsTable();

                // Đóng modal
                $('#allProductsModal').modal('hide');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Lỗi khi thêm sản phẩm');
            });
    });
});


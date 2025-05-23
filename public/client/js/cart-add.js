let cartCount = 0;
let cart = {};

window.addEventListener("scroll", function () {
    let cartIcon = document.getElementById("cartIcon");
    cartIcon.style.display = "block";
});

document.body.addEventListener("click", function (event) {
    if (event.target.classList.contains("add-cart")) {
        let productId = event.target.getAttribute("data-id");
        addToCart(productId, event);
    }
});

// function addToCart(productId, event) {
//     fetch(`cart/add/${productId}/1`, {
//         method: "GET",
//         headers: { "X-Requested-With": "XMLHttpRequest" },
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success) {
//             document.getElementById("cartCount").innerText = data.cart_count;
//             animateToCart(event);
//             shakeCartIcon();

//             let cartList = document.getElementById("cartList");

//             document.querySelectorAll('.cart-quantity-header').forEach((element) => {
//                 element.textContent = data.cart_product_count;
//             });

//             // --- THÊM LOG ĐỂ KIỂM TRA ---
//             console.log("Dữ liệu data từ backend:", data);
//             console.log("productId:", productId);
//             console.log("data.color:", data.color); // Kiểm tra giá trị này
//             console.log("data.size:", data.size);   // Kiểm tra giá trị này

//             let itemKey = `${productId}-${data.color}-${data.size}`;
//             console.log("itemKey được tạo ở frontend:", itemKey); // So sánh với item.key từ backend
//             let item = data.cart.items[itemKey];

//             if (item) {
//                 console.log("Tìm thấy item trong data.cart.items:", item); // Đây là item chính xác từ backend
//                 console.log("item.key từ item backend:", item.key); // Key mà backend dùng

//                 if (item.quantity > item.stock) {
//                     showToast(`Không thể thêm số lượng sản phẩm vượt quá tồn kho! (Tồn kho: ${item.stock})`, "error");
//                     return;
//                 }

//                 // Tìm tất cả các item có cùng key trong DOM
//                 let existingItems = document.querySelectorAll(`[data-item-key="${itemKey}"]`);
//                 console.log("Số lượng existingItems trong DOM:", existingItems.length); // Rất quan trọng để xem có bao nhiêu phần tử trùng khớp

//                 if (existingItems.length > 0) {
//                     // Nếu đã tồn tại, chỉ cập nhật item đầu tiên
//                     let firstItem = existingItems[0];
//                     let quantityElement = firstItem.querySelector('.cart-item-quantity');
//                     quantityElement.textContent = item.quantity;

//                     // Xóa các item trùng lặp (nếu có)
//                     // Đây là lý do nó gộp lại khi refresh, vì bạn đang xóa các bản sao cũ
//                     if (existingItems.length > 1) {
//                         console.log("Đang xóa các item trùng lặp trong DOM...");
//                         for (let i = 1; i < existingItems.length; i++) {
//                             existingItems[i].remove();
//                         }
//                     }

//                     showToast("Cập nhật giỏ hàng thành công!", "success");
//                 } else {
//                     // Nếu chưa tồn tại, thêm mới
//                     console.log("Chưa tìm thấy item trong DOM, thêm mới...");
//                     let cartItem = document.createElement("div");
//                     cartItem.classList.add("cart-item", "p-1", "opacity-0");
//                     cartItem.setAttribute('data-item-key', itemKey); // Đảm bảo key này đúng

//                     let priceProduct = formatNumber(item.price);

//                     cartItem.innerHTML = `
//                         <img src="${item.image}" alt="${item.name}" class="cart-item-image">
//                         <div class="cart-item-info ms-2">
//                             <div class="cart-item-name">${words(item.name, 5)}</div>
//                             <div class="cart-item-price">${priceProduct} đ</div>
//                         </div>
//                         <span class="cart-item-quantity">${item.quantity}</span>
//                     `;

//                     cartList.appendChild(cartItem);

//                     // Ẩn empty-cart nếu đang hiển thị
//                     const emptyCart = document.querySelector('.empty-cart');
//                     if (emptyCart) {
//                         emptyCart.style.display = 'none';
//                     }

//                     // Trigger reflow để transition hoạt động
//                     cartItem.offsetHeight;
//                     cartItem.classList.remove("opacity-0");
//                     showToast("Thêm vào giỏ hàng thành công!", "success");
//                 }
//             } else {
//                 console.error("Lỗi: Không tìm thấy item trong data.cart.items với itemKey:", itemKey);
//                 showToast("Thêm vào giỏ hàng thất bại do lỗi dữ liệu!", "error");
//             }
//         } else {
//             showToast(data.message || "Thêm vào giỏ hàng thất bại!", "error");
//         }
//     })
//     .catch(error => {
//         console.error("Lỗi fetch:", error);
//         showToast("Có lỗi xảy ra khi kết nối đến máy chủ!", "error");
//     });
// }



// function addToCart(productId, event) {
//     fetch(`cart/add/${productId}/1`, {
//         method: "GET",
//         headers: { "X-Requested-With": "XMLHttpRequest" },
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success) {
//             document.getElementById("cartCount").innerText = data.cart_count;
//             animateToCart(event);
//             shakeCartIcon();

//             let cartList = document.getElementById("cartList");

//             document.querySelectorAll('.cart-quantity-header').forEach((element) => {
//                 element.textContent = data.cart_product_count;
//             });

//             let itemKey = `${productId}-${data.color}-${data.size}`;
//             let item = data.cart.items[itemKey];

//             if (item) {
//                 console.log(item);
//                 if (item.quantity > item.stock) {
//                     showToast(`Không thể thêm số lượng sản phẩm vượt quá tồn kho! (Tồn kho: ${item.stock})`, "error");
//                     return;
//                 }

//                 // Tìm tất cả các item có cùng key
//                 let existingItems = document.querySelectorAll(`[data-item-key="${itemKey}"]`);

//                 if (existingItems.length > 0) {
//                     // Nếu đã tồn tại, chỉ cập nhật item đầu tiên
//                     let firstItem = existingItems[0];
//                     let quantityElement = firstItem.querySelector('.cart-item-quantity');
//                     quantityElement.textContent = item.quantity;

//                     // Xóa các item trùng lặp (nếu có)
//                     if (existingItems.length > 1) {
//                         for (let i = 1; i < existingItems.length; i++) {
//                             existingItems[i].remove();
//                         }
//                     }

//                     showToast("Cập nhật giỏ hàng thành công!", "success");
//                 } else {
//                     // Nếu chưa tồn tại, thêm mới
//                     let cartItem = document.createElement("div");
//                     cartItem.classList.add("cart-item", "p-1", "opacity-0");
//                     cartItem.setAttribute('data-item-key', itemKey);

//                     let priceProduct = formatNumber(item.price);

//                     cartItem.innerHTML = `
//                         <img src="${item.image}" alt="${item.name}" class="cart-item-image">
//                         <div class="cart-item-info ms-2">
//                             <div class="cart-item-name">${words(item.name, 5)}</div>
//                             <div class="cart-item-price">${priceProduct} đ</div>
//                         </div>
//                         <span class="cart-item-quantity">${item.quantity}</span>
//                     `;

//                     cartList.appendChild(cartItem);

//                     // Ẩn empty-cart nếu đang hiển thị
//                     const emptyCart = document.querySelector('.empty-cart');
//                     if (emptyCart) {
//                         emptyCart.style.display = 'none';
//                     }

//                     // Trigger reflow để transition hoạt động
//                     cartItem.offsetHeight;
//                     cartItem.classList.remove("opacity-0");
//                     showToast("Thêm vào giỏ hàng thành công!", "success");
//                 }
//             }
//         } else {
//             showToast(data.message || "Thêm vào giỏ hàng thất bại!", "error");
//         }
//     })
//     .catch(error => console.error("Lỗi fetch:", error));
// }

// function addToCart(productId, event) {
//     fetch(`cart/add/${productId}/1`, {
//         method: "GET",
//         headers: { "X-Requested-With": "XMLHttpRequest" },
//     })
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 document.getElementById("cartCount").innerText = data.cart_count;
//                 animateToCart(event);
//                 shakeCartIcon();

//                 let cartList = document.getElementById("cartList");

//                 document.querySelectorAll('.cart-quantity-header').forEach((element) => {
//                     element.textContent = data.cart_product_count;
//                 });

//                 let item = data.cart.items[`${productId}-${data.color}-${data.size}`];
//                 if (item) {
//                     if (item.quantity >= item.stock) {
//                         //     alert(`Không thể thêm số lượng sản phẩm vào giỏ hàng vượt quá số lượng trong kho! (${item.stock})`);
//                         //     return;
//                         showToast(`Không thể thêm số lượng sản phẩm vượt quá tồn kho! (Tồn kho: ${item.stock})`, "error");
//                         return;
//                     }
//                     let oldValue = document.querySelector(`.cart-item-quantity-${item.id}`);
//                     if (oldValue === null) {
//                         let cartItem = document.createElement("div");
//                         cartItem.classList.add("cart-item", "p-1", "opacity-0");

//                         let priceProduct = formatNumber(item.price);

//                         cartItem.innerHTML = `
//                                     <img src="${item.image}" alt="${item.name}" class="cart-item-image">
//                                     <div class="cart-item-info ms-2">
//                                         <div class="cart-item-name">${words(item.name, 5)}</div>
//                                         <div class="cart-item-price">${priceProduct} đ</div>
//                                     </div>
//                                     <span class="cart-item-quantity-${item.id} cart-item-quantity">${item.quantity}</span>
//                                 `;

//                         cartList.appendChild(cartItem);

//                         // Ẩn empty-cart nếu đang hiển thị
//                         const emptyCart = document.querySelector('.empty-cart');
//                         if (emptyCart) {
//                             emptyCart.style.display = 'none';
//                         }

//                         // Trigger reflow để transition hoạt động
//                         cartItem.offsetHeight;
//                         cartItem.classList.remove("opacity-0");
//                         showToast("Thêm vào giỏ hàng thành công!", "success");
//                     }

//                     else {
//                         oldValue.textContent = parseInt(oldValue.textContent) + 1;
//                     }
//                 }
//             } else {
//                 showToast(data.message || "Thêm vào giỏ hàng thất bại!", "error");
//             }
//         })
//         .catch(error => console.error("Lỗi fetch:", error));
// }



// function addToCart(productId, event) {
//     // Gửi yêu cầu thêm sản phẩm vào giỏ hàng
//     fetch(`cart/add/${productId}/1`, {
//         method: "GET", // Hoặc "POST" nếu bạn muốn gửi size/color qua body cho chi tiết sản phẩm
//         headers: { "X-Requested-With": "XMLHttpRequest" },
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success) {
//             // Cập nhật tổng số lượng sản phẩm trong giỏ hàng (icon giỏ hàng)
//             document.getElementById("cartCount").innerText = data.cart_count;
//             animateToCart(event); // Hàm animation (nếu có)
//             shakeCartIcon(); // Hàm lắc icon giỏ hàng (nếu có)

//             let cartList = document.getElementById("cartList"); // Phần tử chứa danh sách các sản phẩm trong giỏ hàng mini

//             // Cập nhật số lượng loại sản phẩm khác nhau trong giỏ hàng (ví dụ: "3 sản phẩm")
//             document.querySelectorAll('.cart-quantity-header').forEach((element) => {
//                 element.textContent = data.cart_product_count;
//             });

//             // Lấy thông tin màu và size của biến thể sản phẩm vừa được thêm
//             const newlyAddedColor = data.color;
//             const newlyAddedSize = data.size;

//             // Tạo một khóa định danh duy nhất cho biến thể này, giống với cách Backend sử dụng
//             const newlyAddedVariantKey = `${productId}-${newlyAddedColor}-${newlyAddedSize}`;
//             let item = data.cart.items[newlyAddedVariantKey]; // Lấy thông tin chi tiết của item vừa thêm từ dữ liệu trả về

//             if (item) {
//                 // Kiểm tra tồn kho (nếu backend chưa chặn hoàn toàn ở trường hợp này)
//                 if (item.quantity > item.stock) {
//                     showToast(`Không thể thêm số lượng sản phẩm vượt quá tồn kho! (Tồn kho: ${item.stock})`, "error");
//                     return;
//                 }

//                 // Tạo một ID DOM "sạch" để dùng làm class hoặc ID cho phần tử HTML
//                 // Thay thế khoảng trắng bằng gạch dưới để tránh lỗi trong tên class/id
//                 const uniqueDomId = newlyAddedVariantKey.replace(/ /g, '_');

//                 // Tìm phần tử hiển thị số lượng của item này trong giỏ hàng mini
//                 let existingQuantityElement = document.querySelector(`.cart-item-quantity-${uniqueDomId}`);

//                 // Tìm toàn bộ phần tử (div) chứa item đó trong giỏ hàng mini
//                 let existingCartItemElement = document.querySelector(`.cart-item-${uniqueDomId}`);


//                 if (existingCartItemElement === null) {
//                     // Nếu item (với biến thể cụ thể này) CHƯA CÓ trong danh sách hiển thị
//                     let cartItem = document.createElement("div");
//                     // Thêm các class cần thiết, bao gồm class định danh duy nhất
//                     cartItem.classList.add("cart-item", "p-1", "opacity-0", `cart-item-${uniqueDomId}`);

//                     let priceProduct = formatNumber(item.price); // Định dạng giá sản phẩm

//                     // Tạo cấu trúc HTML cho item mới trong giỏ hàng mini
//                     cartItem.innerHTML = `
//                         <img src="${item.image}" alt="${item.name}" class="cart-item-image">
//                         <div class="cart-item-info ms-2">
//                             <div class="cart-item-name">${words(item.name, 5)}</div>
//                             <div class="cart-item-price">${priceProduct} đ</div>
//                             <div class="cart-item-variants">Màu: ${newlyAddedColor}, Size: ${newlyAddedSize}</div>
//                         </div>
//                         <span class="cart-item-quantity-${uniqueDomId} cart-item-quantity">${item.quantity}</span>
//                     `;

//                     cartList.appendChild(cartItem); // Thêm item mới vào danh sách

//                     // Ẩn thông báo "Giỏ hàng trống" nếu đang hiển thị
//                     const emptyCart = document.querySelector('.empty-cart');
//                     if (emptyCart) {
//                         emptyCart.style.display = 'none';
//                     }

//                     // Kích hoạt transition để item xuất hiện mượt mà
//                     cartItem.offsetHeight; // Kích hoạt reflow
//                     cartItem.classList.remove("opacity-0"); // Bỏ class opacity để item hiện ra

//                     showToast("Thêm vào giỏ hàng thành công!", "success"); // Hiển thị thông báo thành công
//                 } else {
//                     // Nếu item (với biến thể cụ thể này) ĐÃ CÓ trong danh sách hiển thị
//                     if (existingQuantityElement) {
//                         // Cập nhật số lượng của item đó bằng số lượng mới nhất từ backend
//                         existingQuantityElement.textContent = item.quantity;
//                         showToast("Đã cập nhật số lượng trong giỏ hàng!", "success");
//                     }
//                 }
//             }
//         } else {
//             // Xử lý khi thêm sản phẩm thất bại
//             showToast(data.message || "Thêm vào giỏ hàng thất bại!", "error");
//         }
//     })
//     .catch(error => console.error("Lỗi fetch:", error)); // Bắt lỗi trong quá trình fetch
// }



function addToCart(productId, event) {
    fetch(`cart/add/${productId}/1`, {
        method: "GET",
        headers: { "X-Requested-With": "XMLHttpRequest" },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cập nhật số lượng trong badge
            document.getElementById("cartCount").innerText = data.cart_count;
            animateToCart(event);
            shakeCartIcon();

            let cartList = document.getElementById("cartList");

            // Cập nhật header count
            document.querySelectorAll('.cart-quantity-header').forEach((element) => {
                element.textContent = data.cart_product_count;
            });

            // Debug logs
            // console.log("Dữ liệu data từ backend:", data);
            // console.log("productId:", productId);

            // Tạo itemKey - đảm bảo format nhất quán
            let itemKey = `${productId}-${data.color || 'default'}-${data.size || 'default'}`;
            // console.log("itemKey được tạo:", itemKey);

            // Tìm item trong response từ backend
            let item = data.cart.items[itemKey];

            if (item) {
                // console.log("Tìm thấy item:", item);

                // Kiểm tra stock
                if (item.quantity > item.stock) {
                    showToast(`Không thể thêm số lượng sản phẩm vượt quá tồn kho! (Tồn kho: ${item.stock})`, "error");
                    return;
                }

                // GIẢI PHÁP: Rebuild toàn bộ cart thay vì update từng phần
                rebuildCartDisplay(data.cart.items);
                showToast("Cập nhật giỏ hàng thành công!", "success");

            } else {
                // console.error("Không tìm thấy item với key:", itemKey);
                // console.log("Các keys có sẵn:", Object.keys(data.cart.items));
                showToast("Thêm vào giỏ hàng thất bại do lỗi dữ liệu!", "error");
            }
        } else {
            showToast(data.message || "Thêm vào giỏ hàng thất bại!", "error");
        }
    })
    .catch(error => {
        console.error("Lỗi fetch:", error);
        showToast("Có lỗi xảy ra khi kết nối đến máy chủ!", "error");
    });
}

// Hàm mới để rebuild toàn bộ giỏ hàng
function rebuildCartDisplay(cartItems) {
    let cartList = document.getElementById("cartList");

    // Xóa tất cả items hiện tại (trừ empty-cart message)
    let existingItems = cartList.querySelectorAll('.cart-item');
    existingItems.forEach(item => item.remove());

    // Kiểm tra nếu giỏ hàng trống
    if (!cartItems || Object.keys(cartItems).length === 0) {
        let emptyCart = cartList.querySelector('.empty-cart');
        if (!emptyCart) {
            cartList.innerHTML = '<p class="empty-cart">Chưa có sản phẩm nào.</p>';
        } else {
            emptyCart.style.display = 'block';
        }
        return;
    }

    // Ẩn empty-cart message
    let emptyCart = cartList.querySelector('.empty-cart');
    if (emptyCart) {
        emptyCart.style.display = 'none';
    }

    // Thêm lại tất cả items từ dữ liệu backend
    Object.keys(cartItems).forEach(key => {
        let item = cartItems[key];

        let cartItem = document.createElement("div");
        cartItem.classList.add("cart-item", "d-flex", "align-items-center", "p-1");
        cartItem.setAttribute('data-item-key', key);

        let priceProduct = formatNumber(item.price);

        cartItem.innerHTML = `
            <img src="${item.image}" alt="${item.name}" class="cart-item-image rounded">
            <div class="cart-item-info flex-grow-1 ms-2">
                <div class="cart-item-name text-truncate">${words(item.name, 5)}</div>
                <div class="cart-item-price text-muted">${priceProduct} đ</div>
            </div>
            <span class="cart-item-quantity badge bg-danger ms-2">${item.quantity}</span>
        `;

        cartList.appendChild(cartItem);
    });
}


function showToast(message, type = "error", duration = 3000) {
    const container = document.getElementById("toast-container");
    if (!container) return;

    const toast = document.createElement("div");
    toast.classList.add("toast", type);
    toast.textContent = message;

    // Khi click thì ẩn luôn
    toast.onclick = () => {
        toast.classList.remove("show");
        setTimeout(() => toast.remove(), 400);
    };

    container.appendChild(toast);

    // Hiện toast
    setTimeout(() => {
        toast.classList.add("show");
    }, 100);

    // Tự ẩn sau duration
    setTimeout(() => {
        toast.classList.remove("show");
        setTimeout(() => toast.remove(), 400);
    }, duration);
}




function formatNumber(price) {
    return new Intl.NumberFormat('vi-VN').format(price);
}

function words(str, limit = 10, end = "...") {
    let wordsArray = str.split(/\s+/); // Tách chuỗi thành mảng từ
    if (wordsArray.length > limit) {
        return wordsArray.slice(0, limit).join(" ") + end;
    }
    return str;
}

function toggleCart() {
    let cartItems = document.getElementById("cartItems");
    cartItems.style.display = cartItems.style.display === "none" ? "block" : "none";
    // loadCartItems(); //Cập nhật giỏ hàng khi mở
}


function animateToCart(event) {
    let cartIcon = document.getElementById("cartIcon");
    let productElement = event.target.closest(".product__item").querySelector(".set-bg");
    // console.log(productElement);
    // let imageUrl = productElement.getAttribute("src");
    let imageUrl = productElement.getAttribute("src") || productElement.style.backgroundImage.replace(/url\(["']?(.*?)["']?\)/, '$1');
    let flyingImg = document.createElement("img");
    flyingImg.src = imageUrl;
    flyingImg.classList.add("fly-to-cart");
    document.body.appendChild(flyingImg);
    // console.log(productElement, imageUrl, flyingImg);


    let productRect = event.target.getBoundingClientRect();
    let cartRect = cartIcon.getBoundingClientRect();

    flyingImg.style.left = `${productRect.left + window.scrollX}px`;
    flyingImg.style.top = `${productRect.top + window.scrollY}px`;

    setTimeout(() => {
        flyingImg.style.transform = `
        translate(${cartRect.left - productRect.left}px, ${cartRect.top - productRect.top}px)
        scale(0.2) rotate(720deg)
    `;
        flyingImg.style.opacity = "0";
        flyingImg.style.filter = "blur(2px)";
    }, 100);

    setTimeout(() => {
        flyingImg.remove();
        cartIcon.classList.add("shake");
        setTimeout(() => cartIcon.classList.remove("shake"), 500);
    }, 800);
}

function shakeCartIcon() {
    let cartIcon = document.getElementById("cartIcon");
    cartIcon.classList.add("shake");
    setTimeout(() => cartIcon.classList.remove("shake"), 500);
}

function goToCartPage() {
    window.location.href = "/cart";
}

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

                // Tạo itemKey - đảm bảo format nhất quán formattedColor
                var normalizedColor = data.color.replace(' ', '');
                let itemKey = `${productId}-${normalizedColor || 'default'}-${data.size || 'default'}`;
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
        // console.log(item);
        cartItem.innerHTML = `
            <img src="${item.image}" alt="${item.name}" class="cart-item-image rounded">
            <div class="cart-item-info flex-grow-1 ms-2">
                <div class="cart-item-name text-truncate">Tên: ${words(item.name, 5)}</div>
                <div class="cart-item-color">Size-Màu: ${item.color} - ${item.size}</div>
                <div class="cart-item-price text-muted">Giá: ${priceProduct} đ</div>
            </div>
            <span class="cart-item-quantity badge bg-danger ms-2">Qty: ${item.quantity}</span>
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

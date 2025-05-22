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
                document.getElementById("cartCount").innerText = data.cart_count;
                animateToCart(event);
                shakeCartIcon();

                let cartList = document.getElementById("cartList");

                document.querySelectorAll('.cart-quantity-header').forEach((element) => {
                    element.textContent = data.cart_product_count;
                });

                let item = data.cart.items[`${productId}-${data.color}-${data.size}`];
                if (item) {
                    if (item.quantity >= item.stock) {
                        //     alert(`Không thể thêm số lượng sản phẩm vào giỏ hàng vượt quá số lượng trong kho! (${item.stock})`);
                        //     return;
                        showToast(`Không thể thêm số lượng sản phẩm vượt quá tồn kho! (Tồn kho: ${item.stock})`, "error");
                        return;
                    }
                    let oldValue = document.querySelector(`.cart-item-quantity-${item.id}`);
                    if (oldValue === null) {
                        let cartItem = document.createElement("div");
                        cartItem.classList.add("cart-item", "p-1", "opacity-0");

                        let priceProduct = formatNumber(item.price);

                        cartItem.innerHTML = `
                                    <img src="uploads/${item.image}" alt="${item.name}" class="cart-item-image">
                                    <div class="cart-item-info ms-2">
                                        <div class="cart-item-name">${words(item.name, 5)}</div>
                                        <div class="cart-item-price">${priceProduct} đ</div>
                                    </div>
                                    <span class="cart-item-quantity-${item.id} cart-item-quantity">${item.quantity}</span>
                                `;

                        cartList.appendChild(cartItem);

                        // Ẩn empty-cart nếu đang hiển thị
                        const emptyCart = document.querySelector('.empty-cart');
                        if (emptyCart) {
                            emptyCart.style.display = 'none';
                        }

                        // Trigger reflow để transition hoạt động
                        cartItem.offsetHeight;
                        cartItem.classList.remove("opacity-0");
                        showToast("Thêm vào giỏ hàng thành công!", "success");
                    }

                    else {
                        oldValue.textContent = parseInt(oldValue.textContent) + 1;
                    }
                }
            } else {
                showToast(data.message || "Thêm vào giỏ hàng thất bại!", "error");
            }
        })
        .catch(error => console.error("Lỗi fetch:", error));
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

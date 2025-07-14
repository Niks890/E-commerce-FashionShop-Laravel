document.addEventListener('DOMContentLoaded', function() {
    // Gọi hàm bind filter
    setupShopFilters();
});

function setupShopFilters() {
    // Lắng nghe click các filter, brand, price, tag, color, size, promotion, sort
    document.querySelectorAll('.category__item, .brand__item, .price__item, .tag-item, .color-item, .promotion-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            let params = new URLSearchParams(window.location.search);
            if (this.classList.contains('category__item')) params.set('category', this.dataset.category);
            if (this.classList.contains('brand__item')) params.set('brand', this.dataset.brand);
            if (this.classList.contains('price__item')) params.set('price', this.dataset.price);
            if (this.classList.contains('tag-item')) params.set('tag', this.textContent.trim().replace(' ', '-'));
            if (this.classList.contains('color-item')) params.set('color', this.title);
            if (this.classList.contains('promotion-item')) params.set('promotion', 1);
            params.set('page', 1);
            fetchShopProducts(params);
        });
    });

    // Lắng nghe click phân trang (pagination)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('page-link-ajax')) {
            e.preventDefault();
            let url = new URL(e.target.href, window.location.origin);
            let params = new URLSearchParams(url.search);
            fetchShopProducts(params);
        }
    });

    // Lắng nghe sort
    let sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            let params = new URLSearchParams(window.location.search);
            params.set('sort_by', this.value);
            params.set('page', 1);
            fetchShopProducts(params);
        });
    }
}

function fetchShopProducts(params) {
    let url = '/shop/filter?' + params.toString();
    // Hiển thị spinner nếu muốn
    document.getElementById('product-list').innerHTML = '<div class="text-center my-5"><span class="spinner-border"></span></div>';
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
        .then(res => res.text())
        .then(html => {
            document.getElementById('product-list').innerHTML = html;
            window.history.pushState({}, '', '/shop?' + params.toString());
            // Re-bind filter nếu cần (nếu generate lại phần filter bên trong ajax)
        });
}

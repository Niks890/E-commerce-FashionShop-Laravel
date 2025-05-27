<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatBotApiController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\OllamaController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\RevenueInventoryController;
use App\Http\Controllers\RevenueProductController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\WishListProductController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/* TRANG CLIENT */

Route::group(['prefix' => '/'], function () {
    Route::get('/', [HomeController::class, 'home'])->name('sites.home');

    // Xử lý đăng nhập user
    Route::group(['prefix' => 'user'], function () {
        Route::get('/login', [CustomerController::class, 'login'])->name('user.login');
        Route::post('/login', [CustomerController::class, 'post_login'])->name('user.post_login');
        Route::get('/logout', [CustomerController::class, 'logout'])->name('user.logout')->middleware('auth:customer');
        Route::get('/register', [CustomerController::class, 'register'])->name('user.register');
        Route::post('/register', [CustomerController::class, 'post_register'])->name('user.post_register');
        Route::get('/profile', [CustomerController::class, 'profile'])->name('user.profile')->middleware('auth:customer');
        Route::put('/profile/{customer}/update', [CustomerController::class, 'update_profile'])->name('user.update_profile');
        Route::post('/check-login', [CustomerController::class, 'checkLogin'])->name('user.checkLogin');
    });

    Route::get('/shop', [HomeController::class, 'shop'])->name('sites.shop');
    Route::post('/shop', [HomeController::class, 'shop'])->name('sites.shopSearch');
    Route::get('/cart', [HomeController::class, 'cart'])->name('sites.cart');
    Route::get('/aboutUs', [HomeController::class, 'aboutUs'])->name('sites.aboutUs');
    Route::get('/blog-detail/{slug}', [HomeController::class, 'blogDetail'])->name('sites.blogDetail');

    // xử lý gửi email
    Route::get('/contact', [HomeController::class, 'contact'])->name('sites.contact');
    // Route::get('/contact-success', [ContactController::class, 'contactSuccess'])->name('contact.contactSuccess');
    // Route::get('/test-mail', function () {
    //     $data = ['name' => 'Test User', 'email' => 'minhminh@gmail.com', 'message' => 'This is a test message'];

    //     Mail::raw("Tên người gửi: {$data['name']}\nĐịa chỉ Email: {$data['email']}\nNội dung: {$data['message']}", function($message) use ($data) {
    //         $message->from('raucuquasachnhom@gmail.com', $data['name'])
    //                 ->to('raucuquasachnhom@gmail.com', 'TST Fashion Shop')
    //                 ->subject('Test Email');
    //     });

    //     return 'Gửi email thành công!';
    // });


    Route::post('/contact/send', [ContactController::class, 'sendContact'])->name('contact.send');
    Route::get('/blog', [HomeController::class, 'blog'])->name('sites.blog');
    Route::get('/product/{slug}', [HomeController::class, 'productDetail'])->name('sites.productDetail');


    // Xử lý danh sách yêu thích
    Route::get('/wishlist', [WishListProductController::class, 'index'])->name('sites.wishlist');
    Route::get('/add-to-wishlist/{product}', [WishListProductController::class, 'addToWishList'])->name('sites.addToWishList');
    Route::get('/remove-from-wishlist/{id}', [WishListProductController::class, 'removefromWishList'])->name('sites.removefromWishList');
    // Xử lý chatbot
    // Route::post('/chatbot', [OllamaController::class, 'ollamaRequest']);
    // Xử lý chatbot
    // Route::post('/get-product-info/webhook', [DialogflowController::class, 'getProductInfo'])->name('dialogflow.getProductInfo');


    // Xử lý đơn hàng
    Route::get('/order-history', [CustomerController::class, 'getHistoryOrderOfCustomer'])->name('sites.getHistoryOrder')->middleware('customer');
    Route::get('/order-detail/{order}', [CustomerController::class, 'showOrderDetailOfCustomer'])->name('sites.showOrderDetailOfCustomer')->middleware('customer');
    Route::put('/cancel-order{id}', [CustomerController::class, 'cancelOrder'])->name('sites.cancelOrder')->middleware('customer');

    // Xuất hoá đơn PDF
    Route::get('/order/{id}/invoice', [OrderController::class, 'exportInvoice'])->name('order.invoice');
    Route::get('/order-tracking/{id}', [OrderController::class, 'orderTracking'])->name('order.orderTracking');
    // Xử lý thanh toán
    // Route::get('/checkout', [HomeController::class, 'checkout'])->name('sites.checkout')->middleware('auth:customer');
    Route::post('/payment', [CheckoutController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/success', [HomeController::class, 'successPayment'])->name('sites.success.payment')->middleware('customer');
    // Routes xử lý callback từ các cổng thanh toán
    Route::get('/vnpay-return', [CheckoutController::class, 'vnpayReturn'])->name('payment.vnpay.return');
    Route::get('/momo-return', [CheckoutController::class, 'momoReturn'])->name('payment.momo.return');
    Route::get('/zalopay-return', [CheckoutController::class, 'zalopayReturn'])->name('payment.zalopay.return');

    // xử lý đánh giá đơn hàng
    Route::resources(
        [
            'comments' => RateController::class,
        ]
    );
});

// Xử lý cart
Route::group(['prefix' => '/cart'], function () {
    Route::get('/', [CartController::class, 'cart'])->name('sites.cart');
    Route::get('/add/{product?}/{quantity?}', [CartController::class, 'add'])->name('sites.add');
    Route::post('/add/{product?}/{quantity?}', [CartController::class, 'add'])->name('sites.addFromDetail');
    Route::get('/update/{id}/{quantity?}', [CartController::class, 'update'])->name('sites.update');
    Route::get('/remove/{key}', [CartController::class, 'remove'])->name('sites.remove'); // xoá theo key (example: 1-XS-Đen) chứ ko theo id nữa
    Route::get('/clear', [CartController::class, 'clear'])->name('sites.clear');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('sites.checkout')->middleware('auth:customer');
    Route::post('/update-cart-session', [CartController::class, 'updateCartSession'])->name('sites.updateCartSession');
    // Xử lý cập nhật sản phẩm đc chọn trong giỏ hàng
    Route::post('/update-check-status', [CartController::class, 'updateCheckStatus'])->name('sites.updateCheckStatus');


    Route::post('/create-percent-discount-session', [CartController::class, 'createPercentDiscountSession'])->name('sites.createPercentDiscountSession');
    Route::resources(
        [
            'order' => OrderController::class,
        ]
    );
});

// Xử lý đăng nhập gg
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

// Xử lý đăng nhập fb
// Route::get('/auth/facebook', [FacebookAuthController::class, 'redirectToFacebook'])->name('auth.facebook');
// Route::get('/auth/facebook/callback', [FacebookAuthController::class, 'handleFacebookCallback']);


/* TRANG ADMIN */
Route::get('/admin-login', [AdminController::class, 'login'])->name('admin.login');
Route::post('/post-login', [AdminController::class, 'post_login'])->name('admin.post_login');
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::resources(
        [
            'category' => CategoryController::class,
            'discount' => DiscountController::class,
            'product' => ProductController::class,
            'provider' => ProviderController::class,
            'inventory' => InventoryController::class,
            'staff' => StaffController::class,
            'blog' => BlogController::class,
            'voucher' => VoucherController::class,
            'customer' => CustomerController::class,
            'comment' => CommentController::class
        ]
    );
    Route::get('/categories/{category}/products-modal', [CategoryController::class, 'showProductsInModal'])->name('category.products.modal');
    Route::post('/customers/send-voucher', [CustomerController::class, 'sendVoucher'])->name('customer.sendVoucher');
    Route::put('/staff/{staff}/update', [StaffController::class, 'update_staff'])->name('staff.update_staff');
    Route::get('/add_extra', [InventoryController::class, 'add_extra'])->name('inventory.add_extra');
    Route::post('/post_add_extra', [InventoryController::class, 'post_add_extra'])->name('inventory.post_add_extra');
    Route::get('/search_category', [CategoryController::class, 'search'])->name('category.search');
    Route::get('/search_discount', [DiscountController::class, 'search'])->name('discount.search');
    Route::get('/search_product', [ProductController::class, 'search'])->name('product.search');
    Route::get('/search_provider', [ProviderController::class, 'search'])->name('provider.search');
    Route::get('/search_staff', [StaffController::class, 'search'])->name('staff.search');
    Route::get('/search_inventory', [InventoryController::class, 'search'])->name('inventory.search');
    // Route::get('/advanced-search', [InventoryController::class, 'advancedSearch'])->name('advanced_search');
    Route::get('/search_order', [OrderController::class, 'search'])->name('order.search');
    Route::get('/search_order_approval', [OrderController::class, 'searchOrderApproval'])->name('order.searchOrderApproval');
    Route::get('/search_order_success', [OrderController::class, 'searchOrderSuccess'])->name('order.searchOrderSuccess');
    Route::get('/search_blog', [BlogController::class, 'search'])->name('blog.search');
    Route::get('/profile', [StaffController::class, 'profile'])->name('staff.profile');
    // Xử lý đơn hàng
    Route::put('/update-order-status-delivery/{id}', [OrderController::class, 'updateOrderStatusDelivery'])->name('order.updateOrderStatusDelivery');
    Route::get('/order-approval', [OrderController::class, 'orderApproval'])->name('order.approval');
    Route::get('/order_success', [OrderController::class, 'orderSuccess'])->name('order.orderSuccess');
    Route::get('/delivery-orders', [OrderController::class, 'manageDeliveryOrders'])->name('order.trackingOrder');
    Route::get('/delivery-orders/search', [OrderController::class, 'manageDeliveryOrders'])->name('order.searchOrderTracking');
Route::post('/admin/orders/update-status/{order}', [OrderController::class, 'updateOrderStatus'])->name('order.updateStatus');
    // Thong ke doanh thu va loi nhuan
    Route::group(['prefix' => '/revenue'], function () {
        Route::get('/day', [RevenueController::class, 'revenueDay'])->name('admin.revenueDay')->middleware('can:managers');
        Route::get('/revenue-day/export-pdf', [RevenueController::class, 'exportPdf'])->name('admin.revenueDay.exportPdf')->middleware('can:managers');
        Route::get('/revenue-day/export-excel', [RevenueController::class, 'exportExcel'])->name('admin.revenueDay.exportExcel')->middleware('can:managers');
        Route::get('/month', [RevenueController::class, 'revenueMonth'])->name('admin.revenueMonth')->middleware('can:managers');
        Route::get('/revenue-month/export-pdf', [RevenueController::class, 'exportPdfMonth'])->name('admin.revenueMonth.exportPdf')->middleware('can:managers');
        Route::get('/revenue-month/export-excel', [RevenueController::class, 'exportExcelMonth'])->name('admin.revenueMonth.exportExcel')->middleware('can:managers');
        Route::get('/year', [RevenueController::class, 'revenueYear'])->name('admin.revenueYear');
        Route::get('/profit', [RevenueController::class, 'profitYear'])->name('admin.profitYear');
        Route::get('/revenue-best-seller-product', [RevenueProductController::class, 'revenueProductBestSeller'])->name('admin.revenueProductBestSeller')->middleware('can:managers');
        Route::get('/revenue-best-seller-product/export-pdf', [RevenueProductController::class, 'exportPdf'])->name('admin.revenueProductBestSeller.exportPdf')->middleware('can:managers');
        Route::get('/revenue-best-seller-product/export-excel', [RevenueProductController::class, 'exportExcel'])->name('admin.revenueProductBestSeller.exportExcel')->middleware('can:managers');
        Route::get('/revenue-best-seller-product-month-year', [RevenueProductController::class, 'revenueProductBestSellerMonthYear'])->name('admin.revenueProductBestSellerMonthYear')->middleware('can:managers');
        Route::get('/revenue-inventory', [RevenueInventoryController::class, 'revenueInventory'])->name('admin.revenueInventory')->middleware('can:managers');
    });
});

//CHATBOT REDIS TEST
// Route::get('/chatbot-redis', [ChatBotApiController::class, 'chatbot'])->name('sites.chatbot-redis');
Route::post('/chat/send', [ChatBotApiController::class, 'sendMessage'])->name('chatbot.send');
// Route::get('/cloudinary', [CloudinaryUploadController::class, 'showForm'])->name('cloudinary.showForm');

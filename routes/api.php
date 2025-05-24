<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\CloudinaryUploadController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\RevenueProductController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//JWT: json web token
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::middleware('auth:sanctum')->group(function () {
// });
Route::get('discount', [ApiController::class, 'discounts'])->name('api.discounts');
Route::get('discount/{id}', [ApiController::class, 'discount'])->name('api.discount');
Route::get('discount-code/{code}', [ApiController::class, 'getDiscountByCode'])->name('api.getDiscountByCode');

Route::get('category', [ApiController::class, 'categories'])->name('api.categories');

Route::get('product', [ApiController::class, 'products'])->name('api.products');
Route::get('product-client', [ApiController::class, 'getProductsClient'])->name('api.getProductsClient');
Route::get('product/{id}', [ApiController::class, 'product'])->name('api.product');
Route::get('product-variant-size/{color}/{product_id}', [ApiController::class, 'productVariantSizes'])->name('api.productVariantSizes');
Route::get('product-variant-selected/{size}/{color}/{product_id}', [ApiController::class, 'getSeletedProductVariant'])->name('api.getSeletedProductVariant');
Route::get('product-discount', [ApiController::class, 'getProductDiscount'])->name('api.getProductDiscount');
Route::get('brand', [ApiController::class, 'brands'])->name('api.brands');

Route::get('product-variant', [ApiController::class, 'productVariants'])->name('api.productVariants');

Route::get('inventory', [ApiController::class, 'inventories'])->name('api.inventories');
Route::get('inventory/{id}', [ApiController::class, 'inventory'])->name('api.inventory');
Route::get('inventoryDetail/{id}', [ApiController::class, 'inventoryDetail'])->name('api.inventoryDetail');
// Route::get('/inventory/search', [InventoryController::class, 'apiSearch'])->name('api.inventory.search');

Route::get('staff/{id}', [ApiController::class, 'staff'])->name('api.staff');

// Route tìm kiếm sản phẩm
Route::get('/search', [SearchController::class, 'search'])->name('api.search');

// Route lấy lịch sử tìm kiếm
Route::get('/search-history', [SearchController::class, 'getSearchHistory']);

// Route gợi ý sản phẩm dựa trên lịch sử tìm kiếm
Route::get('/suggest-content-based', [SearchController::class, 'suggestContentBased']);

// Route xóa lịch sử tìm kiếm
Route::delete('/clear-search-history', [SearchController::class, 'clearSearchHistory']);


// Route::get('/order/{id}', [ApiController::class, 'test'])->name('api.orders');
// Route::post('/webhook', [DialogflowWebhookController::class, 'handle']);

Route::get('blog_detail/{id}', [ApiController::class, 'blogDetail'])->name('api.blogDetail');

Route::get('rate-order/{id}', [ApiController::class, 'rateOrder'])->name('api.rateOrder');

Route::get('/revenue-stats', [RevenueController::class, 'revenueMonthApi'])->name('api.revenueMonthApi');
Route::get('/revenue-product-detail/{product_id}', [RevenueProductController::class, 'getProductVariantDetail'])->name('api.getProductVariantDetail');

Route::get('/revenue-product-detail-month-year/{product_id}', [RevenueProductController::class, 'getProductVariantDetailMonthYear'])->name('api.getProductVariantDetailMonthYear');
Route::get('/revenue-bestseller-product', [RevenueProductController::class, 'topProductSalesApi'])->name('api.topProductSalesApi');


Route::post('/upload', [CloudinaryUploadController::class, 'upload'])->name('api.upload');
Route::get('/recommend/user/{userId}', [RecommendationController::class, 'userBased']);
Route::get('/recommend-ibcf/user/{userId}', [RecommendationController::class, 'itemCFRecommend']);


Route::get('/recommendations/user-cosine/{userId}', [RecommendationController::class, 'userBasedCosine']);
Route::get('/recommendations/item-cosine/{userId}', [RecommendationController::class, 'itemBasedCosine']);

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Api\PaymentController;

// Public Endpoints
Route::get('/categories', [PublicController::class, 'getCategories']);
Route::get('/brands', [PublicController::class, 'getBrands']);
Route::get('/products', [PublicController::class, 'getProducts']);
Route::get('/products/flash-deals', [PublicController::class, 'getFlashDeals']);
Route::get('/products/best-sellers', [PublicController::class, 'getBestSellers']);
Route::get('/products/{id}', [PublicController::class, 'getProductDetails']);
Route::get('/banners', [PublicController::class, 'getBanners']);
Route::get('/settings', [PublicController::class, 'getSettings']);
Route::get('/shipping-zones', [PublicController::class, 'getShippingZones']);
Route::get('/governorates', [PublicController::class, 'getGovernorates']);
Route::get('/shipping-methods', [PublicController::class, 'getShippingMethods']);
Route::get('/posts', [PublicController::class, 'getBlogPosts']);
Route::get('/posts/{slug}', [PublicController::class, 'getBlogPost']);
Route::post('/apply-coupon', [PublicController::class, 'applyCoupon']);
Route::post('/contact', [PublicController::class, 'contactUs']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/oauth-exchange', [AuthController::class, 'exchangeOAuthCode'])
    ->middleware('throttle:12,1');

use App\Http\Controllers\UserController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::get('/user/header-data', [UserController::class, 'getUserHeaderData']);
    Route::get('/user/statistics', [UserController::class, 'getUserStatistics']);
    Route::get('/user/notifications', [UserController::class, 'getNotifications']);
    Route::post('/user/notifications/mark-read', [UserController::class, 'markNotificationsAsRead']);
    Route::post('/user/notifications/{id}/mark-read', [UserController::class, 'markNotificationAsRead']);
    
    // User Messages
    Route::get('/user/messages', [UserController::class, 'getUserMessages']);
    Route::post('/user/messages', [UserController::class, 'sendUserMessage']);
    
    // User Order Endpoints
    Route::get('/user/orders', [UserController::class, 'getOrders']);
    Route::post('/user/orders', [UserController::class, 'createOrder']);
    Route::get('/user/orders/{id}', [UserController::class, 'getOrderDetails']);
    Route::post('/user/orders/{id}/expedite', [UserController::class, 'expediteOrder']);
    Route::post('/user/orders/{id}/cancel', [UserController::class, 'cancelOrder']);
    Route::post('/user/orders/{id}/return', [UserController::class, 'requestReturn']);

    // Review Endpoints
    Route::post('/products/{id}/reviews', [UserController::class, 'postReview']);

    // Cart Endpoints
    Route::get('/user/cart', [UserController::class, 'getCart']);
    Route::post('/user/cart', [UserController::class, 'addToCart']);
    Route::delete('/user/cart/{product_id}', [UserController::class, 'removeFromCart']);
    Route::delete('/user/cart', [UserController::class, 'clearCart']);

    // Favorites Endpoints
    Route::get('/user/favorites', [UserController::class, 'getFavorites']);
    Route::post('/user/favorites', [UserController::class, 'toggleFavorite']);

    // Payment Endpoints
    Route::post('/orders/{id}/pay', [PaymentController::class, 'checkout'])->name('payment.checkout');
});

// Payment Callbacks & Webhooks (Public)
Route::match(['get', 'post'], '/payment/callback/{method}', [PaymentController::class, 'callback'])->name('payment.callback');
Route::post('/payment/webhook/{method}', [PaymentController::class, 'webhook'])->name('payment.webhook');

use App\Http\Controllers\AdminController;
use App\Http\Middleware\AdminMiddleware;

Route::middleware(['auth:sanctum', AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/statistics', [AdminController::class, 'statistics']);
    Route::get('/header-data', [\App\Http\Controllers\AdminSupplementalController::class, 'getHeaderData']);
    Route::get('/financials', [\App\Http\Controllers\AdminFinancialController::class, 'getFinancials']);
    Route::get('/transactions', [\App\Http\Controllers\AdminFinancialController::class, 'getTransactions']);
    
    // Users
    Route::get('/users', [AdminController::class, 'getUsers']);
    Route::get('/staff', [AdminController::class, 'getStaff']);
    Route::post('/staff', [AdminController::class, 'createStaff']);
    Route::put('/users/{id}/role', [AdminController::class, 'updateStaffRole']);
    Route::put('/users/{id}/permissions', [AdminController::class, 'updateStaffPermissions']);
    Route::put('/users/{id}/status', [AdminController::class, 'toggleUserStatus']);
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
    Route::post('/users/batch-delete', [AdminController::class, 'batchDeleteUsers']);

    // Categories
    Route::get('/categories', [AdminController::class, 'getCategories']);
    Route::post('/categories', [AdminController::class, 'createCategory']);
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory']);
    Route::put('/categories/{id}/status', [AdminController::class, 'toggleCategoryStatus']);
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory']);
    Route::post('/categories/batch-delete', [AdminController::class, 'batchDeleteCategories']);

    // Brands
    Route::get('/brands', [AdminController::class, 'getBrands']);
    Route::post('/brands', [AdminController::class, 'createBrand']);
    Route::put('/brands/{id}', [AdminController::class, 'updateBrand']);
    Route::put('/brands/{id}/status', [AdminController::class, 'toggleBrandStatus']);
    Route::delete('/brands/{id}', [AdminController::class, 'deleteBrand']);
    Route::post('/brands/batch-delete', [AdminController::class, 'batchDeleteBrands']);

    // Products
    Route::get('/products', [AdminController::class, 'getProducts']);
    Route::post('/products', [AdminController::class, 'createProduct']);
    Route::put('/products/{id}', [AdminController::class, 'updateProduct']);
    Route::put('/products/{id}/status', [AdminController::class, 'toggleProductStatus']);
    Route::put('/products/{id}/stock', [AdminController::class, 'updateProductStock']);
    Route::post('/products/batch-delete', [AdminController::class, 'batchDeleteProducts']);
    Route::delete('/products/{id}', [AdminController::class, 'deleteProduct']);

    // Inventory
    Route::get('/inventory-logs', [AdminController::class, 'getInventoryLogs']);

    // Orders
    Route::get('/orders', [AdminController::class, 'getOrders']);
    Route::get('/return-requests', [AdminController::class, 'getReturnRequests']);
    Route::get('/orders/{id}', [AdminController::class, 'getOrderDetails']);
    Route::put('/orders/{id}/status', [AdminController::class, 'updateOrderStatus']);
    Route::put('/orders/{id}/handle-return', [AdminController::class, 'handleReturnRequest']);
    Route::delete('/orders/{id}', [AdminController::class, 'deleteOrder']);
    Route::post('/orders/batch-delete', [AdminController::class, 'batchDeleteOrders']);

    // --- Phase 2: Supplemental Modules ---
    // Banners
    Route::get('/banners', [\App\Http\Controllers\AdminSupplementalController::class, 'getBanners']);
    Route::post('/banners', [\App\Http\Controllers\AdminSupplementalController::class, 'createBanner']);
    Route::put('/banners/{id}', [\App\Http\Controllers\AdminSupplementalController::class, 'updateBanner']);
    Route::delete('/banners/{id}', [\App\Http\Controllers\AdminSupplementalController::class, 'deleteBanner']);
    Route::post('/banners/batch-delete', [\App\Http\Controllers\AdminSupplementalController::class, 'batchDeleteBanners']);

    // Coupons
    Route::get('/coupons', [\App\Http\Controllers\AdminSupplementalController::class, 'getCoupons']);
    Route::post('/coupons', [\App\Http\Controllers\AdminSupplementalController::class, 'createCoupon']);
    Route::put('/coupons/{id}', [\App\Http\Controllers\AdminSupplementalController::class, 'updateCoupon']);
    Route::delete('/coupons/{id}', [\App\Http\Controllers\AdminSupplementalController::class, 'deleteCoupon']);

    // Contact Messages
    Route::get('/messages', [\App\Http\Controllers\AdminSupplementalController::class, 'getMessages']);
    Route::put('/messages/{id}', [\App\Http\Controllers\AdminSupplementalController::class, 'markMessageAsRead']);
    Route::post('/messages/{id}/reply', [\App\Http\Controllers\AdminSupplementalController::class, 'replyToMessage']);
    Route::delete('/messages/{id}', [\App\Http\Controllers\AdminSupplementalController::class, 'deleteMessage']);

    // Merchants
    Route::post('/merchants', [\App\Http\Controllers\AdminMerchantController::class, 'store']);

    // Reviews
    Route::get('/reviews', [\App\Http\Controllers\AdminSupplementalController::class, 'getReviews']);
    Route::put('/reviews/{id}/status', [\App\Http\Controllers\AdminSupplementalController::class, 'toggleReviewStatus']);
    Route::post('/reviews/{id}/reply', [\App\Http\Controllers\AdminSupplementalController::class, 'replyToReview']);
    Route::delete('/reviews/{id}', [\App\Http\Controllers\AdminSupplementalController::class, 'deleteReview']);

    // Shipping Zones & Methods
    Route::get('/shipping-zones', [\App\Http\Controllers\AdminSupplementalController::class, 'getShippingZones']);
    Route::post('/shipping-zones', [\App\Http\Controllers\AdminSupplementalController::class, 'createShippingZone']);
    Route::put('/shipping-zones/{id}', [\App\Http\Controllers\AdminSupplementalController::class, 'updateShippingZone']);
    Route::delete('/shipping-zones/{id}', [\App\Http\Controllers\AdminSupplementalController::class, 'deleteShippingZone']);
    
    Route::get('/governorates', [\App\Http\Controllers\AdminSupplementalController::class, 'getGovernorates']);
    Route::post('/shipping-zones/assign-governorate', [\App\Http\Controllers\AdminSupplementalController::class, 'assignGovernorateToZone']);
    Route::post('/shipping-zones/{zoneId}/methods', [\App\Http\Controllers\AdminSupplementalController::class, 'manageShippingMethod']);
    Route::delete('/shipping-methods/{id}', [\App\Http\Controllers\AdminSupplementalController::class, 'deleteShippingMethod']);

    // Store Settings
    Route::get('/settings', [\App\Http\Controllers\AdminSupplementalController::class, 'getSettings']);
    Route::post('/settings', [\App\Http\Controllers\AdminSupplementalController::class, 'updateSettings']);
    Route::post('/store-settings/bulk', [\App\Http\Controllers\AdminSupplementalController::class, 'updateBulkSettings']);

    // Blog
    Route::get('/posts', [\App\Http\Controllers\AdminBlogController::class, 'index']);
    Route::post('/posts', [\App\Http\Controllers\AdminBlogController::class, 'store']);
    Route::put('/posts/{id}', [\App\Http\Controllers\AdminBlogController::class, 'update']);
    Route::delete('/posts/{id}', [\App\Http\Controllers\AdminBlogController::class, 'destroy']);
    Route::post('/posts/batch-delete', [\App\Http\Controllers\AdminBlogController::class, 'batchDeletePosts']);

    // --- Purchases & Suppliers ---
    Route::get('/purchases', [\App\Http\Controllers\AdminPurchaseController::class, 'index']);
    Route::get('/purchases/statistics', [\App\Http\Controllers\AdminPurchaseController::class, 'statistics']);
    Route::post('/purchases', [\App\Http\Controllers\AdminPurchaseController::class, 'store']);
    Route::put('/purchases/{id}/status', [\App\Http\Controllers\AdminPurchaseController::class, 'updateStatus']);
    Route::delete('/purchases/{id}', [\App\Http\Controllers\AdminPurchaseController::class, 'destroy']);

    Route::get('/suppliers', [\App\Http\Controllers\AdminPurchaseController::class, 'getSuppliers']);
    Route::post('/suppliers', [\App\Http\Controllers\AdminPurchaseController::class, 'storeSupplier']);
});

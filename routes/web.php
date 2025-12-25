<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AdminManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/user-type', [HomeController::class, 'userType'])->name('user-type');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Authentication
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');

// Shop Routes (requires authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
    Route::get('/shop/{id}', [ProductController::class, 'show'])->name('product.show');
    
    // API for similar products
    Route::get('/api/products/similar/{id}', [ProductController::class, 'similar'])->name('products.similar');
    
    // Cart Routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    
    // Order Routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{id}/delete', [OrderController::class, 'delete'])->name('orders.delete');
    Route::get('/order-confirm/{id}', [OrderController::class, 'confirm'])->name('order.confirm');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update-name', [ProfileController::class, 'updateName'])->name('profile.update-name');
    Route::post('/profile/update-phone', [ProfileController::class, 'updatePhone'])->name('profile.update-phone');
    Route::post('/profile/add-email', [ProfileController::class, 'addEmail'])->name('profile.add-email');
    
    // Contact and Review Routes
    Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    
    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/delete-all', [NotificationController::class, 'deleteAll'])->name('notifications.delete-all');
});

// Admin Routes (requires admin authentication)
Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::post('/inventory/products', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventory/products/{id}', [InventoryController::class, 'show'])->name('inventory.show');
    Route::put('/inventory/products/{id}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/products/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/order-history', [DashboardController::class, 'orderHistory'])->name('order-history');
    Route::get('/orders/{id}', [DashboardController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/verify', [DashboardController::class, 'verify'])->name('orders.verify');
    Route::post('/scan-qr', [DashboardController::class, 'scanQr'])->name('scan-qr');
    Route::post('/send-notification', [DashboardController::class, 'sendNotification'])->name('send-notification');
    Route::put('/orders/{id}/status', [DashboardController::class, 'updateStatus'])->name('orders.update-status');
    
    // Admin Management Routes
    Route::get('/management', [AdminManagementController::class, 'index'])->name('management');
    Route::get('/management/{id}', [AdminManagementController::class, 'show'])->name('detail');
    Route::get('/management/admins', [AdminManagementController::class, 'getAdmins'])->name('management.admins');
    Route::post('/management', [AdminManagementController::class, 'store'])->name('management.store');
    Route::put('/management/{id}/status', [AdminManagementController::class, 'updateStatus'])->name('management.status');
    Route::put('/management/{id}/password', [AdminManagementController::class, 'updatePassword'])->name('management.password');
    Route::delete('/management/{id}', [AdminManagementController::class, 'destroy'])->name('management.destroy');
});

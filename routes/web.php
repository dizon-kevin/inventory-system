<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutAddressController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\XenditWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::post('/xendit/webhook', XenditWebhookController::class)->name('xendit.webhook');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return $user->isAdmin() ? redirect()->route('admin.dashboard') : redirect()->route('user.dashboard');
    })->name('dashboard');

    // Admin routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/notifications', [NotificationController::class, 'adminIndex'])->name('notifications.index');

        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update', 'destroy']);

        Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
    });

    // User routes
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
        Route::patch('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');

        Route::get('/checkout', [OrderController::class, 'create'])->name('orders.create');
        Route::get('/checkout/address-data/regions', [CheckoutAddressController::class, 'regions'])->name('checkout.address-data.regions');
        Route::get('/checkout/address-data/regions/{regionCode}/provinces', [CheckoutAddressController::class, 'provinces'])->name('checkout.address-data.provinces');
        Route::get('/checkout/address-data/regions/{regionCode}/cities', [CheckoutAddressController::class, 'cities'])->name('checkout.address-data.cities');
        Route::get('/checkout/address-data/cities/{cityCode}/barangays', [CheckoutAddressController::class, 'barangays'])->name('checkout.address-data.barangays');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/payment-return', [OrderController::class, 'paymentReturn'])->name('orders.payment-return');
        Route::get('/orders/{order}/status-snapshot', [OrderController::class, 'statusSnapshot'])->name('orders.status-snapshot');
        Route::post('/orders/{order}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('orders.confirm-payment');
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/orders/{order}/track', [OrderController::class, 'track'])->name('orders.track');

        Route::get('/notifications', [NotificationController::class, 'userIndex'])->name('notifications.index');
    });

    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');

    // Admin order routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'update']);
        Route::post('orders/{order}/resync-tracker', [AdminOrderController::class, 'resyncTracker'])->name('orders.resync-tracker');
    });
});

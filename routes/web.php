<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManageRolesAndPermissionsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Public store pages
Route::get('', [StoreController::class, 'index'])->name('store.index');
Route::get('item/{slug}', [StoreController::class, 'show'])->name('store.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/cart', [CartController::class, 'index'])->name('store.cart');
    Route::post('/cart/increment/{cartItemId}', [CartController::class, 'incrementItemQuantity'])->name('store.cart.increment');
    Route::post('/cart/decrement/{cartItemId}', [CartController::class, 'decrementItemQuantity'])->name('store.cart.decrement');
    Route::post('/cart/{itemId}/{quantity}', [CartController::class, 'store'])->name('store.cart.store');
    Route::post('/cart/{cartItemId}', [CartController::class, 'destroy'])->name('store.cart.destroy');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{orderId}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::post('/orders/{orderId}/payment', [OrderController::class, 'createOrderPayment'])->name('orders.payment');

    // Admin: roles & permissions
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/roles', [ManageRolesAndPermissionsController::class, 'index'])->name('roles.index');
        Route::post('/roles', [ManageRolesAndPermissionsController::class, 'storeRole'])->name('roles.store');
        Route::patch('/roles/{role}', [ManageRolesAndPermissionsController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{role}', [ManageRolesAndPermissionsController::class, 'deleteRole'])->name('roles.delete');

        Route::post('/roles/{role}/permissions', [ManageRolesAndPermissionsController::class, 'storePermission'])->name('permissions.store');
        Route::delete('/roles/{role}/permissions/{permission}', [ManageRolesAndPermissionsController::class, 'deletePermission'])->name('permissions.delete');

        Route::post('/assign-role', [ManageRolesAndPermissionsController::class, 'assignRoleToUser'])->name('roles.assign');
    });

    Route::get('/checkout', function () {
        return view('checkout');
    })->name('checkout');

    Route::get('paypal/payment/success', [PayPalController::class, 'paymentSuccess'])->name('paypal.payment.success');
    Route::get('paypal/payment/cancel', [PayPalController::class, 'paymentCancel'])->name('paypal.payment.cancel');
});

require __DIR__ . '/auth.php';

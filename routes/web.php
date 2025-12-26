<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

// Public store pages
Route::get('', [StoreController::class, 'index'])->name('store.index');
Route::get('item/{slug}', [StoreController::class, 'show'])->name('store.show');

Route::middleware('auth')->group(function () {
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
    Route::get('/checkout', function () {
        return view('checkout');
    })->name('checkout');

    Route::get('paypal/payment/success', [PayPalController::class, 'paymentSuccess'])->name('paypal.payment.success');
    Route::get('paypal/payment/cancel', [PayPalController::class, 'paymentCancel'])->name('paypal.payment.cancel');
});

require __DIR__ . '/admin.php';
require __DIR__ . '/auth.php';

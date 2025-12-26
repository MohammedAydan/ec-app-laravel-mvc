<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\ManageRolesAndPermissionsController;
use App\Http\Controllers\Admin\ManageUsersController;
use App\Http\Controllers\Admin\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin.role'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/roles', [ManageRolesAndPermissionsController::class, 'index'])->name('roles.index');
        Route::post('/roles', [ManageRolesAndPermissionsController::class, 'storeRole'])->name('roles.store');
        Route::patch('/roles/{role}', [ManageRolesAndPermissionsController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{role}', [ManageRolesAndPermissionsController::class, 'deleteRole'])->name('roles.delete');

        Route::post('/roles/{role}/permissions', [ManageRolesAndPermissionsController::class, 'storePermission'])->name('permissions.store');
        Route::delete('/roles/{role}/permissions/{permission}', [ManageRolesAndPermissionsController::class, 'deletePermission'])->name('permissions.delete');

        Route::post('/assign-role', [ManageRolesAndPermissionsController::class, 'assignRoleToUser'])->name('roles.assign');

        Route::get('/items', [ItemController::class, 'index'])->name('items.index');
        Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
        Route::post('/items/store', [ItemController::class, 'store'])->name('items.store');
        Route::get('/items/{id}', [ItemController::class, 'show'])->name('items.show');
        Route::get('/items/{id}/edit', [ItemController::class, 'edit'])->name('items.edit');
        Route::patch('/items/{id}', [ItemController::class, 'update'])->name('items.update');
        Route::get('/items/{id}/delete', [ItemController::class, 'delete'])->name('items.delete');
        Route::delete('/items/{id}', [ItemController::class, 'destroy'])->name('items.destroy');

        Route::get('/users', [ManageUsersController::class, 'index'])->name('users.index');
        Route::get('/users/{id}', [ManageUsersController::class, 'show'])->name('users.show');
        Route::get('/users/{id}/edit', [ManageUsersController::class, 'edit'])->name('users.edit');
        Route::patch('/users/{id}', [ManageUsersController::class, 'update'])->name('users.update');
        Route::get('/users/{id}/delete', [ManageUsersController::class, 'delete'])->name('users.delete');
        Route::delete('/users/{id}', [ManageUsersController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{id}/toggle', [ManageUsersController::class, 'toggleStatus'])->name('users.toggle');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/report', [OrderController::class, 'report'])->name('orders.report');
        Route::get('/orders/{orderId}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{orderId}/print', [OrderController::class, 'printInvoice'])->name('orders.print');
        Route::get('/orders/{orderId}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::patch('/orders/{orderId}', [OrderController::class, 'update'])->name('orders.update');
        Route::get('/orders/{orderId}/delete', [OrderController::class, 'delete'])->name('orders.delete');
        Route::delete('/orders/{orderId}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });
});

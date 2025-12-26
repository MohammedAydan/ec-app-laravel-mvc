<?php

namespace App\Providers;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $cartCount = 0;

            if (Auth::check()) {
                $cartCount = Cart::where('user_id', Auth::id())->count();
            }

            $view->with('cartCount', $cartCount);
        });

        // Share permission flags with admin and dashboard views for quick gating in Blade
        View::composer(['admin.*', 'dashboard'], function ($view) {
            $permissions = Auth::user()?->role?->permissions->pluck('name')->values() ?? collect();

            $view->with('permissionNames', $permissions);
            $view->with('canCreate', $permissions->contains('create'));
            $view->with('canRead', $permissions->contains('read'));
            $view->with('canUpdate', $permissions->contains('update'));
            $view->with('canDelete', $permissions->contains('delete'));
        });
    }
}

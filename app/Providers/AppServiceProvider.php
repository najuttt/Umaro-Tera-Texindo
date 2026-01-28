<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;
use Milon\Barcode\Facades\DNS2DFacade as DNS2D;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Repositories\Eloquent\AdminRepository;

// ⬇️ TAMBAHAN INI
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->alias(DNS1D::class, 'DNS1D');
        $this->app->alias(DNS2D::class, 'DNS2D');
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ========================
        // VIEW COMPOSER (PUNYA LO)
        // ========================
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $carts = Cart::where('user_id', Auth::id())->first();
                if (! $carts) {
                    return;
                }

                $view->with('cartsitems', $carts);
            }
        });

        // ========================
        // 🔥 FIX SOCIALITE GOOGLE
        // ========================
        Socialite::extend('google', function ($app) {
            $config = $app['config']['services.google'];

            return Socialite::buildProvider(
                GoogleProvider::class,
                $config
            )->stateless(); // ← INI KUNCI NYA
        });
    }
}

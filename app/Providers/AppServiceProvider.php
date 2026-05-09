<?php

namespace App\Providers;

use App\Repositories\ProductRepository;
use App\Repositories\ProductRepositoryInterface;
use App\Models\Order;
use App\Policies\OrderPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Order::class, OrderPolicy::class);

        RateLimiter::for('auth', function ($request) {
            return Limit::perMinute((int) config('api.rate_limits.auth.per_minute', 10))
                ->by($request->ip());
        });

        RateLimiter::for('products', function ($request) {
            return Limit::perMinute((int) config('api.rate_limits.products.per_minute', 60))
                ->by($request->user()?->id ?: $request->ip());
        });
    }
}

<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\User;
use App\Observers\OrderObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $req) {
            return Limit::perMinute(30)->by($req->user()?->id ?: $req->ip());
        });

        RateLimiter::for('login', function (Request $req) {
            return Limit::perMinute(5)->by($req->ip());
        });



        Order::observe(OrderObserver::class);

        Gate::define('view-trashed-only', function (User $user) {
            return $user->role === "admin";
        });
        
    }
}

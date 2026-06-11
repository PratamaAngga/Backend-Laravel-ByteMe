<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // 🌟 WAJIB DITAMBAHKAN AGAR TIDAK ERROR

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
        // 🌟 PAKSA HTTPS JIKA DI SERVER PRODUCTION (RAILWAY)
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
    }
}
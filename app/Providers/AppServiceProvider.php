<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <--- 1. TAMBAHKAN BARIS INI (PENTING!)

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
        // 2. TAMBAHKAN BLOK KODE INI DI DALAM FUNGSI BOOT
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
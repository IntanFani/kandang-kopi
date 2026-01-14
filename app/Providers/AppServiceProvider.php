<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// Tambahkan baris ini di bagian atas
use Illuminate\Pagination\Paginator;

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
        // Mengatur Laravel agar menggunakan struktur HTML Bootstrap untuk pagination
        Paginator::useBootstrapFive();
    }
}
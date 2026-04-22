<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use App\Models\Pendaftar;
use App\Observers\UserObserver;
use App\Observers\PendaftarObserver;

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
        Paginator::useBootstrapFive();

        // Register model observers for audit logging
        User::observe(UserObserver::class);
        Pendaftar::observe(PendaftarObserver::class);
    }
}

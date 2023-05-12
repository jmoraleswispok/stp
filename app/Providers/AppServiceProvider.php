<?php

namespace App\Providers;

use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('Helpers/CustomHelper.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        AwsS3V3Adapter::macro('getClient', fn() => $this->client);
    }
}

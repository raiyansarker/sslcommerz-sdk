<?php

declare(strict_types=1);

namespace RaiyanSarker\SSLCommerz\Laravel;

use Illuminate\Support\ServiceProvider;
use RaiyanSarker\SSLCommerz\SSLCommerzConnector;

class SSLCommerzServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/sslcommerz.php',
            'sslcommerz'
        );

        $this->app->singleton(SSLCommerzConnector::class, function () {
            /** @var \Illuminate\Contracts\Config\Repository $configRepository */
            $configRepository = $this->app->make('config');
            
            /** @var array<string, mixed> $config */
            $config = $configRepository->get('sslcommerz') ?? [];

            $storeId = $config['store_id'] ?? '';
            $storePassword = $config['store_password'] ?? '';
            $isSandbox = $config['sandbox'] ?? true;

            return new SSLCommerzConnector(
                storeId: is_string($storeId) ? $storeId : '',
                storePassword: is_string($storePassword) ? $storePassword : '',
                isSandbox: (bool) $isSandbox,
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/sslcommerz.php' => config_path('sslcommerz.php'),
            ], 'sslcommerz-config');
        }
    }
}

<?php

namespace Seungmun\LaravelYandexCheckout;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as Provider;
use Seungmun\LaravelYandexCheckout\Contracts\Cart as CartContract;

class ServiceProvider extends Provider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-yandex-checkout.php',
            'laravel-yandex-checkout'
        );

        $this->app->singleton(Cashier::class, function (Application $app) {
            return new Cashier($app->make('config')->get('laravel-yandex-checkout', []));
        });

        $this->app->singleton(CartContract::class, function (Application $app) {
            return new Cart();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-yandex-checkout.php' => config_path('laravel-yandex-checkout.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Register predefined routes used for laravel-yandex-checkout.
        include __DIR__ . '/../routes/web.php';
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}

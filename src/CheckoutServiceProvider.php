<?php

namespace Seungmun\LaravelYandexCheckout;

use Illuminate\Support\ServiceProvider as Provider;

class CheckoutServiceProvider extends Provider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMigrations();

        if ($this->app->runningInConsole()) {
            $this->registerMigrations();

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'laravel-yandex-checkout');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ( ! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(
                __DIR__ . '/../config/laravel-yandex-checkout.php',
                'laravel-yandex-checkout'
            );
        }

        $this->offerPublishing();
    }

    /**
     * Register checkout's migration files.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if (Checkout::$runsMigrations) {
            return $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    /**
     * Setup the resource publishing groups for Passport.
     *
     * @return void
     */
    protected function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/laravel-yandex-checkout.php' => config_path('laravel-yandex-checkout.php'),
            ], 'laravel-yandex-checkout');
        }
    }
}

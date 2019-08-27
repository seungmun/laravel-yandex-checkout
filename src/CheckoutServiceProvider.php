<?php

namespace Seungmun\LaravelYandexCheckout;

use YandexCheckout\Client;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\ServiceProvider as Provider;
use Seungmun\LaravelYandexCheckout\Contracts\YandexCheckout;

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

            $this->commands([
                Console\InstallCommand::class,
            ]);
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

        $this->registerYandexCheckoutClient();
        $this->offerPublishing();
    }

    /**
     * Register the YandexCheckout client.
     *
     * @return void
     */
    protected function registerYandexCheckoutClient()
    {
        $this->app->singleton(YandexCheckout::class, function () {
            $config = $this->app->make(Config::class)->get('laravel-yandex-checkout');
            $shop = $config['shops'][$config['default']];

            return tap($this->makeYandexCheckoutClient(), function ($client) use ($shop) {
                /** @var \Seungmun\LaravelYandexCheckout\Contracts\YandexCheckout $client */
                $client->setAuth($shop['id'], $shop['secret']);
            });
        });
    }

    /**
     * Make the YandexCheckout client instance.
     *
     * @return \YandexCheckout\Client
     */
    public function makeYandexCheckoutClient()
    {
        return new Client();
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

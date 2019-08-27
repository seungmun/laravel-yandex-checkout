<?php

namespace Seungmun\LaravelYandexCheckout\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Seungmun\LaravelYandexCheckout\CheckoutServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [CheckoutServiceProvider::class];
    }

    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [];
    }

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', [
            '--database' => 'testbench',
            '--realpath' => realpath(__DIR__ . '/../database/migrations'),
        ]);
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']['laravel-yandex-checkout'] =
            require __DIR__ . '/../config/laravel-yandex-checkout.php';

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => env('DB_DRIVER', 'mysql'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'seungmun'),
            'username' => env('DB_USERNAME', 'seungmun'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'strict' => false,
            'engine' => null,
        ]);
    }
}
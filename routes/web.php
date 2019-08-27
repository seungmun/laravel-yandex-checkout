<?php

if (config('laravel-yandex-checkout.webhook_routes', true)) {
    Route::group(['middleware' => ['web'], 'prefix' => 'api'], function () {
        Route::any('webhooks/payment', 'Seungmun\LaravelYandexCheckout\Http\WebhookController@payment');
    });
}
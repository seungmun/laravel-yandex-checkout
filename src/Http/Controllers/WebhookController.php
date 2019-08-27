<?php

namespace Seungmun\LaravelYandexCheckout\Http\Controllers;

class WebhookController
{
    use HandlesCheckoutErrors;

    /**
     * Handle a notification webhook request from the YandexCheckout.
     *
     * @return array
     */
    public function yandex()
    {
        return [];
    }
}
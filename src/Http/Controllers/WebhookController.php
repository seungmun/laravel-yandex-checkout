<?php

namespace Seungmun\LaravelYandexCheckout\Http\Controllers;

use Illuminate\Http\Request;
use Seungmun\LaravelYandexCheckout\Traits\HandlesYandex;
use Seungmun\LaravelYandexCheckout\Exceptions\CheckoutException;

class WebhookController
{
    use HandlesCheckoutErrors, HandlesYandex;

    /**
     * Handle a notification webhook request from the YandexCheckout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function yandex(Request $request)
    {
        try {
            $this->handleNotification($this->notificationFactory($request->all()));
        } catch (CheckoutException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        return response()->json(['success' => true, 'message' => 'ok'], 202);
    }
}
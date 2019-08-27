<?php

namespace Seungmun\LaravelYandexCheckout\Http;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Seungmun\LaravelYandexCheckout\Cashier;
use Seungmun\LaravelYandexCheckout\Exceptions\PaymentException;

class WebhookController extends Controller
{
    /**
     * Handle a payment webhook request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function payment(Request $request)
    {
        /** @var \Seungmun\LaravelYandexCheckout\Cashier $cashier */
        $cashier = app()->make(Cashier::class);

        try {
            $payment = $cashier->notificationFactory($request->all());
            $cashier->handleNotification($payment);
        } catch (PaymentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        return response()->json(['success' => true, 'message' => 'ok'], 202);
    }
}
<?php

namespace Seungmun\LaravelYandexCheckout;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Container\Container;
use Seungmun\LaravelYandexCheckout\Models\Order;
use Seungmun\LaravelYandexCheckout\Jobs\ExpiresOrder;
use Seungmun\LaravelYandexCheckout\Contracts\Customer;
use Seungmun\LaravelYandexCheckout\Contracts\YandexCheckout;
use Seungmun\LaravelYandexCheckout\Exceptions\CheckoutException;

class CheckoutService
{
    /**
     * The Laravel application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Create a new checkout service instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Make a new order instance.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Customer|null  $customer
     * @return \Seungmun\LaravelYandexCheckout\Models\Order
     */
    public function order(Customer $customer = null)
    {
        $user = $customer ?: $this->user();

        $order = Checkout::order();
        $user->orders()->save($order);

        return $order;
    }

    /**
     * Purchase all of the items in the specified cart.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Models\Order  $order
     * @return \Seungmun\LaravelYandexCheckout\Models\Order
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\CheckoutException
     */
    public function purchase(Order $order)
    {
        $payload = $order->payload()
            ->setReceiver('email', $order->user->email);

        try {
            /** @var \YandexCheckout\Client $client */
            $client = $this->app->make(YandexCheckout::class);
            $response = $client->createPayment($payload->payload());
        } catch (Exception $e) {
            throw new CheckoutException($e);
        }

        DB::transaction(function () use ($order, $response) {
            $payment = Checkout::payment();
            $payment->uuid = $response->getId();
            $payment->shop_id = $response->getRecipient()->getAccountId();
            $payment->is_paid = $response->getPaid();
            $payment->total_paid = 0;
            $payment->status = $response->getStatus();
            $payment->captured_at = $response->getCapturedAt();
            $payment->expires_at = $response->getExpiresAt();
            $order->addPayment($payment);
        });

        ExpiresOrder::dispatch($order)
            ->delay(
                now()->addMinutes(config('laravel-yandex-checkout.payment_expiry_period'))
            );

        return $order;
    }

    /**
     * Get currently logged in user.
     *
     * @return \Seungmun\LaravelYandexCheckout\Contracts\Customer|\Illuminate\Contracts\Auth\Authenticatable
     */
    public function user()
    {
        return $this->app->make(Guard::class)->user();
    }
}
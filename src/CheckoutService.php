<?php

namespace Seungmun\LaravelYandexCheckout;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Container\Container;
use Seungmun\LaravelYandexCheckout\Models\Order;
use Seungmun\LaravelYandexCheckout\Jobs\ExpiresOrder;
use Seungmun\LaravelYandexCheckout\Contracts\Customer;
use YandexCheckout\Request\Payments\CreatePaymentResponse;
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
     * Factory method for payment response.
     *
     * @param  mixed  $response
     * @return \Seungmun\LaravelYandexCheckout\Models\Payment
     */
    private function paymentFactory($response)
    {
        $payment = Checkout::payment();

        if ($response instanceof CreatePaymentResponse) {
            $payment->uuid = optional($response)->getId() ?? Str::uuid();
            $payment->shop_id = $response->getRecipient()->getAccountId();
            $payment->is_paid = $response->getPaid();
            $payment->status = $response->getStatus();
            $payment->captured_at = $response->getCapturedAt();
            $payment->expires_at = $response->getExpiresAt();
            $payment->total_paid = 0;
        } else if (is_null($response)) {
            $payment->uuid = Str::uuid();
            $payment->shop_id = null;
            $payment->is_paid = true;
            $payment->status = 'succeeded';
            $payment->captured_at = null;
            $payment->expires_at = null;
            $payment->total_paid = 0;
        }

        return $payment;
    }

    /**
     * Purchase all of the items in the specified cart.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Models\Order  $order
     * @param  bool  $capture
     * @return \Seungmun\LaravelYandexCheckout\Models\Order
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\CheckoutException
     */
    public function purchase(Order $order, bool $capture = true)
    {
        $payload = $order->payload()
            ->setReceiver('email', $order->user->email)
            ->autoCapture($capture);

        try {
            /** @var \YandexCheckout\Client $client */
            $client = $this->app->make(YandexCheckout::class);
            $response = $order->shouldPurchase() ? $client->createPayment($payload->payload()) : null;
        } catch (Exception $e) {
            throw new CheckoutException($e);
        }

        $payment = DB::transaction(function () use ($order, $response) {
            $payment = $this->paymentFactory($response);
            $order->addPayment($payment);

            return $payment;
        });

        ExpiresOrder::dispatch($order)->delay(
            now()->addMinutes(config('laravel-yandex-checkout.payment_expiry_period'))
        );

        return $order;
    }

    /**
     * Find order of the specified uuid or throw fails.
     *
     * @param  string  $uuid
     * @return \Seungmun\LaravelYandexCheckout\Models\Order|null
     */
    public function findOrderOrFail($uuid)
    {
        return $this->user()
            ->orders()
            ->where('uuid', $uuid)
            ->firstOrFail();
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
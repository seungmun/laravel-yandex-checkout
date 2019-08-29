<?php

namespace Seungmun\LaravelYandexCheckout;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Container\Container;
use Seungmun\LaravelYandexCheckout\Contracts\Customer;
use Seungmun\LaravelYandexCheckout\Jobs\ExpiresPayment;
use Seungmun\LaravelYandexCheckout\Models\IssuedCoupon;
use Seungmun\LaravelYandexCheckout\Bridge\CreatePayment;
use Seungmun\LaravelYandexCheckout\Contracts\YandexCheckout;
use Seungmun\LaravelYandexCheckout\Exceptions\CheckoutException;
use Seungmun\LaravelYandexCheckout\Contracts\CheckoutService as CheckoutServiceContract;

class CheckoutService implements CheckoutServiceContract
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
     * Purchase all of the items in the specified cart.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Cart  $cart
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Customer|\Illuminate\Contracts\Auth\Authenticatable  $customer
     * @return \Seungmun\LaravelYandexCheckout\Models\Payment
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\CheckoutException
     */
    public function purchase(Cart $cart, Customer $customer = null)
    {
        if (is_null($customer)) {
            $customer = $this->app->make(Guard::class)->user();
        }

        $createPayment = (new CreatePayment())
            ->loadFromCart($cart)
            ->setReceiver('email', $customer->email);

        try {
            /** @var \YandexCheckout\Client $client */
            $client = $this->app->make(YandexCheckout::class);
            $response = $client->createPayment($createPayment->payload());
        } catch (Exception $e) {
            throw new CheckoutException($e);
        }

        $payment = DB::transaction(function () use ($customer, $cart, $response) {
            $payment = Checkout::payment();
            $payment->uuid = $response->getId();
            $payment->shop_id = $response->getRecipient()->getAccountId();
            $payment->is_paid = $response->getPaid();
            $payment->status = $response->getStatus();
            $payment->captured_at = $response->getCapturedAt();
            $payment->expires_at = $response->getExpiresAt();
            $payment->save();

            $summary = Checkout::orderSummary();
            $summary->description = $cart->getDescription();
            $summary->amount = $cart->getAmount();
            $summary->discount = $cart->getDiscount();
            $summary->total_amount = $cart->getTotalAmount();
            $summary->extra = $cart->getAttributes();
            $summary->save();
            $payment->summary()->save($summary);

            $orders = $cart->items()->map(function (CartItem $item) {
                $order = Checkout::order();
                $order->price = $item->getPrice();
                $order->quantity = $item->getQuantity();
                $order->amount = $item->getAmount();
                $order->product()->associate($item->getProduct());

                return $order;
            });

            $summary->orders()->saveMany($orders);
            $customer->payments()->save($payment);

            $cart->coupons()->each(function (IssuedCoupon $issuedCoupon) use ($summary, $cart) {
                $issuedCoupon->update(['used_at' => new Carbon]);
                $issuedCoupon->summary()->save($summary, ['discount' => $cart->getDiscount()]);
                // 여러 쿠폰 사용으로 확장시 discount 값이 고정으로 입력되는 형태는 오류가 된다.
            });

            return $payment;
        });

        ExpiresPayment::dispatch($payment)
            ->delay(
                now()->addMinutes(config('laravel-yandex-checkout.payment_expiry_period'))
            );

        return $payment;
    }
}
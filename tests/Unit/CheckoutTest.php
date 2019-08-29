<?php

namespace Seungmun\LaravelYandexCheckout\Tests\Unit;

use Seungmun\LaravelYandexCheckout\Tests\TestCase;
use Seungmun\LaravelYandexCheckout\Tests\Unit\Models\Bus;
use Seungmun\LaravelYandexCheckout\Tests\Unit\Models\User;
use Seungmun\LaravelYandexCheckout\Contracts\CheckoutService;

class CheckoutTest extends TestCase
{
    public function test_example()
    {

        $checkout = $this->app->make(CheckoutService::class);

        $order = $checkout->order(User::find(1));
        $order->addProduct(Bus::find(1), 3);
        $order->addProduct(Bus::find(2), 3);
        $order->addProduct(Bus::find(3), 3);
        $order->addProduct(Bus::find(1), 3);

        $test = $checkout->purchase($order);
        dd($test->payment->confirmation_url);

        $this->assertTrue(true);
    }
}
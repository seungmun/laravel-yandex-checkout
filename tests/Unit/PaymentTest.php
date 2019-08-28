<?php

namespace Seungmun\LaravelYandexCheckout\Tests\Unit;

use Seungmun\LaravelYandexCheckout\Cart;
use Seungmun\LaravelYandexCheckout\CartItem;
use Seungmun\LaravelYandexCheckout\Tests\TestCase;
use Seungmun\LaravelYandexCheckout\Contracts\CheckoutService;

class Payment extends TestCase
{
    public function test_example()
    {
        $product = $this->dummyProduct();
        $user = $this->dummyUser();

        $cart = new Cart();
        $cart->add(new CartItem($product, 5));

        $checkout = $this->app->make(CheckoutService::class);
        $result = $checkout->purchase($cart, $user);

        dd($result->confirmation_url);
    }

    private function dummyProduct()
    {
        $dummy = new DummyProduct();
        $dummy->setAttribute('id', 1);

        return $dummy;
    }

    private function dummyUser()
    {
        $dummy = new DummyUser();
        $dummy->setAttribute('id', 1);
        $dummy->setAttribute('email', 'seungmunj@gmail.com');

        return $dummy;
    }
}
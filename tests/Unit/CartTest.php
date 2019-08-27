<?php

namespace Seungmun\LaravelYandexCheckout\Tests\Unit;

use Seungmun\LaravelYandexCheckout\Cart;
use Seungmun\LaravelYandexCheckout\CartItem;
use Seungmun\LaravelYandexCheckout\Tests\TestCase;
use Seungmun\LaravelYandexCheckout\Contracts\Product;

class CartTest extends TestCase
{
    public function test_product_can_be_created()
    {
        $product = $this->dummyProduct();

        $this->assertInstanceOf(Product::class, $product);
    }

    public function test_cart_instance_can_be_created()
    {
        $cart = new Cart();

        $this->assertInstanceOf(Cart::class, $cart);
    }

    public function test_cart_item_instance_can_be_created()
    {
        $product = $this->dummyProduct();
        $cartItem = new CartItem($product, 5);

        $this->assertInstanceOf(CartItem::class, $cartItem);
    }

    public function test_cart_takes_cart_items()
    {
        $product = $this->dummyProduct();

        $cart = new Cart();
        $cart->add($product);
        $cart->add($product, 5);
        $cart->add(new CartItem($product, 5));

        $this->assertTrue($cart->isNotEmpty());
    }

    public function test_cart_add_cart_items_quantity()
    {
        $product = $this->dummyProduct();

        $cart = new Cart();
        $cart->add($product);
        $cart->add(new CartItem($product, 1));

        $success = $cart->count() === 1 && $cart->items()->first()->getQuantity() === 2;

        $this->assertTrue($success);
    }

    public function test_cart_get_cart_item()
    {
        $product = $this->dummyProduct();

        $cart = new Cart();
        $cart->add($product);
        $cart->add(new CartItem($product, 1));

        $dummy = $cart->get($product);

        $this->assertIsArray($dummy->toArray());
    }

    private function dummyProduct()
    {
        $dummy = new DummyProduct();
        $dummy->setAttribute('id', 1);

        return $dummy;
    }
}
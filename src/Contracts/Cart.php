<?php

namespace Seungmun\LaravelYandexCheckout\Contracts;

interface Cart
{
    /**
     * Add a specified product into cart.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Product  $product
     * @param  int  $quantity
     * @return $this
     */
    public function add(Product $product, $quantity);

    /**
     * Remove a specified product from cart.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Product  $product
     * @return $this
     */
    public function remove(Product $product);

    /**
     * Retrieve all products in the cart.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all();

    /**
     * Clear all products in the cart.
     *
     * @return $this
     */
    public function clear();

    /**
     * Determine if the cart is empty.
     *
     * @return bool
     */
    public function isEmpty();

    /**
     * Determine if the cart is not empty.
     *
     * @return bool
     */
    public function isNotEmpty();


    /**
     * Make a payment of the specified user with currently added cart items.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Customer  $user
     * @param  array  $extra
     * @return \Seungmun\LaravelYandexCheckout\Contracts\Payment
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\CartException
     */
    public function makePayment(Customer $user, array $extra = []);
}

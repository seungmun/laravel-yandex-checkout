<?php

namespace Seungmun\LaravelYandexCheckout\Contracts;

interface CartItem
{
    /**
     * Get product of the cart item.
     *
     * @return \Seungmun\LaravelYandexCheckout\Contracts\Product
     */
    public function product();

    /**
     * Get quantity of the cart item.
     *
     * @return int
     */
    public function quantity();

    /**
     * Get price of the cart item.
     *
     * @return int
     */
    public function price();

    /**
     * Get amount of the cart item.
     *
     * @return int
     */
    public function amount();

    /**
     * Convert the instance to an array.
     *
     * @return array
     */
    public function toArray();
}

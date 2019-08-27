<?php

namespace Seungmun\LaravelYandexCheckout\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface Product
{
    /**
     * Get the product's order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function order(): MorphOne;

    /**
     * Get title of the product.
     *
     * @return string
     */
    public function title();

    /**
     * Get description of the product.
     *
     * @return string
     */
    public function description();

    /**
     * Get price of the product.
     *
     * @return int
     */
    public function price();

    /**
     * Determine if the product is sold out.
     *
     * @return bool
     */
    public function isSoldOut();

    /**
     * Determine if the product can buy.
     *
     * @return bool
     */
    public function canBuy();

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray();
}
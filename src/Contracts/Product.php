<?php

namespace Seungmun\LaravelYandexCheckout\Contracts;

interface Product
{
    /**
     * Get the product's order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function order();

    /**
     * Product description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Product unit price.
     *
     * @return int
     */
    public function getPrice();

    /**
     * Product sold-out flag.
     *
     * @return bool
     */
    public function isSoldOut();

    /**
     * Product disabled flag.
     *
     * @return bool
     */
    public function isDisabled();
}
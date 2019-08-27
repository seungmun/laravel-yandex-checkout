<?php

namespace Seungmun\LaravelYandexCheckout\Contracts;

interface Card
{
    /**
     * Get cart number.
     *
     * @return string
     */
    public function number();

    /**
     * Get expiry year.
     *
     * @return string
     */
    public function expiryYear();

    /**
     * Get expiry month.
     *
     * @return string
     */
    public function expiryMonth();

    /**
     * Get csc number.
     *
     * @return string
     */
    public function csc();

    /**
     * Get card holder name.
     *
     * @return string
     */
    public function cardHolder();

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray();
}

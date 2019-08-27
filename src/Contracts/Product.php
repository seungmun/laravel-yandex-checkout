<?php

namespace Seungmun\LaravelYandexCheckout\Contracts;

interface Product
{
    /**
     * Product description.
     *
     * @return string
     */
    public function description();

    /**
     * Product unit price.
     *
     * @return int
     */
    public function price();
}
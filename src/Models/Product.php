<?php

namespace Seungmun\LaravelYandexCheckout\Models;

use Illuminate\Database\Eloquent\Model;
use Seungmun\LaravelYandexCheckout\Checkout;
use Seungmun\LaravelYandexCheckout\Contracts\Product as ProductContract;

abstract class Product extends Model implements ProductContract
{
    /**
     * Get the product's order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function order()
    {
        return $this->morphOne(Checkout::orderModel(), 'product');
    }

    /**
     * Product description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->name;
    }

    /**
     * Product unit price.
     *
     * @return int
     */
    public function getPrice()
    {
        return (int)$this->price;
    }

    /**
     * Product sold-out flag.
     *
     * @return bool
     */
    public function isSoldOut()
    {
        return false;
    }

    /**
     * Product disabled flag.
     *
     * @return bool
     */
    public function isDisabled()
    {
        return false;
    }
}
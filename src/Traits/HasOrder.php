<?php

namespace Seungmun\LaravelYandexCheckout\Traits;

use Seungmun\LaravelYandexCheckout\Checkout;

trait HasOrder
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
}
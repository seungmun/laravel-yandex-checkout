<?php

namespace Seungmun\LaravelYandexCheckout\Traits;

use Seungmun\LaravelYandexCheckout\Models\Order;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait Orderable
{
    /**
     * Get the product's order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function order(): MorphOne
    {
        return $this->morphOne(Order::class, 'product');
    }
}
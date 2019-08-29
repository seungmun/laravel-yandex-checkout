<?php

namespace Seungmun\LaravelYandexCheckout\Traits;

use Seungmun\LaravelYandexCheckout\Checkout;

trait HasCheckout
{
    /**
     * Get all of the user's orders.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function orders()
    {
        return $this->morphMany(Checkout::orderModel(), 'customer');
    }

    /**
     * Get all of the user's coupons.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function coupons()
    {
        return $this->morphMany(Checkout::couponModel(), 'customer');
    }
}
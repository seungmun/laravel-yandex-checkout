<?php

namespace Seungmun\LaravelYandexCheckout\Traits;

use Seungmun\LaravelYandexCheckout\Models\IssuedCoupon;

trait HasCoupon
{
    /**
     * Get all of the user(customer)'s issued coupons.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function coupons()
    {
        return $this->morphMany(IssuedCoupon::class, 'customer');
    }
}
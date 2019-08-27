<?php

namespace Seungmun\LaravelYandexCheckout\Traits;

use Seungmun\LaravelYandexCheckout\Checkout;

trait HasPayment
{
    /**
     * Get all of the user(customer)'s comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function payments()
    {
        return $this->morphMany(Checkout::paymentModel(), 'customer');
    }
}
<?php

namespace Seungmun\LaravelYandexCheckout\Traits;

use Seungmun\LaravelYandexCheckout\Models\Payment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Payable
{
    /**
     * Get all of the customer's comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'customer');
    }
}
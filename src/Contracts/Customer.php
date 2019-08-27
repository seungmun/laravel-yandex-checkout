<?php

namespace Seungmun\LaravelYandexCheckout\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Customer
{
    /**
     * Get all of the customer's comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function payments(): MorphMany;

    /**
     * Specify how the user will receive the receipt.
     * Email or Phone(Russian Mobile Phone Number ONLY)
     *
     * @return array
     */
    public function receiptReceive();
}

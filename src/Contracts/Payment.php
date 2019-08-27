<?php

namespace Seungmun\LaravelYandexCheckout\Contracts;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Payment
{
    /**
     * Get the owning customer model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function customer(): MorphTo;

    /**
     * Get the order summary record associated with the payment record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function summary(): HasOne;

    /**
     * Get a payment information as a receipt form.
     *
     * @return array
     */
    public function receipt();
}

<?php

namespace Seungmun\LaravelYandexCheckout\Contracts;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface OrderSummary
{
    /**
     * Get the payment record that owns the order summary.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment(): BelongsTo;

    /**
     * Get the orders for the order summary.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany;
}

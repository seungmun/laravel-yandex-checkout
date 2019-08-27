<?php

namespace Seungmun\LaravelYandexCheckout\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface Order
{
    /**
     * Get the order summary record that owns the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function summary(): BelongsTo;

    /**
     * Get the owning product model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function product(): MorphTo;

    /**
     * Get price of the ordered product.
     *
     * @return int
     */
    public function price();

    /**
     * Get quantity of the ordered product.
     *
     * @return int
     */
    public function quantity();

    /**
     * Get sum amount of the ordered product.
     *
     * @return int
     */
    public function amount();
}

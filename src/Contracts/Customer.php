<?php

namespace Seungmun\LaravelYandexCheckout\Contracts;

interface Customer
{
    /**
     * Get all of the user's orders.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function orders();
}
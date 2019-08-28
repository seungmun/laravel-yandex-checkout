<?php

namespace Seungmun\LaravelYandexCheckout\Contracts;

interface Customer
{
    /**
     * Get all of the user(customer)'s comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function payments();
}
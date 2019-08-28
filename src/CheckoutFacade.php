<?php

namespace Seungmun\LaravelYandexCheckout;

use Illuminate\Support\Facades\Facade;
use Seungmun\LaravelYandexCheckout\Contracts\CheckoutService;

class CheckoutFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return CheckoutService::class;
    }
}
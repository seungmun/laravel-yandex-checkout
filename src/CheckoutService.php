<?php

namespace Seungmun\LaravelYandexCheckout;

use Illuminate\Contracts\Foundation\Application;
use Seungmun\LaravelYandexCheckout\Contracts\CheckoutService as CheckoutServiceContract;

class CheckoutService implements CheckoutServiceContract
{
    /**
     * The Laravel application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Create a new checkout service instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
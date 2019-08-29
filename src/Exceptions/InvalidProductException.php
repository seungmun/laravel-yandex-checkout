<?php

namespace Seungmun\LaravelYandexCheckout\Exceptions;

use Exception;
use Seungmun\LaravelYandexCheckout\Contracts\Product;

class InvalidProductException extends Exception
{
    /**
     * Create a new invalid product exception.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Product  $product
     * @return void
     */
    public function __construct(Product $product)
    {
        $message = 'Product %s is under invalid status.';
        parent::__construct(sprintf($message, $product->getDescription()), 400, null);
    }
}
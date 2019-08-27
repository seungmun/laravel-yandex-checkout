<?php

namespace Seungmun\LaravelYandexCheckout\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Seungmun\LaravelYandexCheckout\Traits\HasOrder;
use Seungmun\LaravelYandexCheckout\Contracts\Product;

class DummyProduct extends Model implements Product
{
    use HasOrder;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dummy_products';

    /**
     * Product description.
     *
     * @return string
     */
    public function description()
    {
        return 'Dummy Product Name';
    }

    /**
     * Product unit price.
     *
     * @return int
     */
    public function price()
    {
        return 1000;
    }
}
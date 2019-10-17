<?php

namespace Seungmun\LaravelYandexCheckout\Http\Requests;

use Illuminate\Http\Request;
use Seungmun\LaravelYandexCheckout\Contracts\Product as ProductContract;

class OrderRequest extends Request
{
    /**
     * Get the attributes of the specified product model's order rules.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Product  $product
     * @param  bool  $fallback
     * @return array
     */
    public function getOrderAttributes(ProductContract $product, bool $fallback = false)
    {
        return method_exists($product, 'rules') && $fallback
            ? $this->only(array_keys(call_user_func_array([$product, 'rules'], [])))
            : $fallback ? $this->all() : [];
    }
}


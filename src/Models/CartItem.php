<?php

namespace Seungmun\LaravelYandexCheckout\Models;

use Seungmun\LaravelYandexCheckout\Contracts\Product;
use Seungmun\LaravelYandexCheckout\Contracts\CartItem as CartItemContract;

class CartItem implements CartItemContract
{
    /**
     * Product model entity.
     *
     * @var \Seungmun\LaravelYandexCheckout\Contracts\Product|\Illuminate\Database\Eloquent\Model
     */
    private $product;

    /**
     * Product order quantity.
     *
     * @var int
     */
    private $quantity;

    /**
     * Create a new cart item instance.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Product|\Illuminate\Database\Eloquent\Model  $product
     * @param  int  $quantity
     * @return void
     */
    public function __construct(Product $product, $quantity)
    {
        $this->setProduct($product)
            ->setQuantity($quantity);
    }

    /**
     * Get product of the cart item.
     *
     * @return \Seungmun\LaravelYandexCheckout\Contracts\Product|\Illuminate\Database\Eloquent\Model
     */
    public function product()
    {
        return $this->getProduct();
    }

    /**
     * Get product of the cart item.
     *
     * @return \Seungmun\LaravelYandexCheckout\Contracts\Product|\Illuminate\Database\Eloquent\Model
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set product of the cart item.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Product  $product
     * @return $this
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get quantity of the cart item.
     *
     * @return int
     */
    public function quantity()
    {
        return $this->getQuantity();
    }

    /**
     * Get quantity of the cart item.
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set quantity of the cart item.
     *
     * @param  int  $quantity
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get price of the cart item.
     *
     * @return int
     */
    public function price()
    {
        return $this->product->price();
    }

    /**
     * Get amount of the cart item.
     *
     * @return int
     */
    public function amount()
    {
        return $this->price() * $this->quantity();
    }

    /**
     * Convert the instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'product' => $this->getProduct()->toArray(),
            'quantity' => $this->getQuantity(),
        ];
    }
}
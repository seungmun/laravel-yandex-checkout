<?php

namespace Seungmun\LaravelYandexCheckout;

use Seungmun\LaravelYandexCheckout\Contracts\Product;
use Seungmun\LaravelYandexCheckout\Exceptions\CheckoutException;

class CartItem
{
    /**
     * Cart item's description.
     *
     * @var string
     */
    private $description;

    /**
     * Cart item's ordered product quantity.
     *
     * @var int
     */
    private $quantity;

    /**
     * Cart item's price.
     *
     * @var int
     */
    private $price;

    /**
     * Cart item's product model.
     *
     * @var \Seungmun\LaravelYandexCheckout\Contracts\Product|\Illuminate\Database\Eloquent\Model
     */
    private $product;

    /**
     * Create a new cart item instance.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Product  $product
     * @param  int  $quantity
     * @return void
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\CheckoutException
     */
    public function __construct(Product $product, $quantity)
    {
        $this->setProduct($product)
            ->setQuantity($quantity)
            ->setDescription($product->description())
            ->setPrice($product->price());
    }

    /**
     * Get cart item's description attribute.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set cart item's description attribute.
     *
     * @param  string  $description
     * @return \Seungmun\LaravelYandexCheckout\CartItem
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Correct if the quantity is out of range.
     *
     * @return void
     */
    protected function correctQuantity()
    {
        if ($this->quantity > 100) {
            $this->quantity = 100;
        } else if ($this->quantity < 1) {
            $this->quantity = 1;
        }
    }

    /**
     * Get cart item's quantity attribute.
     *
     * @return int
     */
    public function getQuantity()
    {
        return (int)$this->quantity;
    }

    /**
     * Set cart item's quantity attribute.
     *
     * @param  int  $quantity
     * @return \Seungmun\LaravelYandexCheckout\CartItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = (int)$quantity;
        $this->correctQuantity();

        return $this;
    }

    /**
     * Add cart item's quantity attribute.
     *
     * @param  int  $quantity
     * @return \Seungmun\LaravelYandexCheckout\CartItem
     */
    public function addQuantity($quantity)
    {
        $this->quantity += (int)$quantity;
        $this->correctQuantity();

        return $this;
    }

    /**
     * Sub cart item's quantity attribute.
     *
     * @param  int  $quantity
     * @return \Seungmun\LaravelYandexCheckout\CartItem
     */
    public function subQuantity($quantity)
    {
        $this->quantity -= (int)$quantity;
        $this->correctQuantity();

        return $this;
    }

    /**
     * Get cart item's price attribute.
     *
     * @return int
     */
    public function getPrice()
    {
        return (int)$this->price;
    }

    /**
     * Set cart item's price attribute.
     *
     * @param  int  $price
     * @return \Seungmun\LaravelYandexCheckout\CartItem
     */
    public function setPrice($price)
    {
        $this->price = (int)$price;

        return $this;
    }

    /**
     * Get cart item's product model.
     *
     * @return \Illuminate\Database\Eloquent\Model|\Seungmun\LaravelYandexCheckout\Contracts\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set cart item's product model.
     *
     * @param  \Illuminate\Database\Eloquent\Model|\Seungmun\LaravelYandexCheckout\Contracts\Product  $product
     * @return \Seungmun\LaravelYandexCheckout\CartItem
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\CheckoutException
     */
    public function setProduct($product)
    {
        if ( ! $product instanceof Product) {
            throw new CheckoutException('The given item is not a product.');
        }

        /*
        if ( ! $product->exists) {
            throw new CheckoutException('The given product does not exist.');
        }
        */

        $this->product = $product;

        return $this;
    }

    /**
     * Get cart item's amount price.
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->getPrice() * $this->getQuantity();
    }

    /**
     * Get serialization key string.
     *
     * @return string
     */
    public function hash()
    {
        $key = $this->product->getMorphClass() . ".";
        $key .= $this->product->getKey();

        return md5($key);
    }

    /**
     * Clone the current cart item.
     *
     * @return \Seungmun\LaravelYandexCheckout\CartItem
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\CheckoutException
     */
    public function clone()
    {
        return new static($this->getProduct(), $this->getQuantity());
    }

    /**
     * Convert the cart item instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'hash' => $this->hash(),
            'description' => $this->getDescription(),
            'price' => $this->getPrice(),
            'quantity' => $this->getQuantity(),
            'amount' => $this->getAmount(),
            'product' => $this->getProduct()->toArray(),
        ];
    }
}
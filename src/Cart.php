<?php

namespace Seungmun\LaravelYandexCheckout;

use Illuminate\Support\Collection;
use Seungmun\LaravelYandexCheckout\Contracts\Product;
use Seungmun\LaravelYandexCheckout\Exceptions\CheckoutException;

class Cart
{
    /**
     * Extra cart attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Cart item collection.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $items;

    /**
     * Coupons to be applied to payment.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $coupons;

    /**
     * Create a new cart instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->items = new Collection();
        $this->coupons = new Collection();
    }

    /**
     * Add a new cart item instance into the cart items collection.
     *
     * @param  mixed  $item
     * @param  int|null  $quantity
     * @return \Seungmun\LaravelYandexCheckout\Cart
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\CheckoutException
     */
    public function add($item, $quantity = null)
    {
        $item = $this->resolveCartItem($item, $quantity);

        $duplicates = $this->items->filter(function (CartItem $temp) use ($item) {
            return $temp->getProduct()->is($item->getProduct());
        });

        if ($duplicates->isNotEmpty()) {
            $this->items = $this->items->reject(function (CartItem $temp) use ($item) {
                return $temp->getProduct()->is($item->getProduct());
            });

            $quantity = $duplicates->sum(function (CartItem $item) {
                return $item->getQuantity();
            });

            $item = $item->addQuantity($quantity)->clone();
        }

        $this->items->push($item);

        return $this;
    }

    /**
     * If the specified cart item exists in the cart items collection, bring it.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Product  $product
     * @return \Illuminate\Support\Collection
     */
    public function get(Product $product)
    {
        /** @var \Illuminate\Database\Eloquent\Model $product */
        return $this->items->filter(function (CartItem $temp) use ($product) {
            return $temp->getProduct()->is($product);
        });
    }

    /**
     * Get all of the cart items in the collection as an array.
     *
     * @return array
     */
    public function all()
    {
        return $this->items->all();
    }

    /**
     * Get all of the items in the collection as a collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function items()
    {
        return $this->items;
    }

    /**
     * Count the number of cart items in the collection.
     *
     * @return int
     */
    public function count()
    {
        return $this->items->count();
    }

    /**
     * Determine if the cart items is empty or not.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->items->isEmpty();
    }

    /**
     * Determine if the cart items is not empty.
     *
     * @return bool
     */
    public function isNotEmpty()
    {
        return ! $this->isEmpty();
    }

    /**
     * Convert the cart instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'count' => $this->items->count(),
            'items' => $this->items->map(function (CartItem $item) {
                return $item->toArray();
            }),
            'amount' => $this->items->sum(function (CartItem $item) {
                return $item->getAmount();
            }),
        ];
    }

    /**
     * Resolve the specified item variable to cart item.
     *
     * @param  mixed  $item
     * @param  int|null  $quantity
     * @return \Seungmun\LaravelYandexCheckout\CartItem
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\CheckoutException
     */
    protected function resolveCartItem($item, $quantity = null)
    {
        if ($item instanceof Product) {
            $item = new CartItem($item, $quantity ?? 1);
        } else if ($item instanceof CartItem) {
            $item = $item->clone();
        } else {
            throw new CheckoutException('Unacceptable cart item entity.');
        }

        return $item;
    }
}
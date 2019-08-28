<?php

namespace Seungmun\LaravelYandexCheckout;

use Illuminate\Support\Collection;
use Seungmun\LaravelYandexCheckout\Contracts\Product;
use Seungmun\LaravelYandexCheckout\Models\IssuedCoupon;
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
     * Single-Product model indicator.
     *
     * @var string
     */
    protected $indicator = null;

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

        if ( ! is_null($this->indicator) && $this->indicator !== $item->getProduct()->getMorphClass()) {
            throw new CheckoutException('Only the same kind of products are available for addition.');
        }

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

        $this->indicator = $item->getProduct()->getMorphClass();
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
     * Remove all cart items.
     *
     * @return \Seungmun\LaravelYandexCheckout\Cart
     */
    public function clear()
    {
        $this->items = new Collection();
        $this->indicator = null;

        return $this;
    }

    /**
     * Get cart item indicator string or model.
     *
     * @param  bool  $asModel
     * @return \Seungmun\LaravelYandexCheckout\Contracts\Product|\Illuminate\Database\Eloquent\Model|string
     */
    public function getIndicator($asModel = false)
    {
        $class = $this->indicator;

        return $asModel ? new $class : $class;
    }

    /**
     * Convert the cart instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'indicator' => $this->indicator,
            'count' => $this->items->count(),
            'amount' => $this->getAmount(),
            'discount' => $this->getDiscount(),
            'total_amount' => $this->getTotalAmount(),
            'items' => $this->items->map(function (CartItem $item) {
                return $item->toArray();
            }),
            'coupons' => $this->coupons->map(function (IssuedCoupon $coupon) {
                return $coupon->toArray();
            }),
        ];
    }

    /**
     * Get sum amount of cart items.
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->items->sum(function (CartItem $item) {
            return $item->getAmount();
        });
    }

    /**
     * Get discounted total amount of cart items.
     *
     * @return int
     */
    public function getTotalAmount()
    {
        $amount = $this->getAmount() - $this->getDiscount();

        return $amount < 0 ? 0 : $amount;
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

    /**
     * Add a coupon into coupon collection.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Models\IssuedCoupon  $coupon
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\CheckoutException
     */
    public function addCoupon(IssuedCoupon $coupon)
    {
        $validator = new CouponValidator($coupon, $this->indicator);
        $validator->validate();

        $this->coupons = new Collection();
        $this->coupons->push($coupon);
    }

    /**
     * Get sum of discount price of all items.
     *
     * @return int
     */
    public function getDiscount()
    {
        $issuedCoupon = $this->coupons->first();

        $discount = $issuedCoupon->coupon->type === 'fixed'
            ? $issuedCoupon->coupon->value
            : $this->getAmount() * (int)$issuedCoupon->coupon->value / 100;

        return (int)$discount;
    }
}
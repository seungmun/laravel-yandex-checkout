<?php

namespace Seungmun\LaravelYandexCheckout;

use Illuminate\Support\Facades\DB;
use Seungmun\LaravelYandexCheckout\Models\Order;
use Seungmun\LaravelYandexCheckout\Models\Payment;
use Seungmun\LaravelYandexCheckout\Models\CartItem;
use Seungmun\LaravelYandexCheckout\Models\OrderSummary;
use Seungmun\LaravelYandexCheckout\Exceptions\CartException;
use Seungmun\LaravelYandexCheckout\Contracts\Cart as CartContract;
use Seungmun\LaravelYandexCheckout\Contracts\Order as OrderContract;
use Seungmun\LaravelYandexCheckout\Contracts\Product as ProductContract;
use Seungmun\LaravelYandexCheckout\Contracts\Customer as CustomerContract;

class Cart implements CartContract
{
    /**
     * Cart product items collection.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $products;

    /**
     * Coupon collection.
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
        $this->products = collect();
    }

    /**
     * Make a payment of the specified user with currently added cart items.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Customer  $user
     * @param  array  $extra
     * @return \Seungmun\LaravelYandexCheckout\Contracts\Payment
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\CartException
     */
    public function makePayment(CustomerContract $user, array $extra = [])
    {
        // Step 1. Check if the cart is empty.
        if ($this->isEmpty()) {
            throw new CartException('장바구니가 비어있습니다.');
        }

        // Step 2. Check if the cart has product that has issued.
        $issues = $this->products->filter(function (CartItem $item) {
            return $item->product()->isSoldOut() || ! $item->product()->canBuy();
        });

        if ($issues->isNotEmpty()) {
            throw new CartException('구매할 수 없는 상품이 존재합니다.');
        }

        // Step 3. Make a payment transaction.
        $payment = DB::transaction(function () use ($user, $extra) {
            $payment = new Payment;
            $payment->save();

            $summary = new OrderSummary;
            $summary->description = $this->expectsDescription();
            $summary->extra = $extra;
            $summary->save();
            $payment->summary()->save($summary);

            $orders = $this->products->map(function (CartItem $item) use ($summary) {
                $order = new Order;
                $order->price = $item->price();
                $order->quantity = $item->quantity();
                $order->amount = $item->amount();
                $order->save();
                $order->product()->associate($item->product());
                $summary->orders()->save($order);

                return $order;
            });

            $summary->update([
                'amount' => $orders->sum(function (OrderContract $order) {
                    return $order->amount();
                }),
            ]);

            // Save a created payment to user.
            $user->payments()->save($payment);

            // Clear the cart items.
            $this->clear();

            return $payment;
        });

        return $payment;
    }

    /**
     * Determine if the cart is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->products->isEmpty();
    }

    /**
     * Determine if the cart is not empty.
     *
     * @return bool
     */
    public function isNotEmpty()
    {
        return ! $this->isEmpty();
    }

    /**
     * Add a specified product into cart.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Product  $product
     * @param  int  $quantity
     * @return $this
     */
    public function add(ProductContract $product, $quantity)
    {
        $product = new CartItem($product, $quantity);
        $this->products->push($product);

        return $this;
    }

    /**
     * Remove a specified product from cart.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Product  $product
     * @return $this
     */
    public function remove(ProductContract $product)
    {
        $this->products = $this->products
            ->reject(function (CartItem $item) use ($product) {
                /** @var \Illuminate\Database\Eloquent\Model $temp */
                /** @var \Illuminate\Database\Eloquent\Model $product */
                $temp = $item->product();

                return $temp->is($product);
            });

        return $this;
    }

    /**
     * Retrieve all products in the cart.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        return $this->products;
    }

    /**
     * Clear all products in the cart.
     *
     * @return $this
     */
    public function clear()
    {
        $this->products = collect();

        return $this;
    }

    /**
     * Expects the description of the cart.
     *
     * @return string
     */
    protected function expectsDescription()
    {
        $description = $this->products->first()->product()->title();

        if ($this->products->count() > 1) {
            $description .= "외 " . ($this->products->count() - 1) . "건";
        } else {
            $quantity = $this->products->first()->quantity();
            $description .= $quantity > 1 ? "(" . $quantity . "개)" : '';
        }

        return $description;
    }
}

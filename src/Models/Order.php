<?php

namespace Seungmun\LaravelYandexCheckout\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Seungmun\LaravelYandexCheckout\Checkout;
use Illuminate\Database\Eloquent\SoftDeletes;
use Seungmun\LaravelYandexCheckout\Bridge\CreatePayment;
use Seungmun\LaravelYandexCheckout\Exceptions\CouponException;
use Seungmun\LaravelYandexCheckout\Exceptions\InvalidProductException;

class Order extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'orders';

    /**
     * The guarded attributes on the model.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'description' => '-',
        'extra' => '[]',
        'amount' => 0,
        'discount' => 0,
        'total_amount' => 0,
        'status' => 'pending',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'integer',
        'discount' => 'integer',
        'total_amount' => 'integer',
        'extra' => 'array',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            $model->uuid = Str::uuid();
            $model->description = '-';
            $model->extra = [];
            $model->status = 'pending';
        });
    }

    /**
     * Get the payment associated with the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function payment()
    {
        return $this->hasOne(Checkout::paymentModel());
    }

    /**
     * Get the order products for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderProducts()
    {
        return $this->hasMany(Checkout::orderProductModel());
    }

    /**
     * Get the coupons for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function coupons()
    {
        return $this->hasMany(Checkout::couponModel());
    }

    /**
     * Get the owning user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function user()
    {
        return $this->morphTo('customer');
    }

    /**
     * Add a new order product into the order.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Models\Product  $product
     * @param  int  $quantity
     * @return \Seungmun\LaravelYandexCheckout\Models\Order
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\InvalidProductException
     */
    public function addProduct(Product $product, int $quantity)
    {
        if ($product->isSoldOut() || $product->isDisabled()) {
            throw new InvalidProductException($product);
        }

        $item = Checkout::orderProduct();
        $item->price = $product->getPrice();
        $item->quantity = $quantity;
        $item->amount = $product->getPrice() * $quantity;
        $item->product()->associate($product);
        $this->orderProducts()->save($item);

        $this->expectsDescription();

        // Todo: Re-generate all amount prices.
        $this->increment('amount', $item->getAmount());
        $this->increment('total_amount', $item->getAmount());

        return $this;
    }

    /**
     * Add a new payment into the order.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Models\Payment  $payment
     * @return $this
     */
    public function addPayment(Payment $payment)
    {
        $this->payment()->save($payment);

        return $this;
    }

    /**
     * Add a new coupon into the order.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Models\Coupon  $coupon
     * @return \Seungmun\LaravelYandexCheckout\Models\Order
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\CouponException
     */
    public function addCoupon(Coupon $coupon)
    {
        /** @var \Illuminate\Support\Collection $items */
        $items = $this->orderProducts->filter(function (OrderProduct $item) use ($coupon) {
            return $coupon->isSuitable($item->product->getMorphClass());
        });

        if ($this->coupons->count() >= 1) {
            throw new CouponException('Already a coupon has been suited.');
        }

        if ($items->isEmpty()) {
            throw new CouponException('Coupon could not suit on this order.');
        }

        if ($coupon->isUsed() || $coupon->isExpired()) {
            throw new CouponException('Expired or already used coupon.');
        }

        $coupon->order()->associate($this);
        $coupon->update(['used_at' => new Carbon()]);

        $discount = $coupon->couponType->type === 'fixed'
            ? $coupon->couponType->value
            : $this->getAmount() * (int)$coupon->couponType->value / 100;

        // Todo: Re-generate all amount prices.
        $this->increment('discount', $discount);
        $this->decrement('total_amount', $discount);

        return $this;
    }

    /**
     * Expects and update the order description.
     *
     * @return void
     */
    protected function expectsDescription()
    {
        $items = $this->orderProducts()
            ->with('product')
            ->get();

        $description = $items->first()->getDescription();

        if ($items->count() > 1) {
            $description .= "외 " . ($items->count() - 1) . "건";
        } else {
            $quantity = $items->first()->getQuantity();
            $description .= $quantity > 1 ? "(" . $quantity . "개)" : '';
        }

        $this->update(['description' => $description]);
    }

    /**
     * Get description of the order.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get extra attributes of the order.
     *
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * Set extra attribute.
     *
     * @param  array  $attributes
     * @return \Seungmun\LaravelYandexCheckout\Models\Order
     */
    public function setExtra(array $attributes)
    {
        $this->update(['extra' => $attributes]);

        return $this;
    }

    /**
     * Determine if order has payment.
     *
     * @return bool
     */
    public function hasPayment()
    {
        return (bool)$this->payment()->exists();
    }

    /**
     * Determine if order is paid.
     *
     * @return bool
     */
    public function isPaid()
    {
        return $this->hasPayment() && $this->payment->is_paid;
    }

    /**
     * Get amount of the order.
     *
     * @return int
     */
    public function getAmount()
    {
        return (int)$this->amount;
    }

    /**
     * Get discount amount of the order.
     *
     * @return int
     */
    public function getDiscount()
    {
        return (int)$this->discount;
    }

    /**
     * Get total amount of order.
     *
     * @return int
     */
    public function getTotalAmount()
    {
        return (int)$this->total_amount;
    }

    /**
     * Get order as a receipt.
     *
     * @return array
     */
    public function receipt()
    {
        return [
            'description' => $this->getDescription(),
            'amount' => $this->getAmount(),
            'discount' => $this->getDiscount(),
            'total_amount' => $this->getTotalAmount(),
            'items' => $this->orderProducts()->with('product')->get(),
            'coupons' => $this->coupons()->with('couponType')->get(),
            'extra' => $this->getExtra(),
        ];
    }

    /**
     * Convert order model to payment payload.
     *
     * @return \Seungmun\LaravelYandexCheckout\Bridge\CreatePayment
     */
    public function payload()
    {
        return new CreatePayment($this);
    }

    /**
     * Determine if order need to purchase.
     *
     * @return bool
     */
    public function shouldPurchase()
    {
        return $this->getTotalAmount() !== 0;
    }

    /**
     * Determine if order need not to purchase.
     *
     * @return bool
     */
    public function shouldNotPurchase()
    {
        return ! $this->shouldPurchase();
    }
}

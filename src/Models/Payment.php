<?php

namespace Seungmun\LaravelYandexCheckout\Models;

use Illuminate\Support\Str;
use Konekt\Enum\Eloquent\CastsEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Seungmun\LaravelYandexCheckout\Contracts\Payment as PaymentContract;

class Payment extends Model implements PaymentContract
{
    use SoftDeletes, CastsEnums;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'shop_key',
        'is_paid',
        'status',
        'captured_at',
        'expires_at',
        'expires_at',
        'response',
    ];

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'uuid' => null,
        'is_paid' => false,
        'captured_at' => null,
        'expires_at' => null,
        'status' => PaymentStatus::__default,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_paid' => 'boolean',
        'response' => 'array',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'captured_at',
        'expires_at',
    ];

    /**
     * The attributes that should be cast to enum types.
     *
     * @var array
     */
    protected $enums = [
        'status' => PaymentStatus::class,
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
    }

    /**
     * Get the owning customer model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function customer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the order summary record associated with the payment record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function summary(): HasOne
    {
        return $this->hasOne(OrderSummary::class);
    }

    /**
     * Get the order summary's orders.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function orders(): HasOneThrough
    {
        return $this->hasOneThrough(Order::class, OrderSummary::class);
    }

    /**
     * Get a payment information as a receipt form.
     *
     * @return array
     */
    public function receipt()
    {
        $summary = $this->summary()
            ->with('orders.product')
            ->first();

        $summary = [
            'description' => $summary->description,
            'amount' => $summary->amount,
            'orders' => $summary->orders
                ->map(function (Order $order) {
                    return [
                        'description' => $order->product->title(),
                        'price' => $order->price(),
                        'quantity' => $order->quantity(),
                        'amount' => $order->amount(),
                    ];
                }),
        ];

        return $summary;
    }
}

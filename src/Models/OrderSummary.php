<?php

namespace Seungmun\LaravelYandexCheckout\Models;

use Illuminate\Database\Eloquent\Model;
use Seungmun\LaravelYandexCheckout\Checkout;

class OrderSummary extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'order_summaries';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

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
        'total_paid' => 0,
        'refunded_amount' => 0,
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
        'total_paid' => 'integer',
        'refunded_amount' => 'integer',
        'extra' => 'array',
    ];

    /**
     * Get the payment record that owns the order summary.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment()
    {
        return $this->belongsTo(Checkout::paymentModel());
    }

    /**
     * Get the orders for the order summary.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Checkout::orderModel());
    }

    /**
     * The issued coupons that belong to the order summary.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function issuedCoupons()
    {
        return $this->belongsToMany(IssuedCoupon::class)
            ->withPivot(['discount']);
    }
}
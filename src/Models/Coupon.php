<?php

namespace Seungmun\LaravelYandexCheckout\Models;

use Illuminate\Database\Eloquent\Model;
use Seungmun\LaravelYandexCheckout\Checkout;

class Coupon extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'coupons';

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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'used_at',
        'issued_at',
        'expires_at',
    ];

    /**
     * Get the coupon type that owns the coupon.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function couponType()
    {
        return $this->belongsTo(CouponType::class);
    }

    /**
     * Get the order that owns the coupon.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Checkout::orderModel());
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
     * Determine if the issued coupon is suitable for indicator model.
     *
     * @param  string  $indicator
     * @return bool
     */
    public function isSuitable($indicator)
    {
        return is_null($this->couponType->target) ||
            ( ! is_null($this->couponType->target) && $this->couponType->target === $indicator);
    }

    /**
     * Determine if the issued coupon in already expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        return ! is_null($this->expires_at) && $this->expires_at->isPast();
    }

    /**
     * Determine if the issued coupon is already used.
     *
     * @return bool
     */
    public function isUsed()
    {
        return ! is_null($this->used_at);
    }
}
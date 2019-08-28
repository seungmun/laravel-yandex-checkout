<?php

namespace Seungmun\LaravelYandexCheckout\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Seungmun\LaravelYandexCheckout\Checkout;

class IssuedCoupon extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'issued_coupons';

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
        'expires_at' => null,
        'used_at' => null,
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'expires_at',
        'used_at',
        'issued_at',
    ];

    /**
     * Get the coupon of the issued coupon.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coupon()
    {
        return $this->belongsTo(Checkout::couponModel());
    }

    /**
     * Get the owning user(customer) model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function user()
    {
        return $this->morphTo('customer');
    }

    /**
     * Scope a query to only include un-used issued coupons.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnUsed(Builder $query)
    {
        return $query->whereNull('issued_at');
    }

    /**
     * Scope a query to only include already used issued coupons.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsed(Builder $query)
    {
        return $query->whereNotNull('issued_at');
    }

    /**
     * Scope a query to only include non-expires issued coupons.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNonExpires(Builder $query)
    {
        return $query->whereNull('expires_at');
    }

    /**
     * Scope a query to only include will expires issued coupons.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpires(Builder $query)
    {
        return $query->whereNotNull('expires_at');
    }

    /**
     * Scope a query to only include un-expired issued coupons.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Exception
     */
    public function scopeUnExpired(Builder $query)
    {
        return $query->where('expires_at', '>', new Carbon());
    }

    /**
     * Scope a query to only include already expired issued coupons.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Exception
     */
    public function scopeExpired(Builder $query)
    {
        return $query->where('expires_at', '<', new Carbon());
    }

    /**
     * Determine if the issued coupon is suitable for indicator model.
     *
     * @param  string  $indicator
     * @return bool
     */
    public function isSuitable($indicator)
    {
        return is_null($this->coupon->target) ||
            ( ! is_null($this->coupon->target) && $this->coupon->target === $indicator);
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
<?php

namespace Seungmun\LaravelYandexCheckout\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Coupon extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'coupons';

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
        'target' => null,
        'description' => null,
        'quantity' => null,
        'is_reusable' => false,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'target' => 'string',
        'quantity' => 'integer',
        'value' => 'integer',
        'is_reusable' => 'boolean',
    ];

    /**
     * Get all of the issued coupons for the coupon.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function issues()
    {
        return $this->hasMany(IssuedCoupon::class);
    }

    /**
     * Scope a query to only include null target coupons.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNonTarget(Builder $query)
    {
        return $query->whereNull('target');
    }

    /**
     * Scope a query to only include coupons of a given target.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $target
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfTarget(Builder $query, string $target)
    {
        return $query->where('target', $target);
    }

    /**
     * Scope a query to only include unlimited quantity coupons.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnlimited(Builder $query)
    {
        return $query->whereNull('quantity');
    }

    /**
     * Scope a query to only include coupons of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType(Builder $query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include type of fixed coupons.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfFixed(Builder $query)
    {
        return $query->where('type', 'fixed');
    }

    /**
     * Scope a query to only include type of percentage coupons.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfPercentage(Builder $query)
    {
        return $query->where('type', 'percentage');
    }

    /**
     * Scope a query to only include re-usable coupons.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReusable(Builder $query)
    {
        return $query->where('is_reusable', true);
    }

    /**
     * Scope a query to only include no re-usable coupons.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotReusable(Builder $query)
    {
        return $query->where('is_reusable', false);
    }

    /**
     * Scope a query to only include will expires coupons.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpires(Builder $query)
    {
        return $query->whereNotNull('expiry');
    }

    /**
     * Scope a query to only include will not expires coupons.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNonExpires(Builder $query)
    {
        return $query->whereNull('expiry');
    }
}
<?php

namespace Seungmun\LaravelYandexCheckout\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Seungmun\LaravelYandexCheckout\Checkout;

class CouponType extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'coupon_types';

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
        'type' => 'fixed',
        'is_reusable' => false,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
        'value' => 'integer',
        'is_reusable' => 'boolean',
    ];

    /**
     * Get the coupons for the coupon type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function coupons()
    {
        return $this->hasMany(Checkout::couponModel());
    }
}
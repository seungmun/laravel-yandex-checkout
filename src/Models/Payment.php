<?php

namespace Seungmun\LaravelYandexCheckout\Models;

use Illuminate\Database\Eloquent\Model;
use Seungmun\LaravelYandexCheckout\Checkout;

class Payment extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payments';

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
        'uuid' => null,
        'is_paid' => false,
        'captured_at' => null,
        'expires_at' => null,
        'status' => 'pending',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_paid' => 'boolean',
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
     * Get the owning user(customer) model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function user()
    {
        return $this->morphTo('customer');
    }

    /**
     * Get the order summary record associated with the payment record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function summary()
    {
        return $this->hasOne(Checkout::orderSummaryModel());
    }

    /**
     * Get the order summary's orders.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function orders()
    {
        return $this->hasOneThrough(Checkout::orderModel(), Checkout::orderSummaryModel());
    }
}
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
        'is_paid' => false,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'total_paid' => 'integer',
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
     * Get the order type that owns the payment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Checkout::orderModel());
    }

    /**
     * Get confirmation url attribute.
     *
     * @return string
     */
    public function getConfirmationUrlAttribute()
    {
        return config('laravel-yandex-checkout.confirmation_url_prefix') . $this->uuid;
    }
}
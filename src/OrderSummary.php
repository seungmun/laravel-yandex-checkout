<?php

namespace Seungmun\LaravelYandexCheckout;

use Illuminate\Database\Eloquent\Model;

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
        'total_paid' => 'integer',
        'refunded_amount' => 'integer',
        'extra' => 'array',
    ];
}
<?php

namespace Seungmun\LaravelYandexCheckout\Models;

use Illuminate\Database\Eloquent\Model;
use Seungmun\LaravelYandexCheckout\Checkout;

class OrderProduct extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'order_products';

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
        'is_refunded' => false,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'integer',
        'quantity' => 'integer',
        'amount' => 'integer',
        'is_refunded' => 'boolean',
    ];

    /**
     * Get the owning product model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function product()
    {
        return $this->morphTo('product');
    }

    /**
     * Get the order that owns the order product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Checkout::orderModel());
    }

    /**
     * Get product's description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->product->getDescription();
    }

    /**
     * Get price of ordered product.
     *
     * @return int
     */
    public function getPrice()
    {
        return (int)$this->price;
    }

    /**
     * Get quantity of ordered product.
     *
     * @return int
     */
    public function getQuantity()
    {
        return (int)$this->quantity;
    }

    /**
     * Get amount of ordered product.
     *
     * @return int
     */
    public function getAmount()
    {
        return (int)$this->amount;
    }
}
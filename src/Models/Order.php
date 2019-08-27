<?php

namespace Seungmun\LaravelYandexCheckout\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Seungmun\LaravelYandexCheckout\Contracts\Order as OrderContract;

class Order extends Model implements OrderContract
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'price',
        'quantity',
        'amount',
        'is_refunded',
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
     * Get the order summary record that owns the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function summary(): BelongsTo
    {
        return $this->belongsTo(OrderSummary::class);
    }

    /**
     * Get the owning product model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function product(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get price of the ordered product.
     *
     * @return int
     */
    public function price()
    {
        return $this->product->price();
    }

    /**
     * Get quantity of the ordered product.
     *
     * @return int
     */
    public function quantity()
    {
        return $this->quantity;
    }

    /**
     * Get sum amount of the ordered product.
     *
     * @return int
     */
    public function amount()
    {
        return $this->price() * $this->quantity();
    }
}
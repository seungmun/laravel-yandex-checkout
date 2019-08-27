<?php

namespace Seungmun\LaravelYandexCheckout\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\SchemalessAttributes\SchemalessAttributes;
use Seungmun\LaravelYandexCheckout\Contracts\OrderSummary as OrderSummaryContract;

class OrderSummary extends Model implements OrderSummaryContract
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'amount',
        'total_paid',
        'refunded_amount',
        'extra',
    ];

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

    /**
     * Get the payment record that owns the order summary.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the orders for the order summary.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get extra attribute.
     *
     * @return \Spatie\SchemalessAttributes\SchemalessAttributes
     */
    public function getExtraAttributesAttribute(): SchemalessAttributes
    {
        return SchemalessAttributes::createForModel($this, 'extra');
    }

    /**
     * Scope of extra attribute.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithExtraAttributes(): Builder
    {
        return SchemalessAttributes::scopeWithSchemalessAttributes('extra');
    }
}

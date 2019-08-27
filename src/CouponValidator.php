<?php

namespace Seungmun\LaravelYandexCheckout;

use Seungmun\LaravelYandexCheckout\Models\IssuedCoupon;
use Seungmun\LaravelYandexCheckout\Exceptions\CheckoutException;

class CouponValidator
{
    /**
     * Coupon indicator string.
     *
     * @var string
     */
    protected $indicator;

    /**
     * Coupon model instance.
     *
     * @var \Seungmun\LaravelYandexCheckout\Contracts\Coupon|\Seungmun\LaravelYandexCheckout\Models\Coupon
     */
    protected $coupon;

    /**
     * Issued coupon model instance.
     *
     * @var \Seungmun\LaravelYandexCheckout\Models\IssuedCoupon
     */
    protected $issued;

    /**
     * Coupon available indicator.
     *
     * @var bool
     */
    public $valid = false;

    /**
     * Coupon unavailable indicator.
     *
     * @var bool
     */
    public $invalid = false;

    /**
     * Create a new coupon validator instance.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Models\IssuedCoupon  $issued
     * @param  string  $indicator
     * @return void
     */
    public function __construct(IssuedCoupon $issued, string $indicator)
    {
        $issued->load('coupon');

        $this->issued = $issued;
        $this->coupon = $issued->coupon;

        $this->indicator = $indicator;
    }

    /**
     * Validate issued coupon is available.
     *
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\CheckoutException
     */
    public function validate()
    {
        if ( ! $this->issued->isSuitable($this->indicator)) {
            $this->handleError(false);
            throw new CheckoutException('Product and Coupon indicator miss-match.');
        }

        if ($this->issued->isUsed() || $this->issued->isExpired()) {
            $this->handleError(false);
            throw new CheckoutException('Expired or already used coupon.');
        }

        $this->handleError(true);
    }

    /**
     * Determine if validation is valid.
     *
     * @return bool
     */
    public function determine()
    {
        try {
            $this->validate();
        } catch (CheckoutException $e) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the coupon is valid.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->valid === true && $this->invalid === false;
    }

    /**
     * Determine if the coupon is invalid.
     *
     * @return bool
     */
    public function invalid()
    {
        return ! $this->valid();
    }

    /**
     * Handle error indicator.
     *
     * @param  bool  $success
     * @return void
     */
    protected function handleError($success)
    {
        $this->valid = ! ! $success;
        $this->invalid = ! ! ! $success;
    }
}
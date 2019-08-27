<?php

namespace Seungmun\LaravelYandexCheckout;

class Checkout
{
    /**
     * Indicates if LaravelYandexCheckout migrations will be run.
     *
     * @var bool
     */
    public static $runsMigrations = true;

    /**
     * The checkout model class name.
     *
     * @var string
     */
    public static $paymentModel = 'Seungmun\LaravelYandexCheckout\Payment';

    /**
     * The order summary model class name.
     *
     * @var string
     */
    public static $orderSummaryModel = 'Seungmun\LaravelYandexCheckout\OrderSummary';

    /**
     * The order model class name.
     *
     * @var string
     */
    public static $orderModel = 'Seungmun\LaravelYandexCheckout\Order';

    /**
     * Set the payment model class name.
     *
     * @param  string  $paymentModel
     * @return void
     */
    public static function usePaymentModel($paymentModel)
    {
        static::$paymentModel = $paymentModel;
    }

    /**
     * Get the payment model class name.
     *
     * @return string
     */
    public static function paymentModel()
    {
        return static::$paymentModel;
    }

    /**
     * Get a new payment model instance.
     *
     * @return \Seungmun\LaravelYandexCheckout\Payment
     */
    public static function payment()
    {
        return new static::$paymentModel;
    }

    /**
     * Set the order summary model class name.
     *
     * @param  string  $orderSummaryModel
     * @return void
     */
    public static function useOrderSummaryModel($orderSummaryModel)
    {
        static::$orderSummaryModel = $orderSummaryModel;
    }

    /**
     * Get the order summary model class name.
     *
     * @return string
     */
    public static function orderSummaryModel()
    {
        return static::$orderSummaryModel;
    }

    /**
     * Get a new order summary model instance.
     *
     * @return \Seungmun\LaravelYandexCheckout\OrderSummary
     */
    public static function orderSummary()
    {
        return new static::$orderSummaryModel;
    }

    /**
     * Set the order model class name.
     *
     * @param  string  $orderModel
     * @return void
     */
    public static function useOrderModel($orderModel)
    {
        static::$orderModel = $orderModel;
    }

    /**
     * Get the order model class name.
     *
     * @return string
     */
    public static function orderModel()
    {
        return static::$orderModel;
    }

    /**
     * Get a new order model instance.
     *
     * @return \Seungmun\LaravelYandexCheckout\Order
     */
    public static function order()
    {
        return new static::$orderModel;
    }

    /**
     * Configure LaravelYandexCheckout to not register its migrations.
     *
     * @return static
     */
    public static function ignoreMigrations()
    {
        static::$runsMigrations = false;

        return new static;
    }
}
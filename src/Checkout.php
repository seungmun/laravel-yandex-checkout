<?php

namespace Seungmun\LaravelYandexCheckout;

use Illuminate\Support\Facades\Route;
use Seungmun\LaravelYandexCheckout\Traits\HandlesPayment;

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
    public static $paymentModel = 'Seungmun\LaravelYandexCheckout\Models\Payment';

    /**
     * The order summary model class name.
     *
     * @var string
     */
    public static $orderSummaryModel = 'Seungmun\LaravelYandexCheckout\Models\OrderSummary';

    /**
     * The order model class name.
     *
     * @var string
     */
    public static $orderModel = 'Seungmun\LaravelYandexCheckout\Models\Order';

    /**
     * The coupon model class name.
     *
     * @var string
     */
    public static $couponModel = 'Seungmun\LaravelYandexCheckout\Models\Coupon';

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
     * @return \Seungmun\LaravelYandexCheckout\Models\Payment
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
     * @return \Seungmun\LaravelYandexCheckout\Models\OrderSummary
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
     * @return \Seungmun\LaravelYandexCheckout\Models\Order
     */
    public static function order()
    {
        return new static::$orderModel;
    }

    /**
     * Set the coupon model class name.
     *
     * @param  string  $couponModel
     * @return void
     */
    public static function useCouponModel($couponModel)
    {
        static::$couponModel = $couponModel;
    }

    /**
     * Get the coupon model class name.
     *
     * @return string
     */
    public static function couponModel()
    {
        return static::$couponModel;
    }

    /**
     * Get a new coupon model instance.
     *
     * @return \Seungmun\LaravelYandexCheckout\Models\Coupon
     */
    public static function coupon()
    {
        return new static::$couponModel;
    }

    /**
     * Binds the LaravelYandexCheckout routes into the controller.
     *
     * @param  callable|null  $callback
     * @param  array  $options
     * @return void
     */
    public static function routes($callback = null, array $options = [])
    {
        $callback = $callback ?: function ($router) {
            /** @var \Seungmun\LaravelYandexCheckout\RouteRegistrar $router */
            $router->all();
        };

        $defaultOptions = [
            // 'prefix' => 'checkout',
            'namespace' => '\Seungmun\LaravelYandexCheckout\Http\Controllers',
        ];

        $options = array_merge($defaultOptions, $options);

        Route::group($options, function ($router) use ($callback) {
            $callback(new RouteRegistrar($router));
        });
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
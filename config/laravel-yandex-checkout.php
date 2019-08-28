<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default YandexCheckout Shop Name
    |--------------------------------------------------------------------------
    */

    'default' => env('CHECKOUT_SHOP', 'default'),

    /*
    |--------------------------------------------------------------------------
    | YandexCheckout Shops
    |--------------------------------------------------------------------------
    */

    'shops' => [

        'default' => [
            'id' => env('CHECKOUT_SHOP_KEY'),
            'secret' => env('CHECKOUT_SHOP_SECRET'),
        ],

        'example' => [
            'id' => 'example-shop-id',
            'secret' => 'example-shop-secret',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Payment confirmation type.
    |--------------------------------------------------------------------------
    |
    | The scenario in which the user is redirected to the Yandex.
    | Checkout's web page to confirm the payment.
    |
    */

    'confirmation_type' => 'redirect',

    /*
    |--------------------------------------------------------------------------
    | Payment confirmation locale. (ru_RU, en_US)
    |--------------------------------------------------------------------------
    |
    | The language of the interface, emails, and text messages
    | that will be displayed and sent to the user.
    | Formatted in accordance with ISO/IEC 15897.
    |
    */

    'confirmation_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Payment confirmation enforcement.
    |--------------------------------------------------------------------------
    |
    | A request for making a payment with authentication by 3-D Secure.
    | It works if you accept bank card payments without user's confirmation by default.
    | In other cases, the 3-D Secure authentication will be handled by Yandex.Checkout.
    | If you would like to accept payment without additional confirmation by the user,
    | contact your Yandex.Checkout manager.
    |
    */

    'confirmation_enforcement' => false,

    /*
    |--------------------------------------------------------------------------
    | Payment confirmation return url.
    |--------------------------------------------------------------------------
    |
    | The URL that the user will return to after
    | confirming or canceling the payment on the web page.
    |
    */

    'confirmation_return_url' => '/api/payment/success',

    /*
    |--------------------------------------------------------------------------
    | Payment confirmation url.
    |--------------------------------------------------------------------------
    |
    | The URL that the user have to redirect to for payment.
    |
    */

    'confirmation_url_prefix' => 'https://money.yandex.ru/payments/external/confirmation?orderId=',

];

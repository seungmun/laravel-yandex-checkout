<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Yandex Checkout Shop(Store) Identification ID
    |--------------------------------------------------------------------------
    |
    | Check in https://kassa.yandex.ru/my/shop-settings this url.
    |
    */

    'shop_id' => env('YANDEX_CHECKOUT_SHOP_ID'),

    /*
    |--------------------------------------------------------------------------
    | Secret key for API
    |--------------------------------------------------------------------------
    |
    | Check in https://kassa.yandex.ru/my/shop-settings this url.
    |
    */

    'secret_key' => env('YANDEX_CHECKOUT_SECRET_KEY'),

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
    | Payment confirmation locale.
    |--------------------------------------------------------------------------
    |
    | The language of the interface, emails, and text messages that will be displayed and sent to the user.
    | Formatted in accordance with ISO/IEC 15897.
    | Possible values: ru_RU, en_US.
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
    | The URL that the user will return to after confirming or canceling the payment on the web page.
    |
    */

    'confirmation_return_url' => '/api/payment/return',

    /*
    |--------------------------------------------------------------------------
    | Auto web hook registration.
    |--------------------------------------------------------------------------
    |
    | Automatic register routes for web hooks.
    |
    */

    'webhook_routes' => true,

];

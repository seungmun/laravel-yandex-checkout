<?php

namespace Seungmun\LaravelYandexCheckout;

use Illuminate\Contracts\Routing\Registrar as Router;

class RouteRegistrar
{
    /**
     * The router implementation.
     *
     * @var \Illuminate\Contracts\Routing\Registrar
     */
    protected $router;

    /**
     * Create a new route registrar instance.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar  $router
     * @return void
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Register routes for webhooks.
     *
     * @return void
     */
    public function all()
    {
        $this->forWebhooks();
    }

    /**
     * Register the routes needed for webhooks.
     *
     * @return void
     */
    public function forWebhooks()
    {
        $this->router->group(['middleware' => ['web', 'guest']], function ($router) {
            $router->any('/webhook/yandex', [
                'uses' => 'WebhookController@yandex',
                'as' => 'checkout.webhooks.yandex',
            ]);
        });
    }
}
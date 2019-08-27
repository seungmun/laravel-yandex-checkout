<?php

namespace Seungmun\LaravelYandexCheckout\Contracts;

interface YandexCheckout
{
    /**
     * Set authorization data.
     *
     * @param  string  $login
     * @param  string  $password
     * @return \Seungmun\LaravelYandexCheckout\Contracts\YandexCheckout
     */
    public function setAuth($login, $password);
}
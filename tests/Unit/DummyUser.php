<?php

namespace Seungmun\LaravelYandexCheckout\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Seungmun\LaravelYandexCheckout\Traits\HasCoupon;
use Seungmun\LaravelYandexCheckout\Traits\HasPayment;
use Seungmun\LaravelYandexCheckout\Contracts\Customer;

class DummyUser extends Model implements Customer
{
    use HasPayment, HasCoupon;
}
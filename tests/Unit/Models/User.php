<?php

namespace Seungmun\LaravelYandexCheckout\Tests\Unit\Models;

use Illuminate\Database\Eloquent\Model;
use Seungmun\LaravelYandexCheckout\Contracts\Customer;
use Seungmun\LaravelYandexCheckout\Traits\HasCheckout;

class User extends Model implements Customer
{
    use HasCheckout;
}
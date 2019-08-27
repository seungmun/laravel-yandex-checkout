<?php

namespace Seungmun\LaravelYandexCheckout\Models;

use Konekt\Enum\Enum;

class PaymentStatus extends Enum
{
    const __default = self::PENDING;

    const PENDING = 'pending';
    const WAITING = 'waiting_for_capture';
    const SUCCESS = 'succeeded';
    const CANCELED = 'canceled';
}
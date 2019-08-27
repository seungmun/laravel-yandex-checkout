<?php

namespace Seungmun\LaravelYandexCheckout\Enums;

use Konekt\Enum\Enum;

class PaymentMethod extends Enum
{
    const __default = self::BANK_CARD;

    const BANK_CARD = 'bank_card';
    const INSTALLMENTS = 'installments';
}
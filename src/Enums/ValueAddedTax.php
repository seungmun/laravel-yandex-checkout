<?php

namespace Seungmun\LaravelYandexCheckout\Enums;

use Konekt\Enum\Enum;

class ValueAddedTax extends Enum
{
    const __default = self::ZERO;

    const NOT_INCLUDED = 1;
    const ZERO = 2;
    const TEN = 3;
    const TWENTY = 4;
}
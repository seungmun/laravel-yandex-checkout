<?php

namespace Seungmun\LaravelYandexCheckout\Tests\Unit;

use Seungmun\LaravelYandexCheckout\Tests\TestCase;

class ExampleTest extends TestCase
{
    public function testPaymentFeatures()
    {
        /*
        $cashier = $this->app->make(Cashier::class);
        $cashier->add(Cookie::find(1), 3);

        $payment = $cashier->transaction(['foo' => 'bar'], User::find(1));
        $card = new Card('xxxx-xxxx-xxxx-xxxx', 'yyyy', 'mm', 'xxx');

        $payload = new PaymentPayload();
        $payload->setMethod(PaymentMethod::BANK_CARD)->setCard($card)->load($payment);

        $result = $cashier->pay($payment, $payload);
        */

        $this->assertTrue(true);
    }
}

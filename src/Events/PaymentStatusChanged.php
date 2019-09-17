<?php

namespace Seungmun\LaravelYandexCheckout\Events;

use Illuminate\Queue\SerializesModels;
use Seungmun\LaravelYandexCheckout\Models\Payment;

class PaymentStatusChanged
{
    use SerializesModels;

    /**
     * The payment entity.
     *
     * @var \Seungmun\LaravelYandexCheckout\Models\Payment
     */
    public $payment;

    /**
     * Create a new event instance.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Models\Payment  $payment
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }
}

<?php

namespace Seungmun\LaravelYandexCheckout\Tests\Unit;

use Seungmun\LaravelYandexCheckout\Checkout;
use Seungmun\LaravelYandexCheckout\Models\Order;
use Seungmun\LaravelYandexCheckout\Models\Payment;
use Seungmun\LaravelYandexCheckout\Tests\TestCase;
use Seungmun\LaravelYandexCheckout\Models\OrderSummary;

class CheckoutTest extends TestCase
{
    public function test_payment_instance_can_be_created()
    {
        $payment = Checkout::payment();

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertInstanceOf(Checkout::paymentModel(), $payment);
    }

    public function test_order_summary_instance_can_be_created()
    {
        $summary = Checkout::orderSummary();

        $this->assertInstanceOf(OrderSummary::class, $summary);
        $this->assertInstanceOf(Checkout::orderSummaryModel(), $summary);
    }

    public function test_order_instance_can_be_created()
    {
        $order = Checkout::order();

        $this->assertInstanceOf(Order::class, $order);
        $this->assertInstanceOf(Checkout::orderModel(), $order);
    }
}
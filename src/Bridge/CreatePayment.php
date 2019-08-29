<?php

namespace Seungmun\LaravelYandexCheckout\Bridge;

use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;
use Seungmun\LaravelYandexCheckout\Models\Order;

class CreatePayment implements Arrayable
{
    /**
     * Payment description attribute.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Payment currency unit.
     *
     * @var string
     */
    protected $currency = 'RUB';

    /**
     * Payment method type.
     *
     * @var string
     */
    protected $method = 'bank_card';

    /**
     * Payment method extra attributes.
     *
     * @var array
     */
    protected $methodExtra;

    /**
     * Receipt receiver attribute.
     *
     * @var array
     */
    protected $receiver;

    /**
     * Receipt items attribute.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $items;

    /**
     * Create a new create payment payload instance.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Models\Order|null  $order
     */
    public function __construct(Order $order = null)
    {
        $this->items = new Collection();
        $this->setMethodExtra([]);
        $this->setReceiver('email', '');

        if ( ! is_null($order)) {
            $this->loadFromOrder($order);
        }
    }

    /**
     * Load all data from the given order.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Models\Order  $order
     * @return \Seungmun\LaravelYandexCheckout\Bridge\CreatePayment
     */
    public function loadFromOrder(Order $order)
    {
        $this->setDescription($order->getDescription())
            ->add($order->getDescription(), $order->getTotalAmount(), 1);

        return $this;
    }

    /**
     * Get payment receipt item list.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Get description attribute.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description attribute.
     *
     * @param  string  $description
     * @return \Seungmun\LaravelYandexCheckout\Bridge\CreatePayment
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get amount attribute.
     *
     * @return int
     */
    public function getAmount()
    {
        return (int)$this->items->sum(function (array $item) {
            return $item['amount']['value'];
        });
    }

    /**
     * Get currency attribute.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set currency attribute.
     *
     * @param  string  $currency
     * @return \Seungmun\LaravelYandexCheckout\Bridge\CreatePayment
     */
    public function setCurrency(string $currency)
    {
        $this->currency = strtoupper($currency);

        return $this;
    }

    /**
     * Get payment method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set payment method.
     *
     * @param  string  $method
     * @return \Seungmun\LaravelYandexCheckout\Bridge\CreatePayment
     */
    public function setMethod(string $method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get extra payment method attributes.
     *
     * @return array
     */
    public function getMethodExtra()
    {
        return $this->methodExtra;
    }

    /**
     * Set extra payment method attributes.
     *
     * @param  array  $methodExtra
     * @return \Seungmun\LaravelYandexCheckout\Bridge\CreatePayment
     */
    public function setMethodExtra(array $methodExtra)
    {
        $this->methodExtra = $methodExtra;

        return $this;
    }

    /**
     * Get payment receipt receiver attribute.
     *
     * @return array
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * Set payment receipt receiver attribute.
     *
     * @param  string  $type
     * @param  string  $to
     * @return \Seungmun\LaravelYandexCheckout\Bridge\CreatePayment
     */
    public function setReceiver(string $type, string $to)
    {
        $this->receiver = [$type => $to];

        return $this;
    }

    /**
     * Add a new receipt item into the payment receipt item list.
     *
     * @param  string  $description
     * @param  int  $amount
     * @param  int  $quantity
     * @param  string|null  $vat
     * @param  string|null  $mode
     * @param  string|null  $subject
     * @return \Seungmun\LaravelYandexCheckout\Bridge\CreatePayment
     */
    public function add(string $description, int $amount, int $quantity, $vat = null, $mode = null, $subject = null)
    {
        $this->items->push([
            'description' => $description,
            'amount' => [
                'value' => sprintf('%.2f', $amount),
                'currency' => $this->getCurrency(),
            ],
            'quantity' => sprintf('%.2f', $quantity),
            'vat_code' => $vat ?? '2',
            'payment_mode' => $mode ?? 'full_prepayment',
            'payment_subject' => $subject ?? 'service',
        ]);

        return $this;
    }

    /**
     * Make amount data attribute.
     *
     * @return array
     */
    protected function makeAmountData()
    {
        return [
            'value' => sprintf('%.2f', $this->getAmount()),
            'currency' => $this->getCurrency(),
        ];
    }

    /**
     * Make payment method data attribute.
     *
     * @return array
     */
    protected function makePaymentMethodData()
    {
        $payload = [
            'type' => $this->getMethod(),
        ];

        if ( ! empty($this->getMethodExtra())) {
            $payload = array_merge([], $payload, $this->getMethodExtra());
        }

        return $payload;
    }

    /**
     * Make payment confirmation data attribute.
     *
     * @return array
     */
    protected function makeConfirmationData()
    {
        return [
            'type' => config('laravel-yandex-checkout.confirmation_type', 'redirect'),
            'return_url' => url(config('laravel-yandex-checkout.confirmation_return_url', '/')),
            'locale' => config('laravel-yandex-checkout.confirmation_locale', 'en_US'),
            'enforce' => config('laravel-yandex-checkout.confirmation_enforcement', false),
        ];
    }

    /**
     * Make payment receipt data attribute.
     *
     * @return array
     */
    protected function makeReceiptData()
    {
        $payload = [
            'items' => $this->items->toArray(),
        ];

        return array_merge([], $payload, $this->getReceiver());
    }

    /**
     * Build create payment request payment.
     *
     * @return array
     */
    public function payload()
    {
        return [
            'amount' => $this->makeAmountData(),
            'description' => $this->getDescription(),
            'confirmation' => $this->makeConfirmationData(),
            'payment_method_data' => $this->makePaymentMethodData(),
            'receipt' => $this->makeReceiptData(),
        ];
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->payload();
    }
}
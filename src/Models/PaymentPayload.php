<?php

namespace Seungmun\LaravelYandexCheckout\Models;

use Illuminate\Contracts\Support\Arrayable;
use Seungmun\LaravelYandexCheckout\Enums\PaymentMethod;
use Seungmun\LaravelYandexCheckout\Enums\ValueAddedTax;
use Seungmun\LaravelYandexCheckout\Exceptions\PaymentException;
use Seungmun\LaravelYandexCheckout\Contracts\Order as OrderContract;
use Seungmun\LaravelYandexCheckout\Contracts\Product as ProductContract;
use Seungmun\LaravelYandexCheckout\Contracts\Payment as PaymentContract;
use Seungmun\LaravelYandexCheckout\Contracts\Customer as CustomerContract;
use Seungmun\LaravelYandexCheckout\Contracts\Card as CardContract;

class PaymentPayload implements Arrayable
{
    /**
     * Customer model entity.
     *
     * @var \Seungmun\LaravelYandexCheckout\Contracts\Customer
     */
    public $customer;

    /**
     * Payment model entity.
     *
     * @var \Seungmun\LaravelYandexCheckout\Contracts\Payment
     */
    public $payment;

    /**
     * Payment method.
     *
     * @var string
     */
    public $method = PaymentMethod::__default;

    /**
     * Payment description.
     *
     * @var string
     */
    public $description;

    /**
     * Card model.
     *
     * @var \Seungmun\LaravelYandexCheckout\Contracts\Card
     */
    public $card;

    /**
     * Confirmation data.
     *
     * @var array
     */
    public $confirmation;

    /**
     * Automatic acceptance of incoming payment.
     *
     * @var bool
     */
    public $autoCapture = false;

    /**
     * Payment meta data.
     *
     * @var array
     */
    public $meta = [];

    /**
     * Payment receipts collection.
     *
     * @var array
     */
    public $receipts = [];

    /**
     * Total receipt amount.
     *
     * @var int
     */
    public $amount = 0;

    /**
     * Set a customer attribute.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Customer  $customer
     * @return $this
     */
    public function setCustomer(CustomerContract $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Set a card attribute.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Card  $card
     * @return $this
     */
    public function setCard(CardContract $card)
    {
        $this->card = $card;

        return $this;
    }

    /**
     * Set a payment method attribute.
     *
     * @param  string  $method
     * @return $this
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\PaymentException
     */
    public function setMethod(string $method)
    {
        if ( ! in_array($method, PaymentMethod::values())) {
            throw new PaymentException('지원하지 않는 결제방식 입니다.');
        }

        $this->method = $method;

        return $this;
    }

    /**
     * Set a description attribute.
     *
     * @param  string  $description
     * @return $this
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set a capture attribute to true.
     *
     * @return $this
     */
    public function autoCapture()
    {
        $this->setAutoCapture(true);

        return $this;
    }

    /**
     * Set a capture attribute.
     *
     * @param  bool  $capture
     * @return $this
     */
    public function setAutoCapture(bool $capture = true)
    {
        $this->autoCapture = $capture;

        return $this;
    }

    /**
     * Set a meta attribute.
     *
     * @param  array  $meta
     * @return array
     */
    public function setMeta(array $meta)
    {
        $this->meta = $meta;

        return $meta;
    }

    /**
     * Set confirmation attribute.
     *
     * @param  string  $type
     * @param  string|null  $url
     * @return $this
     */
    public function setConfirmation($type, $url = null)
    {
        $this->confirmation = [
            'type' => $type,
            'return_url' => url($url),
        ];

        return $this;
    }

    /**
     * Add a new pure item into the payment receipt.
     *
     * @param  string  $description
     * @param  int  $price
     * @param  int  $quantity
     * @param  int|null  $VAT
     * @return $this
     */
    public function add(string $description, int $price, int $quantity, $VAT = null)
    {
        $VAT = is_null($VAT) ? ValueAddedTax::ZERO : $VAT;

        $this->receipts[] = [
            'description' => $description,
            'amount' => [
                'value' => sprintf("%.2f", $price),
                'currency' => 'RUB',
            ],
            'quantity' => sprintf("%.2f", $quantity),
            'vat_code' => $VAT,
        ];

        $this->amount += $price * $quantity;

        return $this;
    }

    /**
     * Add a new product item into the payment receipt.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Product  $product
     * @param  int  $quantity
     * @param  int|null  $VAT
     * @return $this
     */
    public function addProduct(ProductContract $product, $quantity, $VAT = null)
    {
        $this->add($product->description(), $product->price(), $quantity, $VAT);

        return $this;
    }

    /**
     * Add a new product item into the payment receipt.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Order  $order
     * @param  int|null  $VAT
     * @return $this
     */
    public function addOrder(OrderContract $order, $VAT = null)
    {
        /** @var \Seungmun\LaravelYandexCheckout\Models\Order $order */
        $this->add($order->product->description(), $order->price(), $order->quantity(), $VAT);

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $payment = [
            'description' => $this->description,
            'capture' => $this->autoCapture,
            'metadata' => $this->meta,
            'confirmation' => $this->confirmation,
            'payment_method_data' => $this->buildPaymentMethod(),
            'amount' => [
                'value' => sprintf("%.2f", $this->amount),
                'currency' => 'RUB',
            ],
        ];

        if (count($this->receipts) > 0) {
            $payment['receipt'] = $this->buildReceipt();
        }

        return $payment;
    }

    /**
     * Build a payment method data param.
     *
     * @return array
     */
    protected function buildPaymentMethod()
    {
        return [
            'type' => $this->method,
            'card' => $this->card->toArray(),
        ];
    }

    /**
     * Build a receipt attribute param.
     *
     * @return array
     */
    protected function buildReceipt()
    {
        $receiptReceive = $this->customer->receiptReceive();

        $params = [
            key($receiptReceive) => $receiptReceive[key($receiptReceive)],
            'items' => $this->receipts,
        ];

        return $params;
    }

    /**
     * Load payment payload from the payment model.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Payment  $payment
     * @return \Seungmun\LaravelYandexCheckout\Models\PaymentPayload
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\PaymentException
     */
    public function load(PaymentContract $payment)
    {
        /** @var \Seungmun\LaravelYandexCheckout\Models\Payment $payment */
        $this->setCustomer($payment->customer)
            ->setDescription($payment->summary->description)
            ->setAutoCapture();

        $payment->summary
            ->orders
            ->each(function (OrderContract $order) {
                $this->addOrder($order);
            });

        if ($payment->summary->amount > $this->amount) {
            throw new PaymentException('Payment payload has some amount errors.');
        }

        return $this;
    }
}

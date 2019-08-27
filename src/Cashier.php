<?php

namespace Seungmun\LaravelYandexCheckout;

use Exception;
use YandexCheckout\Client;
use Illuminate\Support\Facades\DB;
use YandexCheckout\Model\RefundInterface;
use YandexCheckout\Model\PaymentInterface;
use YandexCheckout\Model\NotificationEventType;
use Seungmun\LaravelYandexCheckout\Models\Payment;
use Seungmun\LaravelYandexCheckout\Models\PaymentPayload;
use YandexCheckout\Model\Notification\NotificationCanceled;
use YandexCheckout\Model\Notification\NotificationSucceeded;
use Seungmun\LaravelYandexCheckout\Exceptions\PaymentException;
use Seungmun\LaravelYandexCheckout\Contracts\Cart as CartContract;
use Seungmun\LaravelYandexCheckout\Contracts\Card as CardContract;
use YandexCheckout\Model\Notification\NotificationRefundSucceeded;
use YandexCheckout\Model\Notification\NotificationWaitingForCapture;
use Seungmun\LaravelYandexCheckout\Contracts\Product as ProductContract;
use Seungmun\LaravelYandexCheckout\Contracts\Payment as PaymentContract;
use Seungmun\LaravelYandexCheckout\Contracts\Customer as CustomerContract;

class Cashier
{
    /**
     * configuration bag.
     *
     * @var array
     */
    private $config = [];

    /**
     * Yandex.Checkout http client.
     *
     * @var \YandexCheckout\Client
     */
    private $client;

    /**
     * Create a new Cashier instance.
     *
     * @param  array  $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = static::makeClient($config);
    }

    /**
     * Make a Yandex.Checkout HTTP Client instance.
     *
     * @param  array  $config
     * @return \YandexCheckout\Client
     */
    public static function makeClient(array $config): Client
    {
        return (new Client())->setAuth($config['shop_id'], $config['secret_key']);
    }

    /**
     * Create a new Yandex.Checkout Payment request.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Payment  $payment
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Card  $card
     * @return \Seungmun\LaravelYandexCheckout\Models\Payment
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\PaymentException
     */
    public function pay(PaymentContract $payment, CardContract $card)
    {
        try {
            $payload = (new PaymentPayload)
                ->setConfirmation(
                    $this->config['confirmation_type'],
                    $this->config['confirmation_return_url']
                )
                ->setAutoCapture()
                ->setMethod('bank_card')
                ->setDescription($payment->summary->description)
                ->setCustomer($payment->customer)
                ->setCard($card);

            $response = $this->client->createPayment($payload->toArray());
        } catch (Exception $e) {
            throw new PaymentException($e);
        }

        $payment = DB::transaction(function () use ($payment, $response) {
            $payment->uuid = $response->getId();
            $payment->shop_key = $response->getRecipient()->getAccountId();
            $payment->is_paid = $response->getPaid();
            $payment->status = $response->getStatus();
            $payment->captured_at = $response->getCapturedAt();
            $payment->expires_at = $response->getExpiresAt();
            $payment->response = $response->jsonSerialize();
            $payment->save();

            $summary = $payment->summary;
            $summary->amount = (int)$response->getAmount()->getValue();
            $summary->refunded_amount = (int)optional($response->getRefundedAmount())->getValue();
            $summary->save();

            return $payment;
        });

        return $payment;
    }

    /**
     * Add a specified product with quantity from the cart.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Product  $product
     * @param  int  $quantity
     * @return \Seungmun\LaravelYandexCheckout\Contracts\Cart
     */
    public function add(ProductContract $product, $quantity)
    {
        return $this->cart()->add($product, $quantity);
    }

    /**
     * Remove a specified product from the cart.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Product  $product
     * @return \Seungmun\LaravelYandexCheckout\Contracts\Cart
     */
    public function remove(ProductContract $product)
    {
        return $this->cart()->remove($product);
    }

    /**
     * Get the cart instance.
     *
     * @return \Seungmun\LaravelYandexCheckout\Contracts\Cart
     */
    public function cart()
    {
        return app()->make(CartContract::class);
    }

    /**
     * Make a new transaction with all items in the cart.
     *
     * @param  array  $options
     * @param  \Seungmun\LaravelYandexCheckout\Contracts\Customer|null  $customer
     * @return \Seungmun\LaravelYandexCheckout\Contracts\Payment
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\CartException
     */
    public function transaction(array $options = [], CustomerContract $customer = null)
    {
        $payment = $this->cart()->makePayment($customer, $options);

        return $payment;
    }

    /**
     * Store information about changes received from the notification.
     *
     * @param  \YandexCheckout\Model\PaymentInterface|\YandexCheckout\Model\RefundInterface  $object
     * @return \Seungmun\LaravelYandexCheckout\Contracts\Payment
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\PaymentException
     */
    public function handleNotification($object)
    {
        if ($object instanceof PaymentInterface) {
            return $this->handlePaymentEvent($object);
        }

        if ($object instanceof RefundInterface) {
            return $this->handleRefundEvent($object);
        }

        throw new PaymentException('잘못된 결제정보에 대한 요청입니다.');
    }

    /**
     * Persist payment information received from the notification.
     *
     * @param  \YandexCheckout\Model\PaymentInterface  $paymentObject
     * @return \Seungmun\LaravelYandexCheckout\Contracts\Payment
     */
    public function handlePaymentEvent(PaymentInterface $paymentObject): PaymentContract
    {
        $payment = Payment::with('summary')
            ->where('uuid', $paymentObject->getId())
            ->firstOrFail();

        $payment = tap($payment)->update([
            'is_paid' => $paymentObject->getPaid(),
            'status' => $paymentObject->getStatus(),
            'captured_at' => $paymentObject->getCapturedAt(),
            'expires_at' => $paymentObject->getExpiresAt(),
        ]);

        $payment->summary->update([
            'total_paid' => (int)$paymentObject->getAmount()->getValue(),
        ]);

        return $payment;
    }

    /**
     * Persist refunded information received from the notification.
     *
     * @param  \YandexCheckout\Model\RefundInterface  $refundObject
     * @return \Seungmun\LaravelYandexCheckout\Models\Payment
     */
    public function handleRefundEvent(RefundInterface $refundObject): Payment
    {
        $payment = Payment::with('summary')
            ->where('uuid', $refundObject->getId())
            ->firstOrFail();

        // Todo: 환불(취소) 처리 부분에 대한 로직 구현

        return $payment;
    }

    /**
     * Get the payment information stored in the database through the requested notification.
     *
     * @param  array  $inputs
     * @return \YandexCheckout\Model\PaymentInterface|\YandexCheckout\Model\RefundInterface
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\PaymentException
     */
    public function notificationFactory(array $inputs)
    {
        switch ($inputs['event']) {
            case NotificationEventType::PAYMENT_SUCCEEDED :
                $notification = new NotificationSucceeded($inputs);
                break;
            case NotificationEventType::PAYMENT_CANCELED :
                $notification = new NotificationCanceled($inputs);
                break;
            case NotificationEventType::PAYMENT_WAITING_FOR_CAPTURE :
                $notification = new NotificationWaitingForCapture($inputs);
                break;
            case NotificationEventType::REFUND_SUCCEEDED :
                $notification = new NotificationRefundSucceeded($inputs);
                break;
            default:
                throw new PaymentException('잘 못된 웹훅 요청입니다.');
        }

        return $notification->getObject();
    }
}

<?php

namespace Seungmun\LaravelYandexCheckout\Traits;

use YandexCheckout\Model\RefundInterface;
use YandexCheckout\Model\PaymentInterface;
use Seungmun\LaravelYandexCheckout\Checkout;
use YandexCheckout\Model\NotificationEventType;
use YandexCheckout\Model\Notification\NotificationCanceled;
use YandexCheckout\Model\Notification\NotificationSucceeded;
use Seungmun\LaravelYandexCheckout\Exceptions\CheckoutException;
use YandexCheckout\Model\Notification\NotificationRefundSucceeded;
use YandexCheckout\Model\Notification\NotificationWaitingForCapture;

trait HandlesYandex
{
    /**
     * Get the payment information stored in the database through the requested notification.
     *
     * @param  array  $inputs
     * @return \YandexCheckout\Model\PaymentInterface|\YandexCheckout\Model\RefundInterface
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\CheckoutException
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
                throw new CheckoutException('잘 못된 웹훅 요청입니다.');
        }

        return $notification->getObject();
    }

    /**
     * Persist payment information received from the notification.
     *
     * @param  \YandexCheckout\Model\PaymentInterface  $paymentObject
     * @return \Seungmun\LaravelYandexCheckout\Models\Payment
     */
    public function handlePaymentEvent(PaymentInterface $paymentObject)
    {
        $payment = Checkout::payment()
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
    public function handleRefundEvent(RefundInterface $refundObject)
    {
        $payment = Checkout::payment()
            ->where('uuid', $refundObject->getId())
            ->firstOrFail();

        // Todo: 환불(취소) 처리 부분에 대한 로직 구현
        return $payment;
    }

    /**
     * Store information about changes received from the notification.
     *
     * @param  \YandexCheckout\Model\PaymentInterface|\YandexCheckout\Model\RefundInterface  $object
     * @return \Seungmun\LaravelYandexCheckout\Models\Payment
     * @throws \Seungmun\LaravelYandexCheckout\Exceptions\CheckoutException
     */
    public function handleNotification($object)
    {
        if ($object instanceof PaymentInterface) {
            return $this->handlePaymentEvent($object);
        }

        if ($object instanceof RefundInterface) {
            return $this->handleRefundEvent($object);
        }

        throw new CheckoutException('잘못된 결제정보에 대한 요청입니다.');
    }
}
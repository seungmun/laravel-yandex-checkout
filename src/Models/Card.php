<?php

namespace Seungmun\LaravelYandexCheckout\Models;

use Seungmun\LaravelYandexCheckout\Contracts\Card as CardContract;

class Card implements CardContract
{
    /**
     * Credit card number.
     *
     * @var string
     */
    protected $number;

    /**
     * Card expiry year.
     *
     * @var string
     */
    protected $expiryYear;

    /**
     * Card expiry month.
     *
     * @var string
     */
    protected $expiryMonth;

    /**
     * Card csc number.
     *
     * @var string
     */
    protected $csc;

    /**
     * Card holder's name.
     *
     * @var string|null
     */
    protected $holder = null;

    /**
     * Create a new card instance.
     *
     * @param  string  $number
     * @param  string  $expiryYear
     * @param  string  $expiryMonth
     * @param  string  $csc
     * @param  string|null  $holder
     * @return void
     */
    public function __construct(
        string $number,
        string $expiryYear,
        string $expiryMonth,
        string $csc,
        string $holder = null)
    {
        $this->setNumber($number)
            ->setExpiryYear($expiryYear)
            ->setExpiryMonth($expiryMonth)
            ->setCsc($csc)
            ->setHolder($holder);
    }

    /**
     * Get card number attribute.
     *
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * Set card number attribute.
     *
     * @param  string  $number
     * @return $this
     */
    public function setNumber(string $number)
    {
        $this->number = trim(str_replace([' ', '-'], ['', ''], $number));

        return $this;
    }

    /**
     * Get card expiry year attribute.
     *
     * @return string
     */
    public function getExpiryYear(): string
    {
        return $this->expiryYear;
    }

    /**
     * Set card expiry year attribute.
     *
     * @param  string  $expiryYear
     * @return $this
     */
    public function setExpiryYear(string $expiryYear)
    {
        $this->expiryYear = $expiryYear;

        return $this;
    }

    /**
     * Get expiry month attribute.
     *
     * @return string
     */
    public function getExpiryMonth(): string
    {
        return $this->expiryMonth;
    }

    /**
     * Set expiry month attribute.
     *
     * @param  string  $expiryMonth
     * @return $this
     */
    public function setExpiryMonth(string $expiryMonth)
    {
        $this->expiryMonth = sprintf('%2d', $expiryMonth);

        return $this;
    }

    /**
     * Get card csc number attribute.
     *
     * @return string
     */
    public function getCsc(): string
    {
        return $this->csc;
    }

    /**
     * Get card csc number attribute.
     *
     * @param  string  $csc
     * @return $this
     */
    public function setCsc(string $csc)
    {
        $this->csc = $csc;

        return $this;
    }

    /**
     * Get card holder's name attribute.
     *
     * @return string|null
     */
    public function getHolder(): ?string
    {
        return $this->holder;
    }

    /**
     * Set card holder's name attribute.
     *
     * @param  string|null  $holder
     * @return $this
     */
    public function setHolder(?string $holder)
    {
        $this->holder = strtoupper($holder);

        return $this;
    }

    /**
     * Get cart number.
     *
     * @return string
     */
    public function number()
    {
        return $this->getNumber();
    }

    /**
     * Get expiry year.
     *
     * @return string
     */
    public function expiryYear()
    {
        return $this->getExpiryYear();
    }

    /**
     * Get expiry month.
     *
     * @return string
     */
    public function expiryMonth()
    {
        return $this->expiryMonth();
    }

    /**
     * Get csc number.
     *
     * @return string
     */
    public function csc()
    {
        return $this->getCsc();
    }

    /**
     * Get card holder name.
     *
     * @return string
     */
    public function cardHolder()
    {
        return $this->getHolder();
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $payload = [
            'number' => $this->getNumber(),
            'expiry_year' => $this->getExpiryYear(),
            'expiry_month' => $this->getExpiryMonth(),
            'csc' => $this->getCsc(),
        ];

        if ( ! empty($this->getHolder())) {
            $payload['cardholder'] = $this->getHolder();
        }

        return $payload;
    }
}

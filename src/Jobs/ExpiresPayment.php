<?php

namespace Seungmun\LaravelYandexCheckout\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Seungmun\LaravelYandexCheckout\Models\Payment;

class ExpiresPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Payment model instance.
     *
     * @var \Seungmun\LaravelYandexCheckout\Models\Payment
     */
    protected $payment;

    /**
     * Create a new job instance.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Models\Payment  $payment
     * @return void
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $shouldExpires = ! $this->payment->is_paid && $this->payment->status === 'pending';

        if ($shouldExpires) {
            $summary = $this->payment->summary;
            $summary->issuedCoupons()->update(['used_at' => null]);
            $summary->issuedCoupons()->detach();
            $this->payment->delete();
        }
    }
}
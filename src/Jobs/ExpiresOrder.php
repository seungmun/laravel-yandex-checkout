<?php

namespace Seungmun\LaravelYandexCheckout\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Seungmun\LaravelYandexCheckout\Models\Order;

class ExpiresOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Order model instance.
     *
     * @var \Seungmun\LaravelYandexCheckout\Models\Order
     */
    protected $order;

    /**
     * Create a new job instance.
     *
     * @param  \Seungmun\LaravelYandexCheckout\Models\Order  $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $order = $this->order;
        $shouldExpires = ! $order->isPaid() && $order->payment->status === 'pending';

        if ($shouldExpires) {
            $order->coupons()->update([
                'order_id' => null,
                'used_at' => null,
            ]);

            $order->delete();
        }
    }
}
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('laravel-yandex-checkout.table.order_summary'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('payment_id')->nullable();
            $table->foreign('payment_id')
                ->references('id')
                ->on(config('laravel-yandex-checkout.table.payment'))
                ->onDelete('cascade');

            $table->string('description');

            $table->integer('amount');
            $table->integer('total_paid');
            $table->integer('refunded_amount');

            $table->json('extra');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('laravel-yandex-checkout.table.payment_summary'));
    }
}

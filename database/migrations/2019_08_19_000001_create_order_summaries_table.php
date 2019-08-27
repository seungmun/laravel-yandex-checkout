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
        Schema::create('order_summaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->foreign('payment_id')
                ->references('id')
                ->on(config('laravel-yandex-checkout.table.payment'))
                ->onDelete('cascade');
            $table->string('description');
            $table->integer('amount');
            $table->integer('total_paid')->default(0);
            $table->integer('refunded_amount')->default(0);
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
        Schema::dropIfExists('order_summaries');
    }
}

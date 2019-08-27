<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('laravel-yandex-checkout.table.order'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('order_summary_id')->nullable();
            $table->foreign('order_summary_id')
                ->references('id')
                ->on(config('laravel-yandex-checkout.table.order_summary'))
                ->onDelete('cascade');

            $table->morphs('product');

            $table->integer('price');
            $table->integer('quantity');
            $table->integer('amount');

            $table->boolean('is_refunded');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('laravel-yandex-checkout.table.order'));
    }
}

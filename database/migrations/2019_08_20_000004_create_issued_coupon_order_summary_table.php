<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssuedCouponOrderSummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issued_coupon_order_summary', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('issued_coupon_id');
            $table->foreign('issued_coupon_id')->references('id')->on('issued_coupons');
            $table->unsignedBigInteger('order_summary_id');
            $table->foreign('order_summary_id')->references('id')->on('order_summaries');
            $table->integer('discount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('issued_coupon_order_summary');
    }
}

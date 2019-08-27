<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Seungmun\LaravelYandexCheckout\Models\PaymentStatus;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->nullable()->unique();
            $table->morphs('customer');
            $table->string('shop_key')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->enum('status', PaymentStatus::values())->default(PaymentStatus::defaultValue());
            $table->timestamp('captured_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('response');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}

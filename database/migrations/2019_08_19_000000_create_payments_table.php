<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->morphs('customer');
            $table->uuid('uuid')->nullable()->unique();
            $table->string('shop_id')->nullable()->index();
            $table->boolean('is_paid')->default(false);
            $table->enum('status', ['pending', 'waiting_for_capture', 'succeeded', 'canceled'])
                ->default('pending');
            $table->timestamp('captured_at')->nullable();
            $table->timestamp('expires_at')->nullable();
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

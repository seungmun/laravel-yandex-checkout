<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->index();
            $table->string('target')->nullable()->index();
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('quantity')->nullable();
            $table->enum('type', ['fixed', 'percentage'])->default('fixed');
            $table->integer('value');
            $table->boolean('is_reusable')->default(false);
            $table->string('expiry')->nullable();
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
        Schema::dropIfExists('coupon_types');
    }
}
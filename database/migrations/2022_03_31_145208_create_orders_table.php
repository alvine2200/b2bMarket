<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //order table
        Schema::create('orders', function (Blueprint $table) {

            $table->id();
            $table->string('paid_by');
            $table->string('business_name');
            $table->string('country');
            $table->string('address');
            $table->string('phone');
            $table->string('email');
            $table->foreignId('user_id')->nullable()->constrained();
           // $table->foreignId('business_id')->nullable()->constrained();
            $table->string('order_status')->default('pending'); //after payment admin can change status to complete
            $table->string('invoice_id');
            $table->string('order_id');
            $table->datetime('order_date');
            $table->string('payment_method');
            $table->string('shipping_method');
            $table->float('total');
            $table->string('referrence_number');
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
        Schema::dropIfExists('orders');
    }
};

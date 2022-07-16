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
        Schema::create('paypal__payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->string('invoice_id')->unique();
            $table->date('order_date')->unique();
            $table->string('order_status');
            $table->string('referrence_number')->unique();
            $table->string('Paid_by');
            $table->string('Paid_to');
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
        Schema::dropIfExists('paypal__payments');
    }
};

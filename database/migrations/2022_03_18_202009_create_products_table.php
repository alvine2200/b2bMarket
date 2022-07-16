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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId("business_id")->constrained();
            $table->foreignId("created_by")->constrained("users");
            $table->string('name');
            $table->string('gallery_image')->nullable();
            $table->string('thumbnail_image')->nullable();
            $table->string('pdf_specs')->nullable();
            $table->text('description');
            $table->float('quantity');
            $table->float('unit_price');
            $table->float('tax')->default(0);
            $table->string('category');
            $table->string('units');
            $table->string('sku');
            $table->float('minimum_purchase_quantity');
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
        Schema::dropIfExists('products');
    }
};

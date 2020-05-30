<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateProductsTable extends Migration
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
            $table->integer('category_id');
            $table->integer('product_group_id');
            $table->string('variation');
            $table->string('main_variation');
            $table->decimal('price', 8, 2)->unsigned();
            $table->decimal('price_vat', 8, 2)->unsigned();
            $table->decimal('price_cashless', 8, 2)->unsigned();
            $table->decimal('price_unit', 8, 2)->unsigned();
            $table->decimal('price_unit_vat', 8, 2)->unsigned();
            $table->decimal('price_unit_cashless', 8, 2)->unsigned();
            $table->decimal('in_stock', 10, 2);
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
}

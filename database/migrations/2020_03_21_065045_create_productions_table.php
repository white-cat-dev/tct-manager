<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->integer('category_id');
            $table->integer('product_group_id');
            $table->integer('product_id');
            $table->integer('order_id');
            $table->integer('facility_id');
            $table->decimal('auto_planned', 10, 3)->unsigned();
            $table->decimal('manual_planned', 10, 3);
            $table->date('date_to')->nullable();
            $table->tinyInteger('priority');
            $table->decimal('performed', 10, 3)->unsigned();
            $table->decimal('auto_batches', 6, 3)->unsigned();
            $table->decimal('manual_batches', 6, 3);
            $table->decimal('salary', 8, 3)->unsigned();
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
        Schema::dropIfExists('productions');
    }
}

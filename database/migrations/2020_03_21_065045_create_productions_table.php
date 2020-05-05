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
            $table->integer('product_id');
            $table->integer('order_id');
            $table->integer('facility_id');
            $table->decimal('planned', 10, 2)->unsigned();
            $table->decimal('performed', 10, 2)->unsigned();
            $table->decimal('batches', 4, 2)->unsigned();
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->string('main_category');
            $table->date('date');
            $table->date('date_to');
            $table->integer('client_id');
            $table->string('delivery');
            $table->decimal('delivery_distance', 5, 2)->unsigned();
            $table->string('comment');
            $table->string('status');
            $table->tinyInteger('priority');
            $table->decimal('weight', 7, 3)->unsigned();
            $table->decimal('cost', 10, 3)->unsigned();
            $table->decimal('paid', 10, 3)->unsigned();
            $table->integer('pallets');
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
}

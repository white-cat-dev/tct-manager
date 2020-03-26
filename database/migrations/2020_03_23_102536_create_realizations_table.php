<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRealizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('realizations', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('product_id');
            $table->integer('order_id')->nullable();
            $table->decimal('planned', 10, 2)->unsigned();
            $table->decimal('performed', 10, 2)->unsigned();
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
        Schema::dropIfExists('realizations');
    }
}

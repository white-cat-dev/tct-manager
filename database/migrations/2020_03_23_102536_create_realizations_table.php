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
            $table->date('date')->nullable();
            $table->integer('category_id');
            $table->integer('product_group_id');
            $table->integer('product_id');
            $table->integer('order_id');
            $table->decimal('planned', 10, 3)->unsigned();
            $table->decimal('ready', 10, 3)->unsigned();
            $table->decimal('performed', 10, 3)->unsigned();
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

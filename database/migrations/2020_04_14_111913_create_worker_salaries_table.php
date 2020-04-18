<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkerSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('worker_salaries', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('worker_id');
            $table->decimal('employments', 10, 2);
            $table->decimal('bonus', 10, 2);
            $table->decimal('advance', 10, 2);
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
        Schema::dropIfExists('worker_salaries');
    }
}

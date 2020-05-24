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
            $table->decimal('employments', 10, 3);
            $table->decimal('lunch', 10, 3);
            $table->decimal('tax', 10, 3);
            $table->decimal('bonus', 10, 3);
            $table->decimal('surcharge', 10, 3);
            $table->decimal('advance', 10, 3);
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

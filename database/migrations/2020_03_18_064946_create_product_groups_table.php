<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateProductGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_groups', function (Blueprint $table) {
            $table->id();
            $table->integer('wp_id');
            $table->string('wp_name');
            $table->string('name');
            $table->integer('category_id');
            $table->integer('set_pair_id');
            $table->integer('width');
            $table->integer('length');
            $table->integer('depth');
            $table->string('adjectives');
            $table->decimal('weight_unit', 6, 2)->unsigned();
            $table->decimal('weight_units', 6, 2)->unsigned();
            $table->decimal('weight_pallete', 6, 2)->unsigned();
            $table->decimal('unit_in_units', 5, 2)->unsigned();
            $table->decimal('unit_in_pallete', 5, 2)->unsigned();
            $table->decimal('units_in_pallete', 5, 2)->unsigned();
            $table->decimal('units_from_batch', 5, 2)->unsigned();
            $table->integer('forms')->unsigned();
            $table->decimal('salary_units', 5, 2)->unsigned();
            $table->integer('recipe_id');
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
        Schema::dropIfExists('product_groups');
    }
}

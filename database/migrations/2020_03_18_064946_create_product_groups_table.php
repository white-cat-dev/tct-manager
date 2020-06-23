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
            $table->string('name');
            $table->string('wp_name');
            $table->string('wp_slug');
            $table->integer('category_id');
            $table->integer('set_pair_id')->nullable();
            $table->decimal('set_pair_ratio', 4, 2)->unsigned();
            $table->decimal('set_pair_ratio_to', 4, 2)->unsigned();
            $table->string('size_params');
            $table->integer('width');
            $table->integer('length');
            $table->integer('height');
            $table->string('adjectives');
            $table->decimal('weight_unit', 7, 3)->unsigned();
            $table->decimal('weight_pallete', 7, 3)->unsigned();
            $table->decimal('unit_in_units', 7, 3)->unsigned();
            $table->decimal('unit_in_pallete', 7, 3)->unsigned();
            $table->decimal('units_in_pallete', 7, 3)->unsigned();
            $table->decimal('units_from_batch', 7, 3)->unsigned();
            $table->decimal('forms', 7, 3)->unsigned();
            $table->decimal('performance', 7, 3)->unsigned();
            $table->decimal('salary_units', 7, 2)->unsigned();
            $table->integer('recipe_id')->nullable();
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

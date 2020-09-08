<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->integer('process_id')->after('model_type');
            $table->string('process_type')->after('process_id');
            $table->string('reason')->after('new_in_stock');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('process_id');
            $table->dropColumn('process_type');
            $table->dropColumn('reason');
        });
    }
}

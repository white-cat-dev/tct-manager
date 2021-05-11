<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGuidToProductsAndOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_groups', function (Blueprint $table) {
            $table->string('guid')->default('')->after('id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('guid')->default('')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_groups', function (Blueprint $table) {
            $table->dropColumn('guid');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('guid');
        });
    }
}

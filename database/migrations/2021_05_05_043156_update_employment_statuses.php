<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmploymentStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employment_statuses', function (Blueprint $table) {
            $table->string('type')->after('name');
            $table->decimal('salary', 10, 3)->after('type');
            $table->decimal('base_salary', 10, 3)->after('salary');
            $table->decimal('default_salary', 10, 3)->after('base_salary');

            $table->dropColumn('salary_production');
            $table->dropColumn('salary_fixed');
            $table->dropColumn('salary_team');
        });

        Schema::table('employments', function (Blueprint $table) {
            $table->decimal('status_custom', 10, 3)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employment_statuses', function (Blueprint $table) {
            $table->decimal('salary_production', 5, 3);
            $table->decimal('salary_fixed', 10, 3);
            $table->decimal('salary_team', 10, 3);

            $table->dropColumn('type');
            $table->dropColumn('salary');
            $table->dropColumn('base_salary');
            $table->dropColumn('default_salary');
        });

        Schema::table('employments', function (Blueprint $table) {
            $table->decimal('status_custom', 4, 3)->change();
        });
    }
}

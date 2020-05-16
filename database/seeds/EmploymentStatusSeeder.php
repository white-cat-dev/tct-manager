<?php

use Illuminate\Database\Seeder;
use App\EmploymentStatus;


class EmploymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EmploymentStatus::insert([
        	[
	        	'name' => 'Ввод',
	        	'icon' => '<i class="fas fa-italic"></i>',
	        	'icon_color' => '#276090',
	        	'salary_production' => 0,
	        	'salary_fixed' => 0,
	        	'salary_team' => 0,
	        	'customable' => true
	        ],
	        [
	        	'name' => 'Выходной',
	        	'icon' => '<i class="fas fa-coffee"></i>',
	        	'icon_color' => '#9a9a9a',
	        	'salary_production' => 0,
	        	'salary_fixed' => 0,
	        	'salary_team' => 0,
	        	'customable' => false
	        ],
	        [
	        	'name' => 'Прогул',
	        	'icon' => '<i class="fas fa-times"></i>',
	        	'icon_color' => '#d35400',
	        	'salary_production' => -1,
	        	'salary_fixed' => -2500,
	        	'salary_team' => 0,
	        	'customable' => false
	        ]
        ]);
    }
}

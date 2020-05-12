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
	        	'name' => '1',
	        	'icon' => 'name',
	        	'icon_color' => '#28a65b',
	        	'salary_production' => 1.0,
	        	'salary_fixed' => 0,
	        	'salary_team' => 0
	        ],
	        [
	        	'name' => '0.9',
	        	'icon' => 'name',
	        	'icon_color' => '#4e9e50',
	        	'salary_production' => 0.9,
	        	'salary_fixed' => 0,
	        	'salary_team' => 0
	        ],
	        [
	        	'name' => '0.8',
	        	'icon' => 'name',
	        	'icon_color' => '#749645',
	        	'salary_production' => 0.8,
	        	'salary_fixed' => 0,
	        	'salary_team' => 0
	        ],
	        [
	        	'name' => '0.7',
	        	'icon' => 'name',
	        	'icon_color' => '#9a8e39',
	        	'salary_production' => 0.7,
	        	'salary_fixed' => 0,
	        	'salary_team' => 0
	        ],
	        [
	        	'name' => '0.6',
	        	'icon' => 'name',
	        	'icon_color' => '#c0862e',
	        	'salary_production' => 0.6,
	        	'salary_fixed' => 0,
	        	'salary_team' => 0
	        ],
	        [
	        	'name' => '0.5',
	        	'icon' => 'name',
	        	'icon_color' => '#e67e22',
	        	'salary_production' => 0.5,
	        	'salary_fixed' => 0,
	        	'salary_team' => 0
	        ],
	        [
	        	'name' => 'Бригадир',
	        	'icon' => '<i class="fas fa-user-tie"></i>',
	        	'icon_color' => '#28a65b',
	        	'salary_production' => 1,
	        	'salary_fixed' => 0,
	        	'salary_team' => 0.1
	        ],
	        [
	        	'name' => 'Пропуск',
	        	'icon' => '<i class="fas fa-times"></i>',
	        	'icon_color' => '#d35400',
	        	'salary_production' => -1,
	        	'salary_fixed' => -2500,
	        	'salary_team' => 0
	        ]
        ]);
    }
}

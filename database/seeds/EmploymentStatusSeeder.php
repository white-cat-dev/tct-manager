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
	        	'name' => 'Должен выйти',
	        	'icon' => '<i class="fas fa-check"></i>',
	        	'icon_color' => '#E0E0E0',
	        	'salary' => 0
	        ],
	        [
	        	'name' => 'Вышел',
	        	'icon' => '<i class="fas fa-check"></i>',
	        	'icon_color' => '#4081B7',
	        	'salary' => 1000
	        ],
	        [
	        	'name' => 'Не вышел',
	        	'icon' => '<i class="fas fa-times"></i>',
	        	'icon_color' => '#E0E0E0',
	        	'salary' => -1000
	        ]
        ]);
    }
}

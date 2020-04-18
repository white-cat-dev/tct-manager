<?php

use Illuminate\Database\Seeder;
use App\Worker;


class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Worker::insert([
        	[
	        	'name' => 'Пикторски',
	        	'surname' => 'Максимов',
	        	'full_name' => 'Виктор',
	        	'patronymic' => 'Александрович',
	        	'status' => 'active',
	        	'facility_id' => 0
	        ],
	        [
	        	'name' => 'Пикторски №2',
	        	'surname' => 'Максимов',
	        	'full_name' => 'Виктор',
	        	'patronymic' => 'Александрович',
	        	'status' => 'active',
	        	'facility_id' => 0
	        ]
        ]);
    }
}

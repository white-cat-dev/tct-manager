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
	        	'name' => 'Витя',
	        	'surname' => 'Максимов',
	        	'full_name' => 'Виктор',
	        	'patronymic' => 'Александрович',
	        	'status' => 'active',
	        	'facility_id' => 1
	        ],
	        [
	        	'name' => 'Егор',
	        	'surname' => 'Мельников',
	        	'full_name' => 'Егор',
	        	'patronymic' => 'Викторович',
	        	'status' => 'active',
	        	'facility_id' => 1
	        ],
	        [
	        	'name' => 'Илья',
	        	'surname' => 'Давыдов',
	        	'full_name' => 'Илья',
	        	'patronymic' => 'Григорьевич',
	        	'status' => 'active',
	        	'facility_id' => 1
	        ],
	        [
	        	'name' => 'Влад',
	        	'surname' => 'Данилов',
	        	'full_name' => 'Владислав',
	        	'patronymic' => 'Петрович',
	        	'status' => 'active',
	        	'facility_id' => 2
	        ]
        ]);
    }
}

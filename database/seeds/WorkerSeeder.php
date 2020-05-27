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
	        	'name' => 'Слава',
	        	'surname' => 'Истомин',
	        	'full_name' => 'Вячеслав',
	        	'patronymic' => 'Батькович',
	        	'phone' => '',
	        	'status' => Worker::STATUS_ACTIVE,
	        	'passport' => '',
	        	'birthdate' => null
	        ]
        ]);
    }
}

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
	        	'phone' => '79120389096',
	        	'status' => Worker::STATUS_ACTIVE
	        ],
	        [
	        	'name' => 'Егор',
	        	'surname' => 'Мельников',
	        	'full_name' => 'Егор',
	        	'patronymic' => 'Викторович',
	        	'phone' => '79085234132',
	        	'status' => Worker::STATUS_ACTIVE
	        ],
	        [
	        	'name' => 'Илья',
	        	'surname' => 'Давыдов',
	        	'full_name' => 'Илья',
	        	'patronymic' => 'Григорьевич',
	        	'phone' => '79085324322',
	        	'status' => Worker::STATUS_ACTIVE
	        ],
	        [
	        	'name' => 'Влад',
	        	'surname' => 'Данилов',
	        	'full_name' => 'Владислав',
	        	'patronymic' => 'Петрович',
	        	'phone' => '79903480345',
	        	'status' => Worker::STATUS_ACTIVE
	        ]
        ]);
    }
}

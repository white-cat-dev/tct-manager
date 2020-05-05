<?php

use Illuminate\Database\Seeder;
use App\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
        	[
	        	'name' => 'Александра',
	            'email' => 'admin',
	            'password' => Hash::make('123'),
	            'type' => 'admin'
            ]
        ]);
    }
}

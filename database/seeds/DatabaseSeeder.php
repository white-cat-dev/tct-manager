<?php

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
	        EmploymentStatusSeeder::class,
            CategorySeeder::class,
            WorkerSeeder::class,
            FacilitySeeder::class,
            ProductSeeder::class
	    ]);
    }
}

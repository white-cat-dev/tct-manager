<?php

use Illuminate\Database\Seeder;
use App\Facility;


class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $facilities = [
            [
                'name' => 'Цех плитки',
                'status' => Facility::STATUS_ACTIVE,
                'categories' => [1, 3],
                'performance' => 30,
                'icon_color' => '#55d98c'
            ],
            [
                'name' => 'Цех блоков',
                'status' => Facility::STATUS_ACTIVE,
                'categories' => [2],
                'performance' => 8,
                'icon_color' => '#5faee3'
            ]
        ];


        foreach ($facilities as $facilityData) 
        {
        	$categories = $facilityData['categories'];
        	unset($facilityData['categories']);
        	$facility = Facility::create($facilityData);
        	$facility->categories()->attach($categories);
        }
    }
}

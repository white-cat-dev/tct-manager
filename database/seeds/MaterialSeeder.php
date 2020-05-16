<?php

use Illuminate\Database\Seeder;
use App\MaterialGroup;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $materialGroups = [
        	[
        		'name' => 'Цемент',
        		'units' => 'weight_kg',
        		'variations' => '',
		    	'materials' => [
		    		[
				    	'variation' => '',
				    	'price' => 0,
				    	'in_stock' => 100
		    		],
		    	]
        	],
        	[
        		'name' => 'Краситель',
        		'units' => 'volume_l',
        		'variations' => 'colors',
		    	'materials' => [
		    		[
				    	'variation' => 'grey',
				    	'price' => 0,
				    	'in_stock' => 100
		    		],
		    		[
				    	'variation' => 'red',
				    	'price' => 0,
				    	'in_stock' => 100
		    		],
		    		[
				    	'variation' => 'yellow',
				    	'price' => 0,
				    	'in_stock' => 100
		    		],
		    		[
				    	'variation' => 'brown',
				    	'price' => 0,
				    	'in_stock' => 100
		    		],
		    		[
				    	'variation' => 'black',
				    	'price' => 0,
				    	'in_stock' => 100
		    		],
		    	]
        	]
        ];

        foreach ($materialGroups as $materialGroupData) 
        {
        	$materials = $materialGroupData['materials'];
        	unset($materialGroupData['materials']);

        	$materialGroup = MaterialGroup::create($materialGroupData);
        	foreach ($materials as $materialData) 
        	{
        		$materialGroup->materials()->create($materialData);
        	}
        }
    }
}

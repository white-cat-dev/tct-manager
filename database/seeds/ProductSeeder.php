<?php

use Illuminate\Database\Seeder;
use App\ProductGroup;


class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $productGroups = [
        	[
        		'wp_id' => 0,
        		'wp_name' => 'Тротуарная плитка "Паутина"',
        		'name' => 'Паутина',
		        'category_id' => 1,
		    	'set_pair_id' => 0,
		    	'width' => 300,
		    	'length' => 300,
		    	'depth' => 30,
		    	'adjectives' => 'feminine',
		    	'weight_unit' => 5.5,
		    	'weight_units' => 61,
		    	'weight_pallete' => 750,
		    	'unit_in_units' => 11.1,
		    	'unit_in_pallete' => 132,
		    	'units_in_pallete' => 11.9,
		    	'units_from_batch' => 8,
		    	'forms' => 10,
		    	'salary_units' => 80,
		    	'products' => [
		    		[
		    			'category_id' => 1,
				        'product_group_id' => 1,
				    	'variation' => 'grey',
				    	'main_variation' => 'grey',
				    	'price' => 380,
				    	'price_unit' => 35,
				    	'price_pallete' => 4525,
				    	'in_stock' => 5
		    		],
		    		[
		    			'category_id' => 1,
				        'product_group_id' => 1,
				    	'variation' => 'red',
				    	'main_variation' => 'red',
				    	'price' => 470,
				    	'price_unit' => 43,
				    	'price_pallete' => 5595,
				    	'in_stock' => 10
		    		]
		    	]
        	],
        	[
        		'wp_id' => 0,
        		'wp_name' => 'Тротуарная плитка «Калифорния» (300×300×30 мм)',
        		'name' => 'Калифорния',
		        'category_id' => 1,
		    	'set_pair_id' => 0,
		    	'width' => 300,
		    	'length' => 300,
		    	'depth' => 30,
		    	'adjectives' => 'feminine',
		    	'weight_unit' => 5.5,
		    	'weight_units' => 61,
		    	'weight_pallete' => 750,
		    	'unit_in_units' => 11.1,
		    	'unit_in_pallete' => 132,
		    	'units_in_pallete' => 11.9,
		    	'units_from_batch' => 8,
		    	'forms' => 10,
		    	'salary_units' => 80,
		    	'products' => [
		    		[
		    			'category_id' => 1,
				        'product_group_id' => 1,
				    	'variation' => 'grey',
				    	'main_variation' => 'grey',
				    	'price' => 380,
				    	'price_unit' => 35,
				    	'price_pallete' => 4525,
				    	'in_stock' => 0
		    		],
		    		[
		    			'category_id' => 1,
				        'product_group_id' => 1,
				    	'variation' => 'red',
				    	'main_variation' => 'red',
				    	'price' => 470,
				    	'price_unit' => 43,
				    	'price_pallete' => 5595,
				    	'in_stock' => 10
		    		]
		    	]
        	],
        	[
        		'wp_id' => 0,
        		'wp_name' => 'Бордюр (500×200×30)',
        		'name' => 'Бордюр',
		        'category_id' => 2,
		    	'set_pair_id' => 0,
		    	'width' => 500,
		    	'length' => 200,
		    	'depth' => 30,
		    	'adjectives' => 'masculine',
		    	'weight_unit' => 11,
		    	'weight_units' => 11,
		    	'weight_pallete' => 550,
		    	'unit_in_units' => 1,
		    	'unit_in_pallete' => 49,
		    	'units_in_pallete' => 49,
		    	'units_from_batch' => 20,
		    	'forms' => 20,
		    	'salary_units' => 10,
		    	'products' => [
		    		[
		    			'category_id' => 2,
				        'product_group_id' => 3,
				    	'variation' => 'grey',
				    	'main_variation' => 'grey',
				    	'price' => 50,
				    	'price_unit' => 50,
				    	'price_pallete' => 2450,
				    	'in_stock' => 5
		    		],
		    		[
		    			'category_id' => 2,
				        'product_group_id' => 3,
				    	'variation' => 'red',
				    	'main_variation' => 'red',
				    	'price' => 70,
				    	'price_unit' => 70,
				    	'price_pallete' => 3430,
				    	'in_stock' => 5
		    		]
		    	]
        	],
        	[
        		'wp_id' => 0,
        		'wp_name' => 'Полистиролбетонный блок (большой)',
        		'name' => 'Полистиролбетон',
		        'category_id' => 3,
		    	'set_pair_id' => 0,
		    	'width' => 600,
		    	'length' => 300,
		    	'depth' => 200,
                'adjectives' => 'masculine',
		    	'weight_unit' => 10,
		    	'weight_units' => 278,
		    	'weight_pallete' => 550,
		    	'unit_in_units' => 27.78,
		    	'unit_in_pallete' => 32,
		    	'units_in_pallete' => 1.15,
		    	'units_from_batch' => 10,
		    	'forms' => 10,
		    	'salary_units' => 350,
		    	'products' => [
		    		[
		    			'category_id' => 3,
				        'product_group_id' => 4,
				    	'variation' => 'd400',
				    	'main_variation' => 'd400',
				    	'price' => 400,
				    	'price_unit' => 144,
				    	'price_pallete' => 4610,
				    	'in_stock' => 40
		    		],
		    		[
		    			'category_id' => 3,
				        'product_group_id' => 4,
				    	'variation' => 'd500',
				    	'main_variation' => 'd400',
				    	'price' => 420,
				    	'price_unit' => 154,
				    	'price_pallete' => 4710,
				    	'in_stock' => 30
		    		],
		    	]
        	]
        ];

        foreach ($productGroups as $productGroupData) 
        {
        	$products = $productGroupData['products'];
        	unset($productGroupData['products']);

        	$productGroup = ProductGroup::create($productGroupData);
        	foreach ($products as $productData) 
        	{
        		$productGroup->products()->create($productData);
        	}
        }
    }
}

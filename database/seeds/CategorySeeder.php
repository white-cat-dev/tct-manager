<?php

use Illuminate\Database\Seeder;
use App\Category;


class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::insert([
            [
                'name' => 'Тротуарная плитка',
                'units' => 'area',
                'has_colors' => true
            ],
            [
                'name' => 'Бордюры',
                'units' => 'unit',
                'has_colors' => true
            ],
            [
                'name' => 'Пенобетонные блоки',
                'units' => 'volume',
                'has_colors' => false
            ]
        ]);
    }
}

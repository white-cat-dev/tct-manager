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
                'variations' => 'colors',
                'adjectives' => 'feminine'
            ],
            [
                'name' => 'Бордюры',
                'units' => 'unit',
                'variations' => 'colors',
                'adjectives' => 'masculine'
            ],
            [
                'name' => 'Строительные блоки',
                'units' => 'volume',
                'variations' => 'grades',
                'adjectives' => 'masculine'
            ]
        ]);
    }
}

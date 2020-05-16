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
                'name' => 'Плитка',
                'main_category' => 'tiles',
                'units' => 'area',
                'variations' => 'colors',
                'adjectives' => 'feminine'
            ],
            [
                'name' => 'Брусчатка',
                'main_category' => 'tiles',
                'units' => 'area',
                'variations' => 'colors',
                'adjectives' => 'feminine'
            ],
            [
                'name' => 'Бордюры',
                'main_category' => 'tiles',
                'units' => 'unit',
                'variations' => 'colors',
                'adjectives' => 'masculine'
            ],
            [
                'name' => 'Водостоки',
                'main_category' => 'tiles',
                'units' => 'unit',
                'variations' => 'colors',
                'adjectives' => 'masculine'
            ],
            [
                'name' => 'Полистиролбетон',
                'main_category' => 'blocks',
                'units' => 'volume',
                'variations' => '',
                'adjectives' => 'masculine'
            ]
        ]);
    }
}

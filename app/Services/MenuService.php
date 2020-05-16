<?php

namespace App\Services;

use DB;
use App\Category;


class MenuService
{
    public function getItems()
    {
        $categories = Category::all();
        $categoriesMenu = [];
        foreach ($categories as $category) 
        {
            $categoriesMenu[] = (object)[
                'name' => $category->name,
                'url' => route('products', ['category' => $category->id])
            ];
        }

        $menu = [
            (object)[
                'name' => 'Категории',
                'icon' => '<i class="fas fa-list"></i>',
                'url' => route('categories')
            ],
            (object)[
                'name' => 'Продукты',
                'icon' => '<i class="fas fa-cubes"></i>',
                'url' => route('products'),
                // 'submenu' => $categoriesMenu
            ],
            (object)[
                'name' => 'Материалы',
                'icon' => '<i class="fas fa-tools"></i>',
                'url' => route('materials')
            ],
            (object)[
                'name' => 'Рецепты',
                'icon' => '<i class="fas fa-scroll"></i>',
                'url' => route('recipes')
            ],
            // (object)[
            //     'name' => 'Клиенты',
            //     'icon' => '<i class="fas fa-users"></i>',
            //     'url' => route('clients')
            // ],
            (object)[
                'name' => 'Заказы',
                'icon' => '<i class="fas fa-shopping-cart"></i>',
                'url' => route('orders')
            ],
            (object)[
                'name' => 'Производство',
                'icon' => '<i class="far fa-calendar-check"></i>',
                'url' => route('productions')
            ],
            (object)[
                'name' => 'Цехи',
                'icon' => '<i class="fas fa-warehouse"></i>',
                'url' => route('facilities')
            ],
            (object)[
                'name' => 'Работники',
                'icon' => '<i class="fas fa-user-tie"></i>',
                'url' => route('workers')
            ],
            (object)[
                'name' => 'График работ',
                'icon' => '<i class="far fa-calendar-alt"></i>',
                'url' => route('employments')
            ]
        ];

        return $menu;
    }
}
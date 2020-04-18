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
                'url' => route('categories')
            ],
            (object)[
                'name' => 'Продукты',
                'url' => route('products'),
                // 'submenu' => $categoriesMenu
            ],
            (object)[
                'name' => 'Клиенты',
                'url' => route('clients')
            ],
            (object)[
                'name' => 'Заказы',
                'url' => route('orders')
            ],
            (object)[
                'name' => 'Производство',
                'url' => route('production')
            ],
            (object)[
                'name' => 'Цехи',
                'url' => route('facilities')
            ],
            (object)[
                'name' => 'Работники',
                'url' => route('workers')
            ],
            (object)[
                'name' => 'График работ',
                'url' => route('employments')
            ]
        ];

        return $menu;
    }
}
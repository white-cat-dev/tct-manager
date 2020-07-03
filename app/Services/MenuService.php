<?php

namespace App\Services;


class MenuService
{
    public function getItems()
    {
        $menu = [
            (object)[
                'name' => 'Управление',
                'icon' => '<i class="far fa-check-square"></i>',
                'url' => '',
                'submenu' => [
                    (object)[
                        'name' => 'Категории',
                        'icon' => '<i class="fas fa-list"></i>',
                        'url' => route('categories')
                    ],
                    (object)[
                        'name' => 'Рецепты',
                        'icon' => '<i class="fas fa-scroll"></i>',
                        'url' => route('recipes')
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
                    ]
                ],
                'submenu_opened' => true
            ],
            (object)[
                'name' => 'Продукты',
                'icon' => '<i class="fas fa-cubes"></i>',
                'url' => route('products'),
            ],
            (object)[
                'name' => 'Материалы',
                'icon' => '<i class="fas fa-tools"></i>',
                'url' => route('materials')
            ],
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
                'name' => 'График работ',
                'icon' => '<i class="far fa-calendar-alt"></i>',
                'url' => route('employments')
            ]
        ];

        return $menu;
    }
}
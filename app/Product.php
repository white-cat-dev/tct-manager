<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Arr;


class Product extends Model
{
    protected $fillable = [
        'category_id',
        'product_group_id',
    	'variation',
        'main_variation',
    	'price',
        'price_vat',
        'price_cashless',
    	'price_unit',
        'price_unit_vat',
        'price_unit_cashless',
    	'in_stock'
    ];

    protected $casts = [
        'price' => 'float',
        'price_vat' => 'float',
        'price_cashless' => 'float',
        'price_unit' => 'float',
        'price_unit_vat' => 'float',
        'price_unit_cashless' => 'float',
        'in_stock' => 'float'
    ];

    protected $appends = [
        'realize_in_stock',
        'free_in_stock',
        'planned',
        'variation_text',
        'variation_noun_text',
        'main_variation_text',
        'units_text'
    ];

    protected $with = [
        'product_group',
        'category'
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function product_group()
    {
        return $this->belongsTo(ProductGroup::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'orders_products')->withPivot('price', 'count', 'cost')->using('App\OrderProduct');
    }

    public function realizations()
    {
        return $this->hasMany(Realization::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }

    public function stocks()
    {
        return $this->morphMany(Stock::class, 'model');
    }


    public function getProgress($order)
    {
        $progress = [
            'total' => 0,
            'production' => 0,
            'realization' => 0,
            'ready' => 0,
            'left' => 0
        ];

        $product = $order->products->where('id', $this->id)->first();

        if ($product)
        {
            $progress['total'] = $product->pivot->count;

            // $baseProduction = $this->productions()->where('order_id', $order->id)->whereNull('date')->first();

            // $progress['production'] = round($baseProduction ? ($baseProduction->performed) : 0, 3);
            
            foreach ($this->realizations()->where('order_id', $order->id)->whereNotNull('date')->get() as $realization) 
            {
                $progress['realization'] += $realization->performed;
            }

            $progress['realization'] = round($progress['realization'], 3);
            // $progress['production'] = round($progress['production'], 3);
            // $progress['left'] = round($progress['total'] - $progress['production'], 3);
            // $progress['ready'] = round($progress['production'] - $progress['realization'], 3);
        }

        return $progress;
    }


    public function getPlannedAttribute()
    {
        $baseProduction = $this->getBaseProduction();
        if ($baseProduction)
        {
            return $baseProduction->auto_planned;
        }
        else
        {
            return 0;
        }
    }


    public function getRealizeInStockAttribute()
    {
        $baseProduction = $this->getBaseProduction();
        if ($baseProduction)
        {
            return $baseProduction->performed;
        }
        else
        {
            return 0;
        }
    }

    public function getFreeInStockAttribute()
    {
        if ($this->in_stock > $this->realize_in_stock)
        {
            return round($this->in_stock - $this->realize_in_stock, 3);
        }
        else
        {
            return 0;
        }
    }


    public function getBaseProduction()
    {
        return $this->productions()->whereNull('date')->where('order_id', 0)->first();
    }

    public function getRealized($orderId = null)
    {
        $production = 0;
        $productions = $this->productions;
        if ($orderId)
        {
            $productions = $productions->where('order_id', $realizations);
        }
        foreach ($productions as $production) 
        {
            $production += $production->planned - $production->performed;
        }

        return $production;
    }


    public function getUnitsTextAttribute()
    {
        switch ($this->category->units) 
        {
            case 'area':
                return 'м<sup>2</sup>';
                break;

            case 'volume':
                return 'м<sup>3</sup>';
                break;

            case 'unit':
                return 'шт';
                break;
            
            default:
                return '';
                break;
        }
    }


    public function getVariationTextAttribute()
    {
        if ($this->category->variations == 'colors')
        {
            $allColors = static::$allVariations['colors'];
            $adjectives = $this->product_group->adjectives;
            $colors = !empty($allColors[$adjectives]) ? $allColors[$adjectives] : [];

            return Arr::get($colors, $this->variation, '');
        }
        else if ($this->category->variations == 'grades')
        {
            $grades = static::$allVariations['grades'];

            return Arr::get($grades, $this->variation, '');
        }
        else
        {
            return '';
        }
    }


    public function getMainVariationTextAttribute()
    {
        if ($this->category->variations == 'colors')
        {
            $allColors = static::$allVariations['colors'];
            $adjectives = $this->product_group->adjectives;
            $colors = !empty($allColors[$adjectives]) ? $allColors[$adjectives] : [];

            return Arr::get($colors, $this->main_variation, '');
        }
        else if ($this->category->variations == 'grades')
        {
            $grades = static::$allVariations['grades'];

            return Arr::get($grades, $this->main_variation, '');
        }
        else
        {
            return '';
        }
    }


    public function getVariationNounTextAttribute()
    {
        if ($this->category->variations == 'colors')
        {
            $colors = static::$allVariations['colors']['masculine'];

            return Arr::get($colors, $this->variation, 'неизвестный') . ' цвет';
        }
        else if ($this->category->variations == 'grades')
        {
            $grades = static::$allVariations['grades'];
            $grade = Arr::get($grades, $this->variation, '');

            return $grade ? ('марка ' . $grade) : 'неизвестная марка';
        }
        else
        {
            return '';
        }
    }


    protected static $allVariations = [
        'colors' => [
            'feminine' => [
                'grey' => 'серая',
                'red' => 'красная',
                'color' => 'цветная',
                'yellow' => 'желтая',
                'brown' => 'коричневая',
                'black' => 'черная'
            ],
            'masculine' => [
                'grey' => 'серый',
                'red' => 'красный',
                'color' => 'цветной',
                'yellow' => 'желтый',
                'brown' => 'коричневый',
                'black' => 'черный'
            ],
            'neuter' => [
                'grey' => 'серое',
                'red' => 'красное',
                'color' => 'цветное',
                'yellow' => 'желтое',
                'brown' => 'коричневое',
                'black' => 'черное'
            ]
        ],
        'grades' => [
            'd400' => 'D400',
            'd500' => 'D500',
            'd600' => 'D600'
        ]
    ];
}

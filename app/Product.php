<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\ProductionsService;
use Arr;


class Product extends Model
{
    use SoftDeletes;

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
        'units_text',
        // 'in_stock_text'
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
            'left' => 0,
            'planned' => 0
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
            $progress['planned'] = round($progress['total'] - $progress['realization'], 3);
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
        $baseProduction = $this->productions()->whereNull('date')->where('order_id', 0)->first();

        if (!$baseProduction)
        {
            $baseProduction = Production::create([
                'date' => null,
                'category_id' => $this->category_id,
                'product_group_id' => $this->product_group_id,
                'product_id' => $this->id,
                'order_id' => 0,
                'facility_id' => 0,
                'manual_planned' => -1,
                'auto_planned' => 0,
                'priority' => Order::PRIORITY_NORMAL,
                'date_to' => null,
                'performed' => 0,
                'auto_batches' => 0,
                'manual_batches' => -1,
                'salary' => 0
            ]);
        }
        
        return $baseProduction;
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


    public function updateInStock($newInStock, $reason, $process = null)
    {
        if ($this->in_stock == $newInStock)
        {
            return;
        }

        $processTypes = [
            'production' => 'App\Production',
            'realization' => 'App\Realization',
            'manual' => ''
        ];

        $productStock = $this->stocks()->create([
            'date' => date('Y-m-d'),
            'process_date' => !empty($process) ? $process->date : date('Y-m-d'),
            'process_id' => !empty($process) ? $process->id : 0,
            'process_type' => !empty($processTypes[$reason]) ? $processTypes[$reason] : '',
            'in_stock' => $this->in_stock,
            'new_in_stock' => $newInStock,
            'reason' => $reason
        ]);

        $this->update([
            'in_stock' => $newInStock
        ]);

        $baseProduction = $this->getBaseProduction();

        if ($baseProduction)
        {
            $baseProduction->update([
                'performed' => ($this->in_stock > $baseProduction->auto_planned) ? $baseProduction->auto_planned : $this->in_stock
            ]);

            ProductionsService::getInstance()->replanProduct($this);
        }
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


    public function getInStockTextAttribute()
    {
        $units = $this->units_text;

        if ($this->free_in_stock == 0)
        {
            return 'под заказ';
        }
        // else if ($this->free_in_stock < 5)
        // {
        //     return 'меньше 5 ' . $units;
        // }
        // else if ($this->free_in_stock < 10)
        // {
        //     return 'меньше 10 ' . $units;
        // }
        // else if ($this->free_in_stock < 50)
        // {
        //     return 'меньше 50 ' . $units;
        // }
        // else
        // {
        //     return 'больше 50 ' . $units;
        // }
        else
        {
            return floor($this->free_in_stock) . ' ' . $units;
        }
    }


    public function getExportInStockAttribute()
    {
        return floor($this->free_in_stock);
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
                'black' => 'черная',
                'white' => 'белая',
                'orange' => 'оранжевая',
                'light-brown' => 'светло-коричневая'
            ],
            'masculine' => [
                'grey' => 'серый',
                'red' => 'красный',
                'color' => 'цветной',
                'yellow' => 'желтый',
                'brown' => 'коричневый',
                'black' => 'черный',
                'white' => 'белый',
                'orange' => 'оранжевый',
                'light-brown' => 'светло-коричневый'
            ],
            'neuter' => [
                'grey' => 'серое',
                'red' => 'красное',
                'color' => 'цветное',
                'yellow' => 'желтое',
                'brown' => 'коричневое',
                'black' => 'черное',
                'white' => 'белый',
                'orange' => 'оранжевый',
                'light-brown' => 'светло-коричневый'
            ]
        ],
        'grades' => [
            'd400' => 'D400',
            'd500' => 'D500',
            'd600' => 'D600'
        ]
    ];
}

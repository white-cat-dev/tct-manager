<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Arr;


class Product extends Model
{
    protected $fillable = [
        'category_id',
        'product_group_id',
    	'color',
    	'price',
    	'price_unit',
    	'price_pallete',
    	'in_stock'
    ];

    protected $casts = [
        'price' => 'float',
        'price_unit' => 'float',
        'price_pallete' => 'float'
    ];

    protected $appends = [
        'realize_in_stock',
        'free_in_stock',
        'color_text'
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

    public function getProducted($orderId = null)
    {

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

            foreach ($this->productions->where('order_id', $order->id) as $production) 
            {
                $progress['production'] += $production->performed;
            }
            foreach ($this->realizations->where('order_id', $order->id) as $realization) 
            {
                $progress['realization'] += $realization->performed;
                $progress['production'] += $realization->planned;
            }

            $progress['ready'] = $progress['production'] - $progress['realization'];
            $progress['left'] = $progress['total'] - $progress['production'];
        }

        return $progress;
    }


    public function getRealizeInStockAttribute()
    {
        $realizeInStock = 0;
        foreach ($this->realizations as $realization) 
        {
            $realizeInStock += $realization->planned - $realization->performed;
        }

        return $realizeInStock;
    }

    public function getFreeInStockAttribute()
    {
        return $this->in_stock - $this->realize_in_stock;
    }

    public function getColorTextAttribute()
    {
        $colors = [
            'red' => 'красный',
            'grey' => 'серый',
            'yellow' => 'желтый'
        ];

        return Arr::get($colors, $this->color, $this->color);
    }
}

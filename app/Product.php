<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Arr;


class Product extends Model
{
    protected $fillable = [
    	'color',
    	'price',
    	'price_unit',
    	'price_pallete',
    	'in_stock'
    ];

    protected $casts = [
        'price' => 'float',
    ];

    protected $appends = [
        'realize_in_stock',
        'free_in_stock',
        'color_text'
    ];


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
            'grey' => 'серый'
        ];

        return Arr::get($colors, $this->color, $this->color);
    }
}

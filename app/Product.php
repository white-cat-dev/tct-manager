<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    protected $fillable = [
    	'color',
    	'price',
    	'price_unit',
    	'price_pallete',
    	'in_stock',
    ];


    public function product_group()
    {
        return $this->belongsTo(ProductGroup::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}

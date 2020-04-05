<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class ProductGroup extends Model
{
    protected $fillable = [
    	'name',
    	'set_pair_id',
        'category_id',
    	'width',
    	'length',
    	'depth',
    	'weight_unit',
    	'weight_square',
    	'weight_pallete',
    	'units_in_square',
    	'units_in_pallete',
    	'squares_in_pallete',
    	'squares_from_batch',
    	'forms'
    ];

    protected $appends = [
        'url',
        'size'
    ];

    protected $with = [
        'products'
    ];


    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function set_pair()
    {
        return $this->hasOne(Product::class);
    }

    public function getUrlAttribute()
    {
        return route('product-show', ['productGroup' => $this->id]);
    }

    public function getSizeAttribute()
    {
        return $this->length . 'x' . $this->width . 'x' . $this->depth;
    }
}

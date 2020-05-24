<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Category extends Model
{
    protected $fillable = [
    	'name',
        'main_category',
        'units',
        'variations',
        'adjectives'
    ];

    protected $appends = [
    	'url'
    ];


    public function product_groups()
    {
        return $this->hasMany(ProductGroup::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }
    

    public function getUrlAttribute()
    {
    	return route('category-show', ['category' => $this->id]);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Category extends Model
{
    protected $fillable = [
    	'name',
        'units',
        'has_colors'
    ];

    protected $appends = [
    	'url'
    ];


    public function productGroups()
    {
        return $this->hasMany(ProductGroup::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getUrlAttribute()
    {
    	return route('category-show', ['category' => $this->id]);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
    	'name',
        'main_category',
        'units',
        'variations',
        'adjectives'
    ];

    protected $appends = [
    	'url',
        'units_text'
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

    public function getUnitsTextAttribute()
    {
        switch ($this->units) 
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
}

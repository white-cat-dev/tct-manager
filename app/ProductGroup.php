<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class ProductGroup extends Model
{
    protected $fillable = [
        'wp_name',
        'wp_slug',
    	'name',
        'category_id',
    	'set_pair_id',
    	'width',
    	'length',
    	'depth',
        'adjectives',
    	'weight_unit',
    	'weight_units',
    	'weight_pallete',
    	'unit_in_units',
    	'unit_in_pallete',
    	'units_in_pallete',
    	'units_from_batch',
    	'forms',
        'salary_units',
        'recipe_id'
    ];

    protected $appends = [
        'url',
        'size'
    ];

    protected $with = [
        'category'
    ];

    protected $casts = [
        'weight_unit' => 'float',
        'weight_units' => 'float',
        'weight_pallete' => 'float',
        'unit_in_units' => 'float',
        'unit_in_pallete' => 'float',
        'units_in_pallete' => 'float',
        'units_from_batch' => 'float',
        'salary_units' => 'float'
    ];


    public static function boot() 
    {
        parent::boot();

        static::deleting(function($productGroup) 
        {
            $productGroup->products()->delete();
        });
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

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
        return $this->length . '×' . $this->width . '×' . $this->depth;
    }
}

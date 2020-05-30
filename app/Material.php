<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Arr;


class Material extends Model
{
    protected $fillable = [
    	'material_group_id',
        'variation',
        'price',
        'in_stock'
    ];

    protected $casts = [
        'price' => 'float',
        'in_stock' => 'float'
    ];


    protected $appends = [
        'variation_text'
    ];

    protected $with = [
        'material_group'
    ];


    public function material_group()
    {
        return $this->belongsTo(MaterialGroup::class);
    }

    public function supplies()
    {
        return $this->hasMany(MaterialSupply::class);
    }

    public function applies()
    {
        return $this->hasMany(MaterialApply::class);
    }

    public function stocks()
    {
        return $this->morphMany(Stock::class, 'model');
    }


    public function getVariationTextAttribute()
    {
        if ($this->material_group->variations == 'colors')
        {
            return Arr::get(static::$allVariations['colors'], $this->variation, '');
        }
        else
        {
        	return '';
        }
    }


    protected static $allVariations = [
        'colors' => [
            'grey' => 'серый',
            'red' => 'красный',
            'yellow' => 'желтый',
            'brown' => 'коричневый',
            'black' => 'черный'
        ]
    ];
}

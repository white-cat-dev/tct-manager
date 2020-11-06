<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Recipe extends Model
{
    protected $fillable = [
        'name'
    ];

    protected $with = [
    	'material_groups'
    ];

    protected $appends = [
        'url',
        'cost'
    ];


    public function material_groups() 
	{
		return $this->belongsToMany(MaterialGroup::class, 'recipes_material_groups')->withPivot('count')->using(RecipeMaterialGroup::class);
	}


    public function getUrlAttribute()
    {
        return route('recipe-show', ['recipe' => $this->id]);
    }


    public function getCostAttribute()
    {
        return round($this->material_groups->sum(function($materialGroup) {
            return $materialGroup->materials->first()->price * $materialGroup->pivot->count;
        }), 3);
    }
}

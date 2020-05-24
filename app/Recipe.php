<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Recipe extends Model
{
    protected $fillable = [
        'name'
    ];

    protected $with = [
    	'material_groups'
    ];

    protected $appends = [
        'url'
    ];


    public function material_groups() 
	{
		return $this->belongsToMany(MaterialGroup::class, 'recipes_material_groups')->withPivot('count')->using(RecipeMaterialGroup::class);
	}


    public function getUrlAttribute()
    {
        return route('recipe-show', ['recipe' => $this->id]);
    }
}

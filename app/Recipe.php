<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Recipe extends Model
{
    protected $fillable = [
        'name',
        'category_id'
    ];

    protected $with = [
    	'material_groups',
        'category'
    ];

    protected $appends = [
        'url'
    ];


    public function material_groups() 
	{
		return $this->belongsToMany(MaterialGroup::class, 'recipes_material_groups')->withPivot('count')->using(RecipeMaterialGroup::class);
	}

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function getUrlAttribute()
    {
        return route('recipe-show', ['recipe' => $this->id]);
    }
}

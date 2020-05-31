<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialGroup extends Model
{
    protected $fillable = [
    	'name',
        'units',
        'variations',
        'control'
    ];


    protected $appends = [
    	'url'
    ];


    public static function boot() 
    {
        parent::boot();

        static::deleting(function($materialGroup) 
        {
            $materialGroup->materials()->delete();
        });
    }


    public function materials()
    {
        return $this->hasMany(Material::class);
    }


    public function getUrlAttribute()
    {
        return route('material-show', ['materialGroup' => $this->id]);
    }
}

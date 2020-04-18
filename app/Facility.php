<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Facility extends Model
{
    protected $fillable = [
    	'name',
        'status',
        'performance'
    ];

    protected $appends = [
        'url'
    ];

    protected $with = [
        'workers',
        'categories'
    ];

    protected $casts = [
        'performance' => 'float'
    ];

    protected static $statuses = [
        'active' => 'Работает',
        'paused' => 'Приостановлен'
    ];


    public function workers() 
    {
    	return $this->hasMany(Worker::class);
    }

    public function categories() 
    {
    	return $this->belongsToMany(Category::class, 'facilities_categories');
    }

    public function getUrlAttribute()
    {
        return route('facility-show', ['facility' => $this->id]);
    }
}

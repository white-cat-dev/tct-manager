<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Client extends Model
{
    protected $fillable = [
    	'name',
     	'phone',
     	'email'
    ];

    protected $appends = [
    	'url'
    ];


    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getUrlAttribute()
    {
    	return route('client-show', ['client' => $this->id]);
    }
}

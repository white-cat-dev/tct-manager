<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Client extends Model
{
    use SoftDeletes;

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

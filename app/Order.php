<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    protected $fillable = [
    	'client_id',
        'priority',
        'cost'
    ];

    protected $appends = [
    	'url'
    ];

    protected $with = [
        'client',
        'products'
    ];

    protected $casts = [
        'cost' => 'float',
    ];


    public function products()
    {
        return $this->belongsToMany(Product::class, 'orders_products')->withPivot('price', 'count', 'cost')->using('App\OrderProduct');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function getUrlAttribute()
    {
        return route('order-show', ['order' => $this->id]);
    }

    public function realizations()
    {
        return $this->hasMany(Realization::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }
}

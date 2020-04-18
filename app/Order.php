<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    protected $fillable = [
    	'client_id',
        'status',
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

    public function getProgress()
    {
        $progress = [
            'total' => 0,
            'production' => 0,
            'realization' => 0,
            'ready' => 0
        ];

        foreach ($this->products as $product) 
        {
            $progress['total'] += $product->pivot->count;
            $progress['production'] += $product->getProgress($this)['production'];
            $progress['realization'] += $product->getProgress($this)['realization'];
        }

        $progress['ready'] = $progress['production'] - $progress['realization'];

        return $progress;
    }
}

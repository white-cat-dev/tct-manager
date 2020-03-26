<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    protected $fillable = [

    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function client()
    {
        return $this->belongTo(Client::class);
    }
}

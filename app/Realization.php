<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Realization extends Model
{
    protected $fillable = [
    	'date',
    	'product_id',
        'order_id',
        'planned',
        'performed'
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

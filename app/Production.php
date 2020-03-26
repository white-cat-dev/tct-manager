<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Production extends Model
{
    const STATUS_NEW = 0;

    protected $fillable = [
    	'date',
    	'product_id',
    	'order_id',
    	'planned',
    	'performed',
    	'status'
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Stock extends Model
{
    protected $fillable = [
    	'date',
    	'model_id',
    	'model_type',
    	'in_stock',
        'new_in_stock'
    ];

    protected $casts = [
        'in_stock' => 'float',
        'new_in_stock' => 'float'
    ];


    public function model()
    {
        return $this->morphTo();
    }
}

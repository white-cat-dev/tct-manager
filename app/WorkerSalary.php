<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class WorkerSalary extends Model
{
    protected $fillable = [
    	'worker_id',
    	'date',
    	'employments',
        'tax',
        'lunch',
    	'advance',
    	'bonus',
        'surcharge'
    ];

    protected $casts = [
        'employments' => 'float',
        'advance' => 'float',
        'bonus' => 'float',
        'lunch' => 'float',
        'tax' => 'float',
        'surcharge' => 'float'
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class WorkerSalary extends Model
{
    protected $fillable = [
    	'worker_id',
    	'date',
    	'employments',
    	'advance',
    	'bonus'
    ];

    protected $casts = [
        'employments' => 'float',
        'advance' => 'float',
        'bonus' => 'float'
    ];
}

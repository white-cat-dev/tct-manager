<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class EmploymentStatus extends Model
{
    use SoftDeletes;

    protected $fillable = [
    	'icon',
        'icon_color',
        'name',
        'type',
        'base_salary',
        'salary',
        'default_salary',
        'customable',
    ];

    protected $casts = [
        'base_salary' => 'float',
        'salary' => 'float',
        'default_salary' => 'float',
        'customable' => 'boolean'
    ];
}

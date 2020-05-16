<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class EmploymentStatus extends Model
{
    protected $fillable = [
    	'icon',
        'icon_color',
        'name',
        'salary_production',
        'salary_fixed',
        'salary_team',
        'customable'
    ];

    protected $casts = [
        'salary_production' => 'float',
        'salary_fixed' => 'float',
        'salary_team' => 'float'
    ];
}

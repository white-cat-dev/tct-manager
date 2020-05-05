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
        'salary_team'
    ];

    protected $casts = [
        'salary' => 'float'
    ];
}

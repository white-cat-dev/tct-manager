<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class EmploymentStatus extends Model
{
    protected $fillable = [
    	'icon',
        'icon_color',
        'name',
        'salary'
    ];

    protected $casts = [
        'salary' => 'float'
    ];
}

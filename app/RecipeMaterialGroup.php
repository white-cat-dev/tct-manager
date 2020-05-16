<?php 

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;


class RecipeMaterialGroup extends Pivot 
{
    protected $casts = [
        'count' => 'float',
    ];
}

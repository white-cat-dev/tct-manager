<?php 

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;


class OrderProduct extends Pivot 
{
    protected $casts = [
        'price' => 'float',
        'count' => 'float',
        'cost' => 'float',
    ];
}

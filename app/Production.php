<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Production extends Model
{
    protected $fillable = [
    	'date',
    	'product_id',
    	'order_id',
    	'planned',
    	'performed',
        'batches'
    ];

    protected $appends = [
        'day'
    ];

    protected $casts = [
        'planned' => 'float',
        'performed' => 'float'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }


    public function getDayAttribute()
    {
        return Carbon::createFromDate($this->date)->day;
    }
}

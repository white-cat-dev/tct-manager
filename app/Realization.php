<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Realization extends Model
{
    protected $fillable = [
    	'date',
        'category_id',
    	'product_id',
        'order_id',
        'planned',
        'performed'
    ];

    protected $appends = [
        'day',
        'formatted_date'
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

    public function getFormattedDateAttribute()
    {
        return Carbon::createFromDate($this->date)->format('d.m.Y');
    }
}

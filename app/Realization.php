<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Realization extends Model
{
    protected $fillable = [
    	'date',
        'category_id',
        'product_group_id',
    	'product_id',
        'order_id',
        'planned',
        'ready',
        'performed'
    ];

    protected $appends = [
        'day',
        'formatted_date'
    ];

    protected $casts = [
        'planned' => 'float',
        'ready' => 'float',
        'performed' => 'float'
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function product_group()
    {
        return $this->belongsTo(ProductGroup::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
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

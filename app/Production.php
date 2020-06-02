<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Production extends Model
{
    protected $fillable = [
    	'date',
        'category_id',
        'product_group_id',
    	'product_id',
    	'order_id',
        'facility_id',
        'auto_planned',
        'manual_planned',
    	'performed',
        'batches',
        'salary'
    ];

    protected $appends = [
        'day',
        'formatted_date',
        'planned'
    ];

    protected $casts = [
        'auto_planned' => 'float',
        'manual_planned' => 'float',
        'performed' => 'float',
        'salary' => 'float',
        'batches' => 'float'
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
        if ($this->date)
        {
            return Carbon::createFromDate($this->date)->day;
        }
        else
        {
            return 0;
        }
    }

    public function getFormattedDateAttribute()
    {
        return Carbon::createFromDate($this->date)->format('d.m.Y');
    }


    public function getPlannedAttribute()
    {
        if (!empty($this->manual_planned))
        {
            return $this->manual_planned;
        }
        else 
        {
            return $this->auto_planned;
        }
    }
}

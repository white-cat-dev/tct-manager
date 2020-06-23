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
        'date_to',
        'priority',
    	'performed',
        'auto_batches',
        'manual_batches',
        'salary'
    ];

    protected $appends = [
        'day',
        'formatted_date',
        'formatted_date_to',
        'planned',
        'batches'
    ];

    protected $casts = [
        'auto_planned' => 'float',
        'manual_planned' => 'float',
        'performed' => 'float',
        'salary' => 'float',
        'auto_batches' => 'float',
        'manual_batches' => 'float'
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

    public function getFormattedDateToAttribute()
    {
        return Carbon::createFromDate($this->date_to)->format('d.m.Y');
    }


    public function getPlannedAttribute()
    {
        if ($this->manual_planned >= 0)
        {
            return $this->manual_planned;
        }
        else 
        {
            return $this->auto_planned;
        }
    }

    public function getBatchesAttribute()
    {
        if ($this->manual_batches >= 0)
        {
            return $this->manual_batches;
        }
        else 
        {
            return $this->auto_batches;
        }
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class OrderPayment extends Model
{
    protected $fillable = [
    	'date',
    	'order_id',
        'paid'
    ];

    protected $appends = [
        'formatted_date'
    ];

    protected $casts = [
        'paid' => 'float'
    ];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }


    public function getFormattedDateAttribute()
    {
        return Carbon::createFromDate($this->date)->format('d.m.Y');
    }
}

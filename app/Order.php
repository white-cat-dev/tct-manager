<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Arr;


class Order extends Model
{
    const STATUS_PRODUCTION = 1;
    const STATUS_READY = 2;
    const STATUS_NEW = 3;
    const STATUS_PAUSED = 4;
    const STATUS_FINISHED = 5;

    protected $fillable = [
        'date',
        'number',
    	'client_id',
        'status',
        'comment',
        'priority',
        'cost',
        'weight',
        'pallets'
    ];

    protected $appends = [
    	'url',
        'status_text',
        'formatted_date'
    ];

    protected $with = [
        'client',
        'products'
    ];

    protected $casts = [
        'cost' => 'float',
        'weight' => 'float'
    ];

    protected static $statuses = [
        0 => 'Нет статуса',
        1 => 'В работе',
        2 => 'Готов к выдаче',
        3 => 'Новый',
        4 => 'Приостановлен',
        5 => 'Завершен'
    ];


    public function products()
    {
        return $this->belongsToMany(Product::class, 'orders_products')->withPivot('price', 'count', 'cost')->using(OrderProduct::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function realizations()
    {
        return $this->hasMany(Realization::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }


    public function getUrlAttribute()
    {
        return route('order-show', ['order' => $this->id]);
    }


    public function getStatusTextAttribute()
    {
        $status = Arr::get(static::$statuses, $this->status, '');
        
        return $status;
    }

    public function getFormattedDateAttribute()
    {
        return Carbon::createFromDate($this->date)->format('d.m.Y');
    }


    public function getProgress()
    {
        $progress = [
            'total' => 0,
            'production' => 0,
            'realization' => 0,
            'ready' => 0
        ];

        foreach ($this->products as $product) 
        {
            $progress['total'] += $product->pivot->count;
            $progress['production'] += $product->getProgress($this)['production'];
            $progress['realization'] += $product->getProgress($this)['realization'];
        }

        $progress['ready'] = $progress['production'] - $progress['realization'];

        return $progress;
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Arr;


class Order extends Model
{
    const STATUS_PRODUCTION = 0;
    const STATUS_READY = 1;
    const STATUS_NEW = 2;
    const STATUS_PAUSED = 3;
    const STATUS_FINISHED = 4;

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
        0 => 'В работе',
        1 => 'Готов к выдаче',
        2 => 'Новый',
        3 => 'Приостановлен',
        4 => 'Завершен'
    ];


    public function products()
    {
        return $this->belongsToMany(Product::class, 'orders_products')->withPivot('price', 'count', 'cost')->using('App\OrderProduct');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function getUrlAttribute()
    {
        return route('order-show', ['order' => $this->id]);
    }

    public function realizations()
    {
        return $this->hasMany(Realization::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
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


    public function getStatusTextAttribute()
    {
        $status = Arr::get(static::$statuses, $this->status, '');
        
        return $status;
    }


    public function getFormattedDateAttribute()
    {
        return Carbon::createFromDate($this->date)->format('d.m.Y');
    }
}

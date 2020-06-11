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
    const STATUS_CART = 6;

    const PRIORITY_NORMAL = 1;
    const PRIORITY_HIGH = 2;

    protected $fillable = [
        'date',
        'date_to',
        'main_category',
        'number',
        'delivery',
        'delivery_distance',
        'delivery_price',
    	'client_id',
        'status',
        'comment',
        'priority',
        'cost',
        'paid',
        'pay_type',
        'weight',
        'pallets',
        'pallets_price'
    ];

    protected $appends = [
    	'url',
        'status_text',
        'delivery_text',
        'priority_text',
        'pay_type_text',
        'formatted_date',
        'formatted_date_to',
        'payments_paid'
    ];

    protected $with = [
        'client',
        'products'
    ];

    protected $casts = [
        'cost' => 'float',
        'paid' => 'float',
        'pallets_price' => 'float',
        'weight' => 'float',
        'delivery_distance' => 'float',
        'delivery_price' => 'float'
    ];

    protected static $statuses = [
        0 => 'Нет статуса',
        1 => 'В работе',
        2 => 'Готов к выдаче',
        3 => 'Новый',
        4 => 'Приостановлен',
        5 => 'Завершен'
    ];

    protected static $priorities = [
        1 => 'Обычный',
        2 => 'Высокий'
    ];

    protected static $payTypes = [
        'cash' => 'Наличный',
        'cashless' => 'Безнал',
        'vat' => 'НДС'
    ];

    protected static $deliveries = [
        'sverdlovsk' => 'Свердловский район',
        'other' => 'Другой район'
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

    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
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

    public function getPriorityTextAttribute()
    {
        $priority = Arr::get(static::$priorities, $this->priority, '');
        
        return $priority;
    }

    public function getPayTypeTextAttribute()
    {
        $payType = Arr::get(static::$payTypes, $this->pay_type, '');
        
        return $payType;
    }

    public function getFormattedDateAttribute()
    {
        return Carbon::createFromDate($this->date)->format('d.m.Y');
    }

    public function getFormattedDateToAttribute()
    {
        return Carbon::createFromDate($this->date_to)->format('d.m.Y');
    }

    public function getFullFormattedDateAttribute()
    {
        return Carbon::createFromDate($this->date_to)->format('d F Y \г.');
    }

    public function getDeliveryTextAttribute()
    {
        if (!$this->delivery) 
        {
            return 'Самовывоз';
        }
        $delivery = Arr::get(static::$deliveries, $this->delivery, '');

        return $delivery . ' (' . $this->delivery_distance . ' км за городом)';
    }

    public function getPaymentsPaidAttribute()
    {
        return $this->payments->sum('paid');
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

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Stock extends Model
{
    protected $fillable = [
    	'date',
    	'model_id',
    	'model_type',
        'process_id',
        'process_type',
    	'in_stock',
        'new_in_stock',
        'reason'
    ];

    protected $casts = [
        'in_stock' => 'float',
        'new_in_stock' => 'float'
    ];

    protected $with = [
        'model',
        'process'
    ];

    protected $appends = [
        'formatted_date',
        'reason_text',
        'change'
    ];


    public function model()
    {
        return $this->morphTo();
    }

    public function process()
    {
        return $this->morphTo();
    }


    public function getFormattedDateAttribute()
    {
        return Carbon::createFromDate($this->date)->format('d.m.Y');
    }

    public function getChangeAttribute()
    {
        return round($this->new_in_stock - $this->in_stock, 3);
    }

    public function getReasonTextAttribute()
    {
        switch ($this->reason) 
        {
            case 'production':
                return 'Производство, ' . $this->process->formatted_date;
                break;

            case 'realization':
                return 'Выдача заказа №' . ($this->process->order->number ? $this->process->order->number : $this->process->order->id) . ', ' . $this->process->formatted_date;
                break;

            case 'material_apply':
                return 'Расход, ' . $this->process->formatted_date;
                break;

            case 'material_supply':
                return 'Поступление, ' . $this->process->formatted_date;
                break;

            case 'manual':
                return 'Корректировка';
                break;

            case 'month_start':
                return 'Начало месяца';
                break;

            case 'create':
                return 'Создание';
                break;
            
            default:
                return 'Не указано';
                break;
        }
    }
}

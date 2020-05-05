<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Arr;


class Worker extends Model
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;


    protected $fillable = [
        'facility_id',
        'surname',
        'full_name',
        'patronymic',
        'phone',
        'name',
        'status',
        'status_date',
        'status_date_next'
    ];

    protected $appends = [
        'url',
        'status_text'
    ];

    protected static $statuses = [
        0 => 'Не работает',
        1 => 'Работает'
    ];


    public function employments()
    {
        return $this->hasMany(Employment::class);
    }

    public function salaries()
    {
    	return $this->hasMany(WorkerSalary::class);
    }

    public function vacations()
    {
        return $this->hasMany(WorkerVacation::class);
    }


    public function getStatusTextAttribute()
    {
        $status = Arr::get(static::$statuses, $this->status, '');
        if ($this->status_date)
        {
            $status .= ' до ' . Carbon::createFromDate($this->status_date)->format('d.m.Y');

            // if ($this->status_date_next)
            // {
            //     $status .= ' (отпуск до ' . Carbon::createFromDate($this->status_date_next)->format('d.m.Y') . ')';
            // }
        }
        return $status;
    }
    

    public function getUrlAttribute()
    {
        return route('worker-show', ['worker' => $this->id]);
    }
}

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
        'birthdate',
        'passport',
        'status',
        'status_date',
        'status_date_next'
    ];

    protected $appends = [
        'url',
        'status_text',
        'surname_name_patronymic',
        'formatted_phone'
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


    public function getFormattedPhoneAttribute()
    {
        return preg_replace('/(\d{3})(\d{3})(\d{2})(\d{2})/', '+7 ($1) $2-$3-$4', $this->phone);
    }


    public function getSurnameNamePatronymicAttribute()
    {
        $name = $this->surname . ' ' . $this->full_name . ' ' . $this->patronymic;
        return ($name != '  ') ? $name : '';
    }


    public function getFormattedBirthdateAttribute()
    {
        if ($this->birthdate)
        {
            return Carbon::createFromDate($this->birthdate)->format('d.m.Y');
        }
        else
        {
            return '';
        }
    }
}

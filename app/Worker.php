<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Arr;


class Worker extends Model
{
    protected $fillable = [
        'surname',
        'full_name',
        'patronymic',
    	'name',
        'status',
        'facility_id'
    ];

    protected $appends = [
        'url',
        'status_text'
    ];

    protected static $statuses = [
        'active' => 'Работает',
        'paused' => 'В отпуске'
    ];


    public function employments()
    {
        return $this->hasMany(Employment::class);
    }

    public function salaries()
    {
    	return $this->hasMany(WorkerSalary::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }


    public function getStatusTextAttribute()
    {
        return Arr::get(static::$statuses, $this->status, '');
    }

    public function getUrlAttribute()
    {
        return route('worker-show', ['worker' => $this->id]);
    }
}

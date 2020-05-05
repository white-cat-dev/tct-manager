<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Employment extends Model
{
    protected $fillable = [
    	'date',
        'worker_id',
        'status_id',
        'facility_id'
    ];

    protected $appends = [
        'day'
    ];


    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function status()
    {
        return $this->belongsTo(EmploymentStatus::class);
    }


    public function getDayAttribute()
    {
        return Carbon::createFromDate($this->date)->day;
    }
}

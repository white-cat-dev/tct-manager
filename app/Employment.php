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
        'status_custom',
        'main_category',
        'salary'
    ];

    protected $appends = [
        'day'
    ];

    protected $casts = [
        'salary' => 'float',
        'status_custom' => 'float'
    ];


    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function status()
    {
        return $this->belongsTo(EmploymentStatus::class)->withTrashed();
    }


    public function getDayAttribute()
    {
        return Carbon::createFromDate($this->date)->day;
    }
}

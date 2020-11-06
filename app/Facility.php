<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Arr;


class Facility extends Model
{
    use SoftDeletes;

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;


    protected $fillable = [
        'name',
        'performance',
        'status',
        'status_date',
        'icon_color'
    ];

    protected $appends = [
        'url',
        'status_text',
        'current_performance',
        'categories_list'
    ];

    protected $with = [
        'categories'
    ];

    protected $casts = [
        'performance' => 'float'
    ];

    protected static $statuses = [
        0 => 'Не работает',
        1 => 'Работает',
    ];


    public function workers() 
    {
    	return $this->hasMany(Worker::class);
    }

    public function categories() 
    {
    	return $this->belongsToMany(Category::class, 'facilities_categories');
    }


    public function getUrlAttribute()
    {
        return route('facility-show', ['facility' => $this->id]);
    }


    public function getStatusTextAttribute()
    {
        $status = Arr::get(static::$statuses, $this->status, '');
        if (!empty($this->status_date))
        {
            $status .= ' до ' . Carbon::createFromDate($this->status_date)->format('d.m.Y');
        }
        return $status;
    }


    public function getCurrentPerformanceAttribute()
    {
        return $this->getPerformance(date('Y-m-d'));
    }


    public function getPerformance($date)
    {
        switch ($this->status) 
        {
            case static::STATUS_ACTIVE:
                if ($this->status_date && $date >= $this->status_date)
                {
                    if ($this->status_date_next && $date < $this->status_date)
                    {
                        return 0;
                    }
                    else 
                    {
                        return $this->performance;
                    }
                }
                else
                {
                    return $this->performance;
                }
                break;

            case static::STATUS_INACTIVE:
                if ($this->status_date && $date >= $this->status_date)
                {
                    if ($this->status_date_next && $date < $this->status_date)
                    {
                        return $this->performance;
                    }
                    else 
                    {
                        return 0;
                    }
                }
                else
                {
                    return 0;
                }
                break;

            default:
                return 0;
                break;
        }
    }


    public function getCategoriesListAttribute()
    {
        return $this->categories->pluck('id')->values();
    }
}

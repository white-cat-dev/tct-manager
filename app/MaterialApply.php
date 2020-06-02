<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class MaterialApply extends Model
{
    protected $fillable = [
        'date',
        'material_id',
        'performed',
        'planned'
    ];

    protected $casts = [
        'planned' => 'float',
        'performed' => 'float'
    ];

    protected $appends = [
    	'day'
    ];


    public function getDayAttribute()
    {
        if ($this->date)
        {
            return Carbon::createFromDate($this->date)->day;
        }
        else
        {
            return 0;
        }
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}

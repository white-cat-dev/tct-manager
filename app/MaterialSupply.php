<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class MaterialSupply extends Model
{
    protected $fillable = [
        'date',
        'material_id',
        'performed'
    ];

    protected $appends = [
        'formatted_date'
    ];

    protected $casts = [
        'performed' => 'float'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function getFormattedDateAttribute()
    {
        return Carbon::createFromDate($this->date)->format('d.m.Y');
    }
}

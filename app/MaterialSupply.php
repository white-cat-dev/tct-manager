<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class MaterialSupply extends Model
{
    protected $fillable = [
        'date',
        'material_id',
        'performed'
    ];


    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}

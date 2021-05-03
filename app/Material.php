<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Arr;


class Material extends Model
{
    use SoftDeletes;

    protected $fillable = [
    	'material_group_id',
        'variation',
        'price',
        'in_stock'
    ];

    protected $casts = [
        'price' => 'float',
        'in_stock' => 'float'
    ];


    protected $appends = [
        'variation_text',
        'units_text'
    ];

    protected $with = [
        'material_group'
    ];


    public function material_group()
    {
        return $this->belongsTo(MaterialGroup::class);
    }

    public function supplies()
    {
        return $this->hasMany(MaterialSupply::class);
    }

    public function applies()
    {
        return $this->hasMany(MaterialApply::class);
    }

    public function stocks()
    {
        return $this->morphMany(Stock::class, 'model');
    }


    public function getVariationTextAttribute()
    {
        if ($this->material_group->variations == 'colors')
        {
            return Arr::get(static::$allVariations['colors'], $this->variation, '');
        }
        else
        {
        	return '';
        }
    }


    public function getUnitsTextAttribute()
    {
        switch ($this->material_group->units) 
        {
            case 'volume_l':
                return 'л';
                break;

            case 'volume_ml':
                return 'мл';
                break;

            case 'weight_kg':
                return 'кг';
                break;

            case 'weight_t':
                return 'т';
                break;
            
            default:
                return '';
                break;
        }
    }


    public function updateInStock($newInStock, $reason, $process = null)
    {
        if ($this->in_stock == $newInStock)
        {
            return;
        }

        $processTypes = [
            'material_supply' => 'App\MaterialSupply',
            'material_apply' => 'App\MaterialApply',
            'manual' => ''
        ];

        $materialStock = $this->stocks()->create([
            'date' => date('Y-m-d'),
            'process_date' => !empty($process) ? $process->date : date('Y-m-d'),
            'process_id' => !empty($process) ? $process->id : 0,
            'process_type' => !empty($processTypes[$reason]) ? $processTypes[$reason] : '',
            'in_stock' => $this->in_stock,
            'new_in_stock' => $newInStock,
            'reason' => $reason
        ]);

        $this->update([
            'in_stock' => $newInStock
        ]);
    }


    protected static $allVariations = [
        'colors' => [
            'grey' => 'серый',
            'red' => 'красный',
            'yellow' => 'желтый',
            'brown' => 'коричневый',
            'black' => 'черный',
            'white' => 'белый',
            'orange' => 'оранжевый',
            'light-brown' => 'светло-коричневый'
        ]
    ];
}

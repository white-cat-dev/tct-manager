<?php

namespace App\Exports;

use App\Product;
use App\Material;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class MaterialsExport implements FromCollection, ShouldAutoSize
{

    public function collection()
    {
        $collection = collect([
            ['Название', 'Цена', 'В наличии']
        ]);

        $materials = Material::all()->sortBy('material_group.name');

        foreach ($materials as $material) 
        {
            $collection->push([
                $material->material_group->name . ' ' . $material->variation_text,
                $material->price . ' руб',
                $material->in_stock > 0 ? $material->in_stock : '0'
            ]);
        }

        return $collection;
    }
}
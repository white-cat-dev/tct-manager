<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Production;


class ProductionsExport implements FromCollection, ShouldAutoSize
{

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
    }
    

    public function collection()
    {
        $productions = Production::whereNotNull('date')
            ->whereYear('date', $this->year)
            ->whereMonth('date', $this->month)
            ->where('product_id', '>', 0)
            ->get();

        $productsProductions = [];

        foreach ($productions as $production) 
        {
            $product = $production->product;

            if (empty($product))
            {
                continue;
            }

            if (empty($productsProductions[$production->product_id]))
            {

                $productsProductions[$production->product_id] = [
                    'name' => $product->product_group->name .  ' ' . $product->product_group->size . ' ' . $product->variation_text,
                    'performed' => $production->performed
                ];
            }
            else
            {
                $productsProductions[$production->product_id]['performed'] += $production->performed;
            }
        }

        $collection = collect([
            ['Наименование', 'Выпущено']
        ]);

        foreach ($productsProductions as $productProduction) 
        {
            if ($productProduction['performed'] > 0)
            {
                $collection->push([
                    $productProduction['name'],
                    $productProduction['performed']            
                ]);
            }
        }

        return $collection;
    }
}
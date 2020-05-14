<?php

namespace App\Exports;

use App\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;
// use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class ProductsExport implements FromCollection, ShouldAutoSize
{
    protected $category = 0;
    protected $onlyInStock = false;


    public function __construct($category, $onlyInStock)
    {
        $this->category = $category;
        $this->onlyInStock = $onlyInStock == 'true';
    }
    

    public function collection()
    {
        $query = Product::with('product_group');
        if ($this->category)
        {
            $query = $query->where('category_id', $this->category);
        }

        if ($this->onlyInStock)
        {
            $query = $query->where('in_stock', '>', 0);
        }

        $products = $query->get()->sortBy('product_group.name');


        $collection = collect([
            [
                'Название', 'В наличии'
            ]
        ]);

        foreach ($products as $num => $product) 
        {
            $collection->push([
                $product->product_group->name . ' ' . $product->variation_text,
                $product->in_stock > 0 ? $product->in_stock : '0'
            ]);
        }

        return $collection;
    }


    // public static function afterSheet(AfterSheet $event)
    // {
    //     $colums = ['A', 'B', 'C'];

    //     foreach ($colums as $column) 
    //     {
    //         $event->sheet->getDelegate()->getColumnDimension($column)->setWidth(100);
    //     }
    // }
}
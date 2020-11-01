<?php

namespace App\Exports;

use App\Product;
use App\Material;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class ProductsExport implements FromCollection, ShouldAutoSize
{
    protected $category = 0;
    protected $onlyInStock = false;
    protected $withMaterials = false;


    public function __construct($category, $onlyInStock, $withMaterials)
    {
        $this->category = $category;
        $this->onlyInStock = $onlyInStock == 'true';
        $this->withMaterials = $withMaterials == 'true';
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
            ['Название', 'Цена', 'В наличии']
        ]);

        foreach ($products as $product) 
        {
            $collection->push([
                $product->product_group->name .  ' ' . $product->product_group->size . ' ' . $product->variation_text,
                $product->price . ' руб',
                $product->in_stock > 0 ? $product->in_stock : '0'
            ]);
        }


        if ($this->withMaterials)
        {
            $collection->push(['', '']);

            $materials = Material::orderBy('name')->get();

            foreach ($materials as $material) 
            {
                $collection->push([
                    $material->name,
                    $material->price . ' руб',
                    $material->in_stock > 0 ? $material->in_stock : '0'
                ]);
            }

        }

        return $collection;
    }
}
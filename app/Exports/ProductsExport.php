<?php

namespace App\Exports;

use App\Product;
use Maatwebsite\Excel\Concerns\FromCollection;


class ProductsExport implements FromCollection
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


        $collection = collect([]);

        foreach ($products as $product) 
        {
            $collection->push([
                $product->product_group->name . ' ' . $product->variation_text,
                $product->in_stock > 0 ? $product->in_stock : '0'
            ]);
        }

        return $collection;
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MaterialGroup;
use App\Category;


class ProductsVariation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:variation {variation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new variation for products';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $variation = $this->argument('variation');
        
        $materialGroups = MaterialGroup::where('variations', 'colors')
            ->get();

        foreach ($materialGroups as $materialGroup) 
        {
            $newMaterial = $materialGroup->materials()->where('variation', $variation)->first();
            if (!$newMaterial)
            {
                $material = $materialGroup->materials()->first();
                $newMaterial = $materialGroup->materials()->create([
                    'variation' => $variation,
                    'price' => $material->price,
                    'in_stock' => 0
                ]);
            }
        }

        $categories = Category::where('variations',  'colors')
            ->with('product_groups')
            ->get();

        foreach ($categories as $category) 
        {
            foreach ($category->product_groups as $productGroup) 
            {
                $newProduct = $productGroup->products()->where('variation', $variation)->first();
                if (!$newProduct)
                {

                    $product = $productGroup->products()->where('main_variation', 'color')->first();
                    $newProduct = $productGroup->products()->create([
                        'category_id' => $category->id,
                        'variation' => $variation,
                        'main_variation' => 'color',
                        'price' => $product->price,
                        'price_vat' => $product->price_vat,
                        'price_cashless' => $product->price_cashless,
                        'price_unit' => $product->price_unit,
                        'price_unit_vat' => $product->price_unit_vat,
                        'price_unit_cashless' => $product->price_unit_cashless,
                        'in_stock' => 0
                    ]);
                }
            }
        }
    }
}

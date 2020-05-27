<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Category;
use App\ProductGroup;
use Str;


class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products {file?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from json file';

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
     * @return mixed
     */
    public function handle()
    {
        $file = $this->argument('file');

        if (empty($file))
        {
            $file = resource_path('/data/products.json');
        }
        $categoriesData = json_decode(file_get_contents($file), true);

        foreach ($categoriesData as $categoryData) 
        {
            $category = $this->getCategory($categoryData);

            $prevProductGroup = null;
            $nextSetPair = false;

            foreach ($categoryData['product_groups'] as $productGroupData) 
            {
                $productGroup = $this->getProductGroup($productGroupData, $category, $prevProductGroup);

                if ($nextSetPair)
                {
                    $prevProductGroup->update([
                        'set_pair_id' => $productGroup->id
                    ]);
                }

                $nextSetPair = !empty($productGroupData['set_pair']) && $productGroupData['set_pair'] == 'next';

                foreach ($productGroupData['products'] as $productData) 
                {
                    $product = $this->getProduct($productData, $productGroup);
                }

                $prevProductGroup = $productGroup;

                $this->info($productGroup->name . ' ' . $productGroup->size);
            }
        }
    }


    protected function getCategory($categoryData)
    {
        $category = Category::where('name', $categoryData['name'])
            ->first();

        if (!$category)
        {
            $category = Category::create($categoryData);
        }
        else
        {
            $category->update($categoryData);
        }

        return $category;
    }


    protected function getProductGroup($productGroupData, $category, $prevProductGroup)
    {
        $productGroupData['wp_slug'] = Str::slug($productGroupData['wp_name']);
        $productGroupData['recipe_id'] = null;

        if (empty($productGroupData['adjectives']))
        {
            $productGroupData['adjectives'] = $category->adjectives;
        }

        if (empty($productGroupData['forms_add']))
        {
            $productGroupData['forms_add'] = 0;
        }


        if (!empty($productGroupData['set_pair'])) 
        {
            if ($productGroupData['set_pair'] == 'prev')
            {
                $productGroupData['set_pair_id'] = $prevProductGroup->id;
            }
            else
            {
                $productGroupData['set_pair_id'] = null;
            }
        }
        else
        {
            $productGroupData['set_pair_id'] = null;
            $productGroupData['set_pair_ratio'] = 0;
            $productGroupData['set_pair_ratio_to'] = 0;
        }

        $productGroup = $category->product_groups()
            ->where('name', $productGroupData['name'])
            ->where('length', $productGroupData['length'])
            ->where('width', $productGroupData['width'])
            ->where('height', $productGroupData['height'])
            ->first();

        if (!$productGroup)
        {
            $productGroup =  $category->product_groups()->create($productGroupData);
        }
        else
        {
            $productGroup->update($productGroupData);
        }

        return $productGroup;
    }


    protected function getProduct($productData, $productGroup)
    {
        $productData['category_id'] = $productGroup->category_id;
        $productData['in_stock'] = 0;

        if ($productGroup->category->units == 'unit')
        {
            $productData['price_unit'] = $productData['price'];
            $productData['price_unit_vat'] = $productData['price_vat'];
            $productData['price_unit_cashless'] = $productData['price_cashless'];
        }
        else
        {
            $productData['price_unit'] = ceil($productData['price'] / $productGroup->unit_in_units);
            $productData['price_unit_vat'] = ceil($productData['price_vat'] / $productGroup->unit_in_units);
            $productData['price_unit_cashless'] = ceil($productData['price_cashless'] / $productGroup->unit_in_units);
        }

        $product = $productGroup->products()
            ->where('variation', $productData['variation'])
            ->first();

        if (!$product)
        {
            $product = $productGroup->products()->create($productData);
        }
        else
        {
            $product->update($productData);
        }

        return $product;
    }
}

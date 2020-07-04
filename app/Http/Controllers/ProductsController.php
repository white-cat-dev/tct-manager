<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductGroup;
use App\Product; 
use App\Order; 
use Str;


class ProductsController extends Controller
{
    public function index(Request $request) 
    {
        if ($request->wantsJson())
        {
            $category = $request->get('category', 0);
            $stock = $request->get('stock', false);

            $query = ProductGroup::with('products');

            if ($category)
            {
                $query = $query->where('category_id', $category);
            }

            if ($stock)
            {
                $query = $query->whereHas('products', function($productsQuery) {
                    $productsQuery->where('in_stock', '>', 0);
                })->with(['products' => function($productsQuery) {
                    $productsQuery->where('in_stock', '>', 0);
                }]);
            }

            $productGroups = $query->orderBy('name')->get();

            return $productGroups;
        }

        return view('index', ['ngTemplate' => 'products']);
    }


    public function show(Request $request, ProductGroup $productGroup) 
    {
        if ($request->wantsJson())
        {
            $productGroup->products = $productGroup->products;
            $productGroup->category = $productGroup->category;

            return $productGroup;
        }

        return view('index', ['ngTemplate' => 'products.show']);
    }


    public function create(Request $request) 
    {
        if ($request->wantsJson())
        {
            $this->validate($request, $this->validationRules);

            $productGroupData = $this->getData($request);
            $productGroup = ProductGroup::create($productGroupData);

            foreach ($request->get('products', []) as $productData) 
            {
                $productData = $this->getProductData($productData, $productGroup);

                $product = $productGroup->products()->create($productData);
            }

            $productGroup->products = $productGroup->products;
            $productGroup->category = $productGroup->category;

            return $productGroup;
        }

        return view('index', ['ngTemplate' => 'products.edit']);
    }


    public function edit(Request $request, ProductGroup $productGroup) 
    {
        if ($request->wantsJson())
        {
            $this->validate($request, $this->validationRules);

            $productGroupData = $this->getData($request);
            $productGroup->update($productGroupData);

            $productsIds = $productGroup->products()->select('id')->pluck('id', 'id');

            foreach ($request->get('products', []) as $productData) 
            {
                $id = !empty($productData['id']) ? $productData['id'] : 0;
                
                $productsIds->forget($id);

                $product = $productGroup->products()->find($id);

                $productData = $this->getProductData($productData, $productGroup);

                if (!$product) 
                {
                    $product = $productGroup->products()->create($productData);
                }
                else 
                {
                    if ($product->in_stock != $productData['in_stock'])
                    {
                        $productStock = $product->stocks()->where('date', date('Y-m-d'))->first();
                        if ($productStock)
                        {
                            $productStock->update([
                                'new_in_stock' => $productData['in_stock']
                            ]);
                        }
                    }
                    $product->update($productData);
                }
            }

            // $productGroup->products()->detach($productsIds);

            $productGroup->products = $productGroup->products;
            $productGroup->category = $productGroup->category;

            return $productGroup;
        }

        return view('index', ['ngTemplate' => 'products.edit']);
    }


    public function delete(Request $request, ProductGroup $productGroup)
    {
        if ($request->wantsJson())
        {
            $productGroup->delete();
        }
    }


    public function copy(Request $request, ProductGroup $productGroup)
    {
        $productGroupCopy = $productGroup->replicate()->fill([
            'name' => $productGroup->name . ' (копия)'
        ]);
        $productGroupCopy->save();

        foreach ($productGroup->products as $product) 
        {
            $productCopy = $product->replicate()->fill([
                'product_group_id' => $productGroupCopy->id
            ]);
            $productCopy->save();
        }

        return $productGroupCopy;
    }


    public function orders(Request $request, Product $product)
    {
        $orders = $product->orders()->where('status', '!=', Order::STATUS_FINISHED)->get();

        foreach ($orders as $order) 
        {
            $order->progress = $product->getProgress($order);
        }

        return $orders;
    }


    protected $validationRules = [
        'name' => 'required',
        'category_id' => 'required',
        'width' => 'required',
        'length' => 'required',
        'height' => 'required',
        'weight_unit' => 'required',
        'weight_pallete' => 'required',
        'unit_in_units' => 'required',
        'unit_in_pallete' => 'required',
        'units_in_pallete' => 'required',
        'units_from_batch' => 'required',
        'forms' => 'required',
        'adjectives' => 'required',
        'salary_units' => 'required'

    ];


    protected function getData(Request $request)
    {
        $wpName = $request->get('wp_name', '');
        $wpSlug = Str::slug($wpName);

        return [
            'wp_name' => $wpName,
            'wp_slug' => $wpSlug,
            'name' => $request->get('name', ''),
            'set_pair_id' => $request->get('set_pair_id', null),
            'set_pair_ratio' => $request->get('set_pair_ratio', 0),
            'set_pair_ratio_to' => $request->get('set_pair_ratio_to', 0),
            'category_id' => $request->get('category_id', 0),
            'size_params' => $request->get('size_params', 'lwh'),
            'width' => $request->get('width', 0),
            'length' => $request->get('length', 0),
            'height' => $request->get('height', 0),
            'adjectives' => $request->get('adjectives', 'feminine'),
            'weight_unit' => $request->get('weight_unit', 0),
            'weight_pallete' => $request->get('weight_pallete', 0),
            'unit_in_units' => $request->get('unit_in_units', 0),
            'unit_in_pallete' => $request->get('unit_in_pallete', 0),
            'units_in_pallete' => $request->get('units_in_pallete', 0),
            'units_from_batch' => $request->get('units_from_batch', 0),
            'forms' => $request->get('forms', 0),
            'performance' => $request->get('performance', 0),
            'salary_units' => $request->get('salary_units', 0),
            'recipe_id' => $request->get('recipe_id', null)
        ];
    }


    protected function getProductData($data, $productGroup)
    {
        $categoryId = $productGroup->category_id;

        if (!$productGroup->category->variations)
        {
            $data['variation'] = '';
            $data['main_variation'] = '';
        }
        
        return [
            'category_id' => $categoryId,
            'variation' => !empty($data['variation']) ? $data['variation'] : '',
            'main_variation' => !empty($data['main_variation']) ? $data['main_variation'] : '',
            'price' => !empty($data['price']) ? $data['price'] : 0,
            'price_vat' => !empty($data['price_vat']) ? $data['price_vat'] : 0,
            'price_cashless' => !empty($data['price_cashless']) ? $data['price_cashless'] : 0,
            'price_unit' => !empty($data['price_unit']) ? $data['price_unit'] : 0,
            'price_unit_vat' => !empty($data['price_unit_vat']) ? $data['price_unit_vat'] : 0,
            'price_unit_cashless' => !empty($data['price_unit_cashless']) ? $data['price_unit_cashless'] : 0,
            'in_stock' => !empty($data['in_stock']) ? $data['in_stock'] : 0
        ];
    }
}
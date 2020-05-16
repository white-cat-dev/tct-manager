<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductGroup;
use App\Product; 


class ProductsController extends Controller
{
    public function index(Request $request) 
    {
        if ($request->wantsJson())
        {
            $category = $request->get('category');
            $query = ProductGroup::with('products');
            if ($category)
            {
                $query = $query->where('category_id', $category);
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

            foreach ($request->get('products') as $productData) 
            {
                $productData['category_id'] = $productGroup->category_id;
                if (!$productGroup->category->variations)
                {
                    $productData['variation'] = '';
                    $productData['main_variation'] = '';
                }
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
                $productData['category_id'] = $productGroup->category_id;
                if (!$productGroup->category->variations)
                {
                    $productData['variation'] = '';
                    $productData['main_variation'] = '';
                }

                $id = !empty($productData['id']) ? $productData['id'] : 0;
                
                $productsIds->forget($id);

                $product = $productGroup->products()->find($id);

                if (!$product) 
                {
                    $product = $productGroup->products()->create($productData);
                }
                else 
                {
                    $product->update($productData);
                }
            }

            $productGroup->products()->whereIn('products.id', $productsIds)->delete();

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


    protected $validationRules = [
        'name' => 'required',
        'category_id' => 'required',
        'width' => 'required',
        'length' => 'required',
        'depth' => 'required',
        'weight_unit' => 'required',
        'weight_units' => 'required',
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
        return [
            'wp_id' => $request->get('wp_id', 0),
            'wp_name' => $request->get('wp_name', ''),
            'name' => $request->get('name', ''),
            'set_pair_id' => $request->get('set_pair_id', 0),
            'category_id' => $request->get('category_id', 0),
            'width' => $request->get('width', 0),
            'length' => $request->get('length', 0),
            'depth' => $request->get('depth', 0),
            'adjectives' => $request->get('noun', 'feminine'),
            'weight_unit' => $request->get('weight_unit', 0),
            'weight_units' => $request->get('weight_units', 0),
            'weight_pallete' => $request->get('weight_pallete', 0),
            'unit_in_units' => $request->get('unit_in_units', 0),
            'unit_in_pallete' => $request->get('unit_in_pallete', 0),
            'units_in_pallete' => $request->get('units_in_pallete', 0),
            'units_from_batch' => $request->get('units_from_batch', 0),
            'forms' => $request->get('forms', 0),
            'salary_units' => $request->get('salary_units', 0),
            'recipe_id' => $request->get('recipe_id', 0),
        ];
    }
}
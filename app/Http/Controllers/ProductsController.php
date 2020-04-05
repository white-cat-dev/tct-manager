<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductGroup;


class ProductsController extends Controller
{
    protected $validationRules = [
        'name' => 'required'
    ];

    public function index(Request $request) 
    {
        if ($request->wantsJson())
        {
            $productGroups = ProductGroup::all();
            return $productGroups;
        }

        return view('index', ['ngTemplate' => 'products']);
    }


    public function show(Request $request, ProductGroup $productGroup) 
    {
        if ($request->wantsJson())
        {
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
                $product = $productGroup->products()->create($productData);
            }
            
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

            $productsIds = $productGroup->products()->select('id')->pluck('id');

            foreach ($request->get('products') as $productData) 
            {
                $id = !empty($productData['id']) ? $productData['id'] : 0;
                
                if ($productsIds->has($id)) 
                {
                    $productsIds->forget($id);
                }

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


    protected function getData(Request $request)
    {
        return [
            'name' => $request->get('name'),
            'set_pair_id' => $request->get('set_pair_id') ? : 0,
            'category_id' => $request->get('category_id') ? : 0,
            'width' => $request->get('width') ? : 0,
            'length' => $request->get('length') ? : 0,
            'depth' => $request->get('depth') ? : 0,
            'weight_unit' => $request->get('weight_unit') ? : 0,
            'weight_square' => $request->get('weight_square') ? : 0,
            'weight_pallete' => $request->get('weight_pallete') ? : 0,
            'units_in_square' => $request->get('units_in_square') ? : 0,
            'units_in_pallete' => $request->get('units_in_pallete') ? : 0,
            'squares_in_pallete' => $request->get('squares_in_pallete') ? : 0,
            'squares_from_batch' => $request->get('squares_from_batch') ? : 0,
            'forms' => $request->get('forms') ? : 0
        ];
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProductGroup;
use App\Product;
use Str;


class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $productGroups = ProductGroup::with('products')->get();
        $productGroupsData = [];

        foreach ($productGroups as $productGroup) 
        {
            $productGroupData = [
                'uuid' => $productGroup['uuid']
            ];

            $paramsData = [];

            switch ($productGroup->size_params) 
            {
                case 'lwh':
                    $paramsData['length'] = [
                        'value' => $productGroup->length . ' мм'
                    ];
                    $paramsData['width'] = [
                        'value' => $productGroup->width . ' мм'
                    ];
                    $paramsData['depth'] = [
                        'value' => $productGroup->height . ' мм'
                    ];
                    break;

                case 'lhw':
                    $paramsData['length'] = [
                        'value' =>$productGroup->length . ' мм'
                    ];
                    $paramsData['height'] = [
                        'value' =>$productGroup->width . ' мм'
                    ];
                    $paramsData['width'] = [
                        'value' =>$productGroup->height . ' мм'
                    ];
                    break;

                case 'lh':
                    $paramsData['diameter'] = [
                        'value' =>$productGroup->length . ' мм'
                    ];
                    $paramsData['depth'] = [
                        'value' =>$productGroup->width . ' мм'
                    ];
                    break;

                case 'whl':
                    $paramsData['width'] = [
                        'value' =>$productGroup->length . ' мм'
                    ];
                    $paramsData['height'] = [
                        'value' =>$productGroup->width . ' мм'
                    ];
                    $paramsData['length'] = [
                        'value' =>$productGroup->height . ' мм'
                    ];
                    break;
            }

            if (!empty($productGroup->weight_unit))
            {
                $paramsData['weight-unit'] = [
                    'value' => $productGroup->weight_unit . ' кг'
                ];
            }

            if (!empty($productGroup->weight_pallet))
            {
                $paramsData['weight-pallet'] = [
                    'value' => $productGroup->weight_pallet . ' кг'
                ];
            }

            if ($productGroup->category->units == 'volume')
            {
                $paramsData['volume-unit'] = [
                    'value' => $productGroup->length * $productGroup->width * $productGroup->height / 1000000000 . ' куб. м'
                ];

                $paramsData['volume-pallet'] = [
                    'value' => $productGroup->length * $productGroup->width * $productGroup->height / 1000000000 * $productGroup->unit_in_pallete . ' куб. м'
                ];
            }


            if ($productGroup->unit_in_units)
            {
                if ($productGroup->category->units == 'volume')
                {
                    $paramsData['area-units'] = [
                        'value' => $productGroup->unit_in_units . ' шт'
                    ];
                }
                else if ($productGroup->category->units == 'volume')
                {
                    $paramsData['volume-units'] = [
                        'value' => $productGroup->unit_in_units . ' шт',
                    ];
                }
            }

            if (($productGroup->unit_in_units) || ($productGroup->units_in_pallete))
            {   
                $value = $productGroup->unit_in_pallete ? $productGroup->unit_in_pallete . ' шт' : '';
                if ($productGroup->units_in_pallete)
                {
                    $units = ($productGroup->category->units == 'area') ? ' кв. м' : (($productGroup->category->units == 'volume') ? ' куб. м' : '');
                    $value = ($value ? $value . ', ' : '') . $productGroup->units_in_pallete . $units;
                }

                $paramsData['pallet-units'] = [
                    'value' => $value
                ];
            }


            $productsData = [];
            foreach ($productGroup->products as $product) 
            {
                $productsData[] = [
                    'key' => $product->variation,
                    'name' => $product->variation_text,
                    'price' => $product->price
                ];
            }

            $productGroupsData[] = [
                'uuid' => $productGroup->uuid,
                'name' => $productGroup->wp_name ? $productGroup->wp_name : $productGroup->name,
                'size' => $productGroup->size,
                'units' => $productGroup->units_text,
                'units_in_pallet' => $productGroup->units_in_pallete,
                'weight_units' => $productGroup->weight_unit * $productGroup->unit_in_units,
                'params' => $paramsData,
                'products' => $productsData
            ];
        }

        return $productGroupsData;
    }


    public function stocks(Request $request)
    {
        $stocks = [];

        $uuid = explode(',', $request->get('products', ''));
        $productGroups = ProductGroup::with('products')->whereIn('uuid', $uuid)->get();

        foreach ($productGroups as $productGroup) 
        {
            $stocks[$productGroup->uuid] = [];
            foreach ($productGroup->products as $product) 
            {
                $stocks[$productGroup->uuid][$product->variation] = $product->export_in_stock;
            }
        }

        return $stocks;
    }
}
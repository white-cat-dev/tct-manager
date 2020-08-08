<?php

namespace App\Http\Controllers\WpApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProductGroup;
use Str;


class ProductsController extends Controller
{
    public function getPost(Request $request) 
    {
        $wpSlug = Str::slug($request->get('title', ''));
        if ($wpSlug)
        {
            $productGroup = ProductGroup::where('wp_slug', $wpSlug)->first();
            if ($productGroup)
            {
                $productGroup->products = $productGroup->products->unique('main_variation');
            }
        }
        else
        {
            $productGroup = null;
        }

        
        $excerpt = ($productGroup) ? view('wp-api.excerpt', compact('productGroup', 'wpSlug'))->render() : '';
        $content = ($productGroup) ? view('wp-api.content', compact('productGroup', 'wpSlug'))->render() : '';

        $response = [
            'excerpt' => $excerpt,
            'content' => $content
        ];

        return $response;
    }


    public function getStock(Request $request) 
    {
        $wpSlug = Str::slug($request->get('title', ''));
        if ($wpSlug)
        {
            $productGroup = ProductGroup::where('wp_slug', $wpSlug)->first();
        }
        else
        {
            $productGroup = null;
        }

        $response = [
            'id' => 0
        ];

        if ($productGroup)
        {
            $response['id'] = $productGroup->id;
            $response['name'] = $productGroup->wp_name;
            $response['units'] = $productGroup->units_text;
            $response['set_pair'] = !empty($productGroup->set_pair);

            $products = [];

            foreach ($productGroup->products as $product) 
            {
                $newProduct = [
                    'id' => $product->id,
                    'variation' => $product->variation_text,
                    'stock' => $product->in_stock > 0,
                    'default' => $product->variation == 'grey',
                    'in_stock' => $product->in_stock_text
                ];

                if (!empty($productGroup->set_pair))
                {
                    $setPairProduct = $productGroup->set_pair->products->where('variation', $product->variation)->first();
                    $newProduct['in_stock_pair'] = $setPairProduct ? $setPairProduct->in_stock_text : 'под заказ';
                }
                else
                {
                    $newProduct['in_stock_pair'] = '';
                }

                $products[] = $newProduct;
            }

            $response['products'] = $products;
        }

        return $response;
    }
}
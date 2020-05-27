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

        $response = [];

        if ($productGroup)
        {
            $response['id'] = $productGroup->id;
            $response['name'] = $productGroup->wp_name;
            $response['units'] = $productGroup->units_text;
            $response['set_pair'] = null;

            $products = [];

            foreach ($productGroup->products as $product) 
            {
                
                $products[] = [
                    'id' => $product->id,
                    'variation' => $product->variation_text,
                    'stock' => $product->in_stock > 0,
                    'default' => $product->variation == 'grey'
                ];
            }

            $response['products'] = $products;
        }

        return $response;
    }
}
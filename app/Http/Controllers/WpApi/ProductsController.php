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
        $wpName = $request->get('title');
        if ($wpName)
        {
            $productGroup = ProductGroup::where('wp_name', $wpName)->first();
        }
        else
        {
            $productGroup = null;
        }

        $response = [];

        if ($productGroup)
        {
            foreach ($productGroup->products as $product) 
            {
                $response[] = [
                    'id' => $product->id,
                    'variation' => $product->variation_text,
                    'stock' => $product->in_stock > 0,
                    'default' => $product->variation == 'grey'
                ];
            }
        }

        return $response;
    }
}
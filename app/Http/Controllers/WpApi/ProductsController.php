<?php

namespace App\Http\Controllers\WpApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProductGroup;


class ProductsController extends Controller
{
    public function excerpt(Request $request) 
    {
        return '';
        $wpName = $request->get('title');
        if ($wpName)
        {
            $productGroup = ProductGroup::where('wp_name', $wpName)->first();
        }
        else
        {
            $productGroup = null;
        }

        if ($productGroup)
        {
            return view('wp-api.excerpt', compact('productGroup'));
        }
        else
        {
            return '';
        }
    }


    public function content(Request $request, ProductGroup $productGroup) 
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

        if ($productGroup)
        {
            return view('wp-api.content', compact('productGroup'));
        }
        else
        {
            return '';
        }
    }
}
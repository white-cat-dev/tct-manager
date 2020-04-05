<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class TemplatesController extends Controller
{
    public function categories(Request $request) 
    {
        return view('categories');
    }

    public function categoriesShow(Request $request) 
    {
        return view('categories.show');
    }

    public function categoriesEdit(Request $request) 
    {
        return view('categories.edit');
    }


    public function products(Request $request) 
    {
        return view('products');
    }

    public function productsShow(Request $request) 
    {
        return view('products.show');
    }

    public function productsEdit(Request $request) 
    {
        return view('products.edit');
    }


    public function clients(Request $request) 
    {
        return view('clients');
    }

    public function clientsShow(Request $request) 
    {
        return view('clients.show');
    }

    public function clientsEdit(Request $request) 
    {
        return view('clients.edit');
    }


    public function orders(Request $request) 
    {
        return view('orders');
    }

    public function ordersShow(Request $request) 
    {
        return view('orders.show');
    }

    public function ordersEdit(Request $request) 
    {
        return view('orders.edit');
    }

    public function production(Request $request) 
    {
        return view('production');
    }
}
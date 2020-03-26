<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;


class OrdersController extends Controller
{
    public function index(Request $request) 
    {
        $orders = Order::all();
        
        return view('orders');
    }
}
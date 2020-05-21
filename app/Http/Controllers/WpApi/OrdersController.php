<?php

namespace App\Http\Controllers\WpApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Order;
use Cookie;


class OrdersController extends Controller
{
    public function getOrder(Request $request) 
    {
        $cart = $this->getUserCart();

        return $cart;
    }


    public function addToOrder(Request $request)
    {
        $cart = $this->getUserCart();

        $productId = $request->get('id', 0);
        if ($productId) 
        {
            $product = Product::findOrFail($productId);
        }

        $productCount = $request->get('count');
        if (!is_numeric($productCount))
        {
            return;
        }

        $cart->products()->attach([
            $product->id => [
                'price' => $product->price,
                'count' => $productCount,
                'cost' => $product->price * $productCount
            ]
        ]);
    }


    protected function getUserCart()
    {
        $cartId = (int)Cookie::get('cart');

        if (!empty($cartId))
        {
            $cart = Order::find($cartId);
            if (!$cart || ($cart->status != Order::STATUS_CART))
            {
                Cookie::forget('cart');
                $cart = null;
            }
        }
        
        if (empty($cart)) 
        {
            $cart = Order::create([
                'status' => Order::STATUS_CART,
                'number' => '',
                'date' => date('Y-m-d'),
                'client_id' => 0,
                'comment' => 'Заказ сделан на сайте',
                'priority' => 0,
                'cost' => 0,
                'weight' => 0,
                'pallets' => 0
            ]);

            Cookie::queue('cart', $cart->id, 129600);
        }

        return $cart;
    }
}
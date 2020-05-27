<?php

namespace App\Http\Controllers\WpApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProductGroup;
use App\Product;
use App\Order;
use Cookie;


class OrdersController extends Controller
{
    public function getOrder(Request $request) 
    {
        $cartId = $request->get('tct_manager_cart', null);
        $cart = $this->getUserCart($cartId);

        foreach ($cart->products as $product) 
        {
            $product->otherProducts = $product->product_group->products;
        }

        return $cart;
    }


    public function addToOrder(Request $request)
    {
        $cartId = $request->get('tct_manager_cart', null);
        $cart = $this->getUserCart($cartId);

        $productCount = $request->get('count');
        if (!is_numeric($productCount))
        {
            return;
        }

        $productGroupId = $request->get('id', 0);
        if ($productGroupId) 
        {
            $productGroup = ProductGroup::findOrFail($productGroupId);

            if ($productGroup->set_pair)
            {
                $setPairProduct = $productGroup->set_pair->products->first();
                $setPairProductCount = round($productCount / ($productGroup->set_pair_ratio + $productGroup->set_pair_ratio_to) * $productGroup->set_pair_ratio_to, 2);

                $this->addProduct($cart, $setPairProduct, $setPairProductCount);  

                $productCount -= $setPairProductCount;
            }

            $product = $productGroup->products->first();
            $this->addProduct($cart, $product, $productCount);    
        }

        return [
            'tct_manager_cart' => $cart->id
        ];
    }


    protected function addProduct($cart, $product, $count)
    {
        $cartProduct = $cart->products->find($product->id);
        if ($cartProduct)
        {
            $cart->products()->updateExistingPivot($product->id, [
                'count' => $cartProduct->pivot->count + $count,
                'cost' => $cartProduct->pivot->cost + $product->price * $count
            ]);
        }
        else
        {
            $cart->products()->attach([
                $product->id => [
                    'price' => $product->price,
                    'count' => $count,
                    'cost' => $product->price * $count
                ]
            ]); 
        }
    }


    protected function getUserCart($cartId = null)
    {
        if (!$cartId)
        {
            $cartId = (int)Cookie::get('tct_manager_cart');
        }

        if (!empty($cartId))
        {
            $cart = Order::find($cartId);
            if (!$cart || ($cart->status != Order::STATUS_CART))
            {
                Cookie::forget('tct_manager_cart');
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

            Cookie::queue('tct_manager_cart', $cart->id, 129600);
        }

        $cart->products = $cart->products;

        return $cart;
    }
}
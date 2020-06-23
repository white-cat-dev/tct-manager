<?php

namespace App\Http\Controllers\WpApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProductGroup;
use App\Product;
use App\Order;
use App\Client;
use Cookie;
use Carbon\Carbon;
use App\Services\ProductionsService;


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


    public function getDate(Request $request)
    {
        $cart = $this->getUserCart($request->get('id'));

        $products = [];

        foreach ($cart->products as $product) 
        {
            $products[] = [
                'product' => $product,
                'count' => $product->pivot->count
            ];
        }

        $dateTo = ProductionsService::getInstance()->testPlanOrder($products, Order::PRIORITY_NORMAL);

        if ($dateTo > date('Y-m-d'))
        {
            $dateTo = Carbon::createFromDate($dateTo)->addDays(2);
        }
        else
        {
            $dateTo = Carbon::createFromDate($dateTo);
        }

        $dateText = $dateTo->diffInDays(Carbon::today());

        if ($dateText == 0)
        {
            $dateText = 'Все товары из заказа есть в наличии';
        }
        else
        {
            $dateText = 'Ваш заказ ориентировочно будет готов через <strong>' . $dateText . ' ' . trans_choice('день|дня|дней', $dateText) . '</strong> после оплаты';
        }
       
        return [
            'date_text' => $dateText
        ];
    }


    public function saveOrder(Request $request)
    {
        $cartId = $request->get('id');
        $cart = Order::find($cartId);

        $cartData = $this->getData($request);
        $cart->update($cartData);
        if ($cart)
        {
            $cart->update([
                'status' => Order::STATUS_NEW
            ]);
        }
    }


    public function updateOrder(Request $request)
    {
        $cartId = $request->get('id');
        $cart = Order::find($cartId);

        $cartData = $this->getData($request);

        $cart->update($cartData);

        $clientData = $request->get('client');
        $client = Client::find($clientData['id']);
        $client->update($this->getClientData($clientData));

        $productsIds = $cart->products()->select('product_id')->pluck('product_id', 'product_id');

        foreach ($request->get('products', []) as $productData) 
        {
            $productsIds->forget($productData['id']);

            $product = $cart->products()->find($productData['id']);

            if (!$product) 
            {
                $product = $cart->products()->attach($productData['id'], [
                    'count' => $productData['pivot']['count'],
                    'price' => $productData['pivot']['price'],
                    'cost' => $productData['pivot']['cost']
                ]);
            }
            else 
            {
                $cart->products()->updateExistingPivot($productData['id'], [
                    'count' => $productData['pivot']['count'],
                    'price' => $productData['pivot']['price'],
                    'cost' => $productData['pivot']['cost']
                ]);
            }
        }

        $cart->products()->detach($productsIds);

        $cart->refresh();
        
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
            $client = Client::create([
                'name' => '',
                'phone' => '',
                'email' => ''
            ]);

            $cart = Order::create([
                'status' => Order::STATUS_CART,
                'main_category' => 'tiles',
                'number' => '',
                'date' => date('Y-m-d'),
                'date_to' => date('Y-m-d'),
                'client_id' => $client->id,
                'comment' => '',
                'priority' => 0,
                'cost' => 0,
                'weight' => 0,
                'pallets' => 0,
                'pallets_price' => 150,
                'paid' => 0,
                'pay_type' => 'cash',
                'delivery_price' => 0,
                'delivery' => '',
                'delivery_distance' => 0
            ]);

            Cookie::queue('tct_manager_cart', $cart->id, 129600);
        }

        $cart->products = $cart->products;

        return $cart;
    }


    protected function getData(Request $request)
    {
        return [
            'date' => $request->get('date', date('Y-m-d')),
            'date_to' => $request->get('date', date('Y-m-d')), 
            'number' => '',
            'main_category' => $request->get('main_category', 'tiles'),
            'delivery' => $request->get('delivery', ''),
            'delivery_distance' => $request->get('delivery_distance', 0),
            'delivery_price' => $request->get('delivery_price', 0),
            'client_id' => $request->get('client_id', 0),
            'priority' => $request->get('priority', Order::PRIORITY_NORMAL),
            'comment' => $request->get('comment', ''),
            'status' => $request->get('status', Order::STATUS_PRODUCTION),
            'cost' => $request->get('cost', 0),
            'paid' => $request->get('paid', 0),
            'pay_type' => $request->get('pay_type', 'cash'),
            'weight' => $request->get('weight', 0),
            'pallets' => $request->get('pallets', 0),
            'pallets_price' => $request->get('pallets_price', 150)
        ];
    }

    protected function getClientData($data)
    {
        return [
            'name' => !empty($data['name']) ? $data['name'] : '',
            'phone' => !empty($data['phone']) ? $data['phone'] : '',
            'email' => !empty($data['email']) ? $data['email'] : ''
        ];
    }
}
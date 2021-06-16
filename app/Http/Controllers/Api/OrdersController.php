<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Client;
use App\Order;
use Str;


class OrdersController extends Controller
{
    public function order(Request $request)
    {
        $clientData = [
            'name' => $request->get('client_name', ''),
            'phone' => $request->get('client_phone', ''),
            'email' => $request->get('client_email', ''),
        ];

        $client = Client::create($clientData);

        $comment = 'Заказ №' . $request->get('id') . '. ' . $request->get('comment');

        $orderData = [
            'date' => $request->get('date', date('Y-m-d H:i:s')),
            'date_to' => $request->get('date', date('Y-m-d H:i:s')),
            'number' => '',
            'main_category' => 'tiles',
            'delivery' => $request->get('delivery', ''),
            'delivery_distance' => $request->get('delivery_distance', 0),
            'delivery_price' => $request->get('delivery_price', 0),
            'client_id' => $client->id,
            'priority' => Order::PRIORITY_NORMAL,
            'comment' => $comment,
            'status' => Order::STATUS_NEW,
            'cost' => $request->get('cost', 0),
            'weight' => $request->get('weight', 0),
            'paid' => 0,
            'pay_type' => $request->get('pay_type', 'cash'),
            'pallets' => $request->get('pallets', 0),
            'pallets_price' => $request->get('pallets_price', 200)
        ];

        $order = Order::create($orderData);

        foreach ($request->get('products', []) as $productData) 
        {
            $productGroup = ProductGroup::where('uuid', $productData['uuid'])->first();
            if ($productGroup) 
            {
                $product = $productGroup->products()->where('key', $productData['key'])->first();

                if ($product)
                {
                    $order->products()->attach([
                        $product->id => [
                            'price' => $productData['price'],
                            'count' => $productData['count'],
                            'cost' => $productData['price'] * $productData['count']
                        ]
                    ]);
                }
            }
        }
    }
}
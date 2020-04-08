<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Client;
use App\Realization;
use App\Production;
use Carbon\Carbon;


class OrdersController extends Controller
{
    protected $validationRules = [
        
    ];

    public function index(Request $request) 
    {
        if ($request->wantsJson())
        {
            $orders = Order::all();
            return $orders;
        }

        return view('index', ['ngTemplate' => 'orders']);
    }


    public function show(Request $request, Order $order) 
    {
        if ($request->wantsJson())
        {
            return $order;
        }

        return view('index', ['ngTemplate' => 'orders.show']);
    }


    public function create(Request $request) 
    {
        if ($request->wantsJson())
        {
            $this->validate($request, $this->validationRules);

            $orderData = $this->getData($request);
            
            $client = Client::create($request->get('client'));
            $orderData['client_id'] = $client->id;

            $order = Order::create($orderData);


            foreach ($request->get('products') as $productData) 
            {
            	$order->products()->attach([
                    $productData['id'] => [
                    	'price' => $productData['pivot']['price'],
                        'count' => $productData['pivot']['count'],
                        'cost' => $productData['pivot']['cost']
                    ]
                ]);
            }

            foreach ($order->products as $product)
            {
                $productCount = $product->pivot->count;
                if ($product->free_in_stock > 0)
                {
                    $realization = Realization::create([
                        'date' => date('Y-m-d'),
                        'product_id' => $product->id,
                        'order_id' => $order->id,
                        'planned' => ($productCount >= $product->free_in_stock) ? $product->free_in_stock : $productCount,
                        'performed' => 0
                    ]);

                    $productCount -= $realization->planned;
                }

                $currentDay = Carbon::today();

                $maxBatches = 2;

                while ($productCount > 0)
                {
                    $currentDay = $currentDay->addDay();
                    $currentProductions = Production::where('date', $currentDay->format('Y-m-d'))->get();
                    $currentBatches = 0;
                    foreach ($currentProductions as $currentProduction) 
                    {
                        $currentBatches += $currentProduction->batches;
                    }
                    if ($currentBatches >= $maxBatches)
                    {
                        continue;
                    }

                    $maxCount = $product->product_group->squares_from_batch * ($maxBatches - $currentBatches);
                    $plannedCount = ($productCount >= $maxCount) ? $maxCount : $productCount;

                    $production = Production::create([
                        'date' => $currentDay->format('Y-m-d'),
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'planned' => $plannedCount,
                        'performed' => 0,
                        'batches' => $plannedCount / $product->product_group->squares_from_batch
                    ]);

                    $productCount -= $plannedCount;
                }
            }
            
            return $order;
        }

        return view('index', ['ngTemplate' => 'orders.edit']);
    }


    public function edit(Request $request, Order $order) 
    {
        if ($request->wantsJson())
        {
            // $this->validate($request, $this->validationRules);

            // $productGroup->update($this->getData($request));

            // $productsIds = $productGroup->products()->select('id')->pluck('id');

            // foreach ($request->get('products') as $productData) 
            // {
            //     $id = !empty($productData['id']) ? $productData['id'] : 0;
                
            //     if ($productsIds->has($id)) 
            //     {
            //         $productsIds->forget($id);
            //     }

            //     $product = $productGroup->products()->find($id);

            //     if (!$product) 
            //     {
            //         $product = $productGroup->products()->create($productData);
            //     }
            //     else 
            //     {
            //         $product->update($productData);
            //     }
            // }

            return $order;
        }

        return view('index', ['ngTemplate' => 'orders.edit']);
    }


    public function delete(Request $request, Order $order)
    {
        if ($request->wantsJson())
        {
            $order->delete();
        }
    }


    protected function getData(Request $request)
    {
        return [
            'client_id' => $request->get('client_id') ? : 0,
            'priority' => $request->get('priority') ? : 0,
            'cost' => $request->get('cost') ? : 0
        ];
    }
}
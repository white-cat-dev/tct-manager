<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Client;
use App\Realization;
use App\Production;
use App\Facility;
use Carbon\Carbon;
use App\Services\ProductionsService;


class OrdersController extends Controller
{
    protected $validationRules = [
        
    ];

    public function index(Request $request) 
    {
        if ($request->wantsJson())
        {
            $query = Order::with('realizations', 'realizations.product');

            $statuses = $request->get('status', -1);
            if ($statuses != -1)
            {
                $statuses = explode(',', $statuses);
                $query = $query->whereIn('status', $statuses);
            }

            $mainCategories = $request->get('main_category', -1);
            if ($mainCategories != -1)
            {
                $mainCategories = explode(',', $mainCategories);
                $query = $query->whereIn('main_category', $mainCategories);
            }


            $orders = $query->orderBy('status')->orderBy('date', 'DESC')->orderBy('number', 'DESC')->get();

            foreach ($orders as $order) 
            {
                $order->progress = $order->getProgress();
                foreach ($order->products as $product) 
                {
                    $product->progress = $product->getProgress($order);
                }
            }
            return $orders;
        }

        return view('index', ['ngTemplate' => 'orders']);
    }


    public function show(Request $request, Order $order) 
    {
        if ($request->wantsJson())
        {
            $order->progress = $order->getProgress();
            foreach ($order->products as $product) 
            {
                $product->progress = $product->getProgress($order);
            }

            $order->productions = $order->productions()->with('product')->where('performed', '>', 0)->get();
            $order->realizations = $order->realizations()->with('product')->get();
            $order->payments = $order->payments()->get();

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
            
            $client = Client::create($this->getClientData($request->get('client')));
            $orderData['client_id'] = $client->id;

            $order = Order::create($orderData);

            foreach ($request->get('products', []) as $productData) 
            {
            	if (!$productData['id'])
                {
                    continue;
                }

                $order->products()->attach([
                    $productData['id'] => [
                    	'price' => $productData['pivot']['price'],
                        'count' => $productData['pivot']['count'],
                        'cost' => $productData['pivot']['cost']
                    ]
                ]);
            }

            ProductionsService::getInstance()->planOrder($order);

            if ($order->productions()->count() == 0)
            {
                $order->update([
                    'status' => Order::STATUS_READY
                ]);
            }
            
            return $order;
        }

        return view('index', ['ngTemplate' => 'orders.edit']);
    }


    public function edit(Request $request, Order $order) 
    {
        if ($request->wantsJson())
        {
            $this->validate($request, $this->validationRules);

            $orderData = $this->getData($request);
            $order->update($orderData);

            $clientData = $request->get('client');
            $client = Client::find($clientData['id']);
            $client->update($this->getClientData($clientData));

            $productsIds = $order->products()->select('product_id')->pluck('product_id', 'product_id');

            foreach ($request->get('products', []) as $productData) 
            {
                $productsIds->forget($productData['id']);

                $product = $order->products()->find($productData['id']);

                if (!$product) 
                {
                    $product = $order->products()->attach($productData['id'], [
                        'count' => $productData['pivot']['count'],
                        'price' => $productData['pivot']['price'],
                        'cost' => $productData['pivot']['cost']
                    ]);
                }
                else 
                {
                    $order->products()->updateExistingPivot($productData['id'], [
                        'count' => $productData['pivot']['count'],
                        'price' => $productData['pivot']['price'],
                        'cost' => $productData['pivot']['cost']
                    ]);
                }
            }

            $order->products()->whereIn('orders_products.product_id', $productsIds)->delete();

            ProductionsService::getInstance()->planOrder($order);

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


    public function saveRealization(Request $request)
    {
        $realizationsData = $request->get('realizations');

        foreach ($realizationsData as $realizationData) 
        {
            $realization = Realization::where('id', $realizationData['id'])->with('order', 'product')->first();

            if ($realization)
            {
                if ($realization->date)
                {
                    continue;
                }

                $order = $realization->order;

                $performed = (float)$realizationData['performed'];
                $planned = $realization->planned - $performed;

                $realization->product->update([
                    'in_stock' => $realization->product->in_stock - $performed
                ]);

                $realization->update([
                    'date' => date('Y-m-d'),
                    'planned' => $performed,
                    'performed' => $performed
                ]);


                if ($planned == 0)
                {
                    $baseProductions = $realization->order->productions()->whereNull('date')->get();
                    if ($baseProductions->count() == 0) 
                    {
                        $order->update([
                            'status' => Order::STATUS_FINISHED
                        ]);
                        continue;
                    }
                }

                $baseRealization = Realization::create([
                    'date' => null,
                    'category_id' => $realization->product->id,
                    'product_id' => $realization->product_id,
                    'order_id' => $realization->order_id,
                    'planned' => $planned,
                    'performed' => 0
                ]);
            }
        }
    }


    protected function getData(Request $request)
    {
        $date = $request->get('date_raw', -1); 
        if ($date == -1)
        {
            $date = $request->get('date', date('Y-m-d')); 
        }
        else
        {
            $date = $date ? Carbon::createFromFormat('dmY', $date)->format('Y-m-d') : date('Y-m-d');
        }

        $number = $request->get('number', '');

        if (!$number)
        {
            $number = Order::whereYear('date', date('Y'))
                ->whereMonth('date', date('m'))
                ->count() + 1;

            $number = Carbon::createFromDate($date)->format('m') . '-' .  str_pad($number, 3, '0', STR_PAD_LEFT);
        }


        return [
            'date' => $date,
            'date_to' => $date,
            'number' => $number,
            'main_category' => $request->get('main_category', 'tiles'),
            'delivery' => $request->get('delivery', ''),
            'delivery_distance' => $request->get('delivery_distance', 0),
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
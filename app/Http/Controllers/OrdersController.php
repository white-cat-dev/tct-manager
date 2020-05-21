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

            $statuses = $request->get('status', 0);
            if ($statuses != 0)
            {
                $statuses = explode(',', $statuses);
                $query = $query->whereIn('status', $statuses);
            }

            $orders = $query->orderBy('status')->orderBy('priority')->orderBy('date')->get();

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
        $date = $request->get('date', -1);

        if ($date == -1)
        {
            $date = $request->get('date', date('Y-m-d')); 
        }
        else
        {
            $date = $date ? substr($date, 0, 10) : date('Y-m-d');
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
            'number' => $number,
            'client_id' => $request->get('client_id', 0),
            'priority' => $request->get('priority', Order::PRIORITY_NORMAL),
            'comment' => $request->get('comment', 0),
            'status' => $request->get('status', Order::STATUS_PRODUCTION),
            'cost' => $request->get('cost', 0),
            'weight' => $request->get('weight', 0),
            'pallets' => $request->get('pallets', 0)
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
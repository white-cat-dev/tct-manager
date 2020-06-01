<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Client;
use App\Realization;
use App\OrderPayment;
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


            $orders = $query->orderBy('date', 'DESC')->orderBy('number', 'DESC')->get();

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
            $baseRealization = Realization::where('id', $realizationData['id'])->with('order', 'product')->first();

            if ($baseRealization)
            {
                if ($baseRealization->date)
                {
                    continue;
                }

                $realization = Realization::create($this->getRealizationData($realizationData, $baseRealization));
                $performed = $realization->performed;

                if ($performed > $baseRealization->ready)
                {
                    $difference = $performed - $baseRealization->ready;

                    $baseProduction = $baseRealization->order->productions()
                        ->whereNull('date')
                        ->where('product_id', $baseRealization->product_id)
                        ->first();

                    $baseProduction->update([
                        'auto_planned' => $baseProduction->auto_planned - $difference
                    ]);

                    // ProductionsService::getInstance()->updateOrderPlan($baseRealization->order, $baseRealization->product, 0);

                    $otherBaseRealizations = Realization::whereNull('date')
                        ->where('order_id', '!=', $baseRealization->order_id)
                        ->where('product_id', $baseRealization->product_id)
                        ->where('ready', '>', 0)
                        ->get();

                    foreach ($otherBaseRealizations as $otherBaseRealization) 
                    {
                        if ($otherBaseRealization->ready > $difference)
                        {
                            $otherBaseRealization->update([
                                'ready' =>  $otherBaseRealization->ready - $difference
                            ]);

                            $baseRealization->update([
                                'ready' => $baseRealization->ready + $difference
                            ]);

                            $otherBaseProduction = $otherBaseRealization->order->productions()
                                ->whereNull('date')
                                ->where('product_id', $otherBaseRealization->product_id)
                                ->first();

                            $otherBaseProduction->update([
                                'auto_planned' => $otherBaseProduction->auto_planned + $difference
                            ]);

                            // ProductionsService::getInstance()->updateOrderPlan($otherBaseProduction->order, $otherBaseProduction->product, 0);

                            break;
                        }
                        else
                        {
                            $otherBaseRealization->update([
                                'ready' =>  0
                            ]);

                            $baseRealization->update([
                                'ready' => $baseRealization->ready + $otherBaseRealization->ready
                            ]);

                            $otherBaseProduction = $otherBaseRealization->order->productions()
                                ->whereNull('date')
                                ->where('product_id', $otherBaseRealization->product_id)
                                ->first();

                            $otherBaseProduction->update([
                                'auto_planned' => $otherBaseProduction->auto_planned + $otherBaseRealization->ready
                            ]);

                            // ProductionsService::getInstance()->updateOrderPlan($otherBaseProduction->order, $otherBaseProduction->product, 0);

                            $difference -= $otherBaseRealization->ready;
                        }
                    }
                }

                $baseRealization->product->update([
                    'in_stock' => $baseRealization->product->in_stock - $performed
                ]);

                $baseRealization->update([
                    'ready' => $baseRealization->ready - $performed,
                    'performed' => $baseRealization->performed + $performed
                ]);



                $orderBaseRealizations = $baseRealization->order->realizations()->whereNull('date')->get();

                $isOrderFinished = true;

                foreach ($orderBaseRealizations as $orderBaseRealization) 
                {
                    if ($orderBaseRealization->planned > $orderBaseRealization->performed)
                    {
                        $isOrderFinished = false;
                        break;
                    }
                }

                if ($isOrderFinished)
                {
                    $baseRealization->order->update([
                        'status' => Order::STATUS_FINISHED
                    ]);
                }
            }
        }
    }


    public function savePayment(Request $request)
    {
        $paymentData = $this->getPaymentData($request);

        if (!$paymentData['order_id'])
        {
            return;
        }

        $payment = OrderPayment::create($paymentData);

        $payment->order->update([
            'paid' => $payment->order->paid + $payment->paid
        ]);
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



    protected function getRealizationData($data, $baseRealization)
    {
        $date = !empty($data['date_raw']) ? $data['date_raw'] : date('dmY');
        $date = Carbon::createFromFormat('dmY', $date)->format('Y-m-d');

        return [
            'date' => $date,
            'category_id' => $baseRealization->category_id,
            'product_group_id' => $baseRealization->product_group_id,
            'product_id' => $baseRealization->product_id,
            'order_id' => $baseRealization->order_id,
            'planned' => 0,
            'ready' => 0,
            'performed' => !empty($data['performed']) ? (float)$data['performed'] : 0
        ];
    }


    protected function getPaymentData(Request $request)
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

        return [
            'date' => $date,
            'order_id' => $request->get('order_id', 0),
            'paid' => $request->get('paid', 0)
        ];
    }
}
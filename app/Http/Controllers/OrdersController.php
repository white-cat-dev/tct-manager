<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Client;
use App\Realization;
use App\OrderPayment;
use App\Production;
use App\Facility;
use App\Product;
use Carbon\Carbon;
use App\Services\DateService;
use App\Services\ProductionsService;
use Illuminate\Pagination\LengthAwarePaginator;


class OrdersController extends Controller
{
    protected $validationRules = [
        
    ];

    public function index(Request $request) 
    {
        if ($request->wantsJson())
        {
            $today = Carbon::today();

            $month = (int)$request->get('month', $today->month);
            $year = (int)$request->get('year', $today->year);

            $monthes = DateService::getMonthes();
            $years = DateService::getYears(Order::select('date'));


            $query = Order::with('realizations', 'realizations.product');

            $status = $request->get('status', -1);
            if ($status != -1)
            {
                switch ($status) 
                {
                    case 'production':
                        $query->where('status', Order::STATUS_PRODUCTION);
                        break;

                    case 'ready':
                        $query->where('status', Order::STATUS_PRODUCTION);
                        break;

                    case 'unpaid':
                        $query->where('status', Order::STATUS_UNPAID);
                        break;

                    case 'finished':
                        $query->where('status', Order::STATUS_FINISHED);
                        break;

                    case 'new':
                        $query->where('status', Order::STATUS_NEW);
                        break;
                }
            }

            $mainCategories = $request->get('main_category', -1);
            if ($mainCategories != -1)
            {
                $mainCategories = explode(',', $mainCategories);
                $query = $query->whereIn('main_category', $mainCategories);
            }

            $query = $query->orderBy('date', 'DESC')->orderBy('number', 'DESC');

            switch ($status) 
            {
                case 'production':
                    $orders = $query->get();
                    break;

                case 'ready':
                    $tempOrders = $query->get();
                    $orders = collect([]);

                    foreach ($tempOrders as $order) 
                    {
                        $isOrderReady = true;

                        $order->progress = $order->getProgress();
                        foreach ($order->products as $product) 
                        {
                            $product->progress = $product->getProgress($order);

                            if ($product->progress['total'] - $product->progress['realization'] > $product->in_stock)
                            {
                                $isOrderReady = false;
                            }
                        }

                        if ($isOrderReady && ($order->status != Order::STATUS_FINISHED))
                        {
                            $orders->push($order);
                        }
                    }
                    break;

                case 'unpaid':
                    $orders = $query->get();
                    break;

                case 'finished':
                    $orders = $query->whereYear('date', $year)
                        ->whereMonth('date', $month)
                        ->paginate(50);
                    break;

                case 'new':
                    $orders = $query->get();
                    break;

                default:
                    $orders = $query->whereYear('date', $year)
                        ->whereMonth('date', $month)
                        ->paginate(50);
            }

            foreach ($orders as $order) 
            {
                $order->progress = $order->getProgress();
                foreach ($order->products as $product) 
                {
                    $product->progress = $product->getProgress($order);
                }
            }

            return [
                'orders' => ($orders instanceof LengthAwarePaginator) ? $orders->items() : $orders,
                'last_page' => ($orders instanceof LengthAwarePaginator) ? $orders->lastPage() : -1,
                'monthes' => $monthes,
                'years' => $years,
                'year' => $year,
                'month' => $month
            ];
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
            $order->realizations_cost = $order->realizations_cost;
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

            if ($order->paid > 0)
            {
                $order->payments()->create([
                    'date' => $order->date,
                    'paid' => $order->paid
                ]);
            }

            if (!empty($request->get('all_realizations')))
            {   
                foreach ($order->products as $product) 
                {
                    $realization = $order->realizations()->create([
                        'date' => $order->date,
                        'category_id' => $product->category_id,
                        'product_group_id' => $product->product_group_id,
                        'product_id' => $product->id,
                        'order_id' => $order->id,
                        'planned' => 0,
                        'ready' => 0,
                        'performed' => $product->pivot->count
                    ]);

                    $product->updateInStock($product->in_stock - $product->pivot->count, 'realization', $realization);
                }

                $realization = $order->realizations()->create([
                    'date' => $order->date,
                    'category_id' => 0,
                    'product_group_id' => 0,
                    'product_id' => 0,
                    'order_id' => $order->id,
                    'planned' => 0,
                    'ready' => 0,
                    'performed' => $order->pallets
                ]);

                $this->checkFinishedOrder($order);
            }
            else
            {
                ProductionsService::getInstance()->planOrder($order);
            }
            
            return $order;
        }

        return view('index', ['ngTemplate' => 'orders.edit']);
    }


    public function edit(Request $request, Order $order) 
    {
        if ($request->wantsJson())
        {
            if (!empty($request->get('production')))
            {
                $order->update([
                    'status' => Order::STATUS_PRODUCTION
                ]);

                ProductionsService::getInstance()->planOrder($order);
                return $order;
            }
            
            $this->validate($request, $this->validationRules);

            $orderData = $this->getData($request);

            $oldProducts = clone $order->products;

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

            $order->products()->detach($productsIds);


            foreach ($request->get('realizations', []) as $realizationData) 
            {
                if ($realizationData['id'])
                {
                    $realization = $order->realizations()->find($realizationData['id']);
                    $realizationPerformed = $realization->performed;

                    $realization->update($this->getRealizationData($realizationData));

                    $realizationPerformed = $realization->performed - $realizationPerformed;

                    $product = $realization->product;
                    $product->updateInStock($product->in_stock - $realizationPerformed, 'realization', $realization);

                    $baseProduction = $product->getBaseProduction();

                    if ($baseProduction)
                    {
                        $baseProduction->update([
                            'auto_planned' => ($baseProduction->auto_planned > $realizationPerformed) ? ($baseProduction->auto_planned - $realizationPerformed) : 0,
                            'performed' => ($baseProduction->performed > $realizationPerformed) ? ($baseProduction->performed - $realizationPerformed) : 0
                        ]);
                    }
                }

                $this->checkFinishedOrder($order);
            }


            foreach ($request->get('payments') as $paymentData) 
            {
                if ($paymentData['id'])
                {
                    $payment = $order->payments()->find($paymentData['id']);

                    $payment->update($this->getPaymentData($paymentData));
                }
            }


            ProductionsService::getInstance()->replanOrder($order, $oldProducts);

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
            $realizationData = $this->getRealizationData($realizationData);

            if (!$realizationData['order_id'] || $realizationData['performed'] == 0)
            {
                continue;
            }

            $realization = Realization::create($realizationData);

            if ($realization->product)
            {
                $product = $realization->product;
                $product->updateInStock($product->in_stock - $realization->performed, 'realization', $realization);

                $baseProduction = $product->getBaseProduction();

                if ($baseProduction)
                {
                    $baseProduction->update([
                        'auto_planned' => ($baseProduction->auto_planned > $realization->performed) ? ($baseProduction->auto_planned - $realization->performed) : 0,
                        'performed' => ($baseProduction->performed > $realization->performed) ? ($baseProduction->performed - $realization->performed) : 0
                    ]);
                }
            }

            $order = $realization->order;
        }

        if (!empty($order))
        {
            $this->checkFinishedOrder($order);
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

        $this->checkFinishedOrder($payment->order);
    }


    protected function checkFinishedOrder($order)
    {
        $isOrderFinished = true;

        foreach ($order->products as $product) 
        {
            $progress = $product->getProgress($order);
            if ($progress['realization'] < $progress['total'])
            {
                $isOrderFinished = false;
                break;
            }
        }

        if ($isOrderFinished) 
        {
            if ($order->paid >= $order->cost)
            {
                $order->update([
                    'status' => Order::STATUS_FINISHED
                ]);
            }
            else
            {
                $order->update([
                    'status' => Order::STATUS_UNPAID
                ]);   
            }
        }
        else
        {
            $order->update([
                'status' => Order::STATUS_PRODUCTION
            ]);
        }
    }


    public function getDate(Request $request)
    {
        $orderData = $this->getData($request);
        // $priority = $orderData['priority'];

        $products = [];

        foreach ($request->get('products', []) as $productData) 
        {
            if (!$productData['id'])
            {
                continue;
            }

            $product = Product::find($productData['id']);

            $products[] = [
                'product' => $product,
                'count' => $productData['pivot']['count']
            ];
        }

        $dateTo = ProductionsService::getInstance()->testPlanOrder($products);

        if ($dateTo > $orderData['date'])
        {
            $dateTo = Carbon::createFromDate($dateTo)->addDays(2)->format('Y-m-d');
        }
       
        return [
            'date' => $dateTo
        ];
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

        $dateTo = $request->get('date_to_raw', -1); 
        if ($dateTo == -1)
        {
            $dateTo = $request->get('date_to', date('Y-m-d')); 
        }
        else
        {
            $dateTo = $dateTo ? Carbon::createFromFormat('dmY', $dateTo)->format('Y-m-d') : date('Y-m-d');
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
            'date_to' => $dateTo,
            'number' => $number,
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
            'pallets_price' => $request->get('pallets_price', 200)
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



    protected function getRealizationData($data)
    {
        $date = !empty($data['date_raw']) ? $data['date_raw'] : date('dmY');
        $date = Carbon::createFromFormat('dmY', $date)->format('Y-m-d');

        return [
            'date' => $date,
            'category_id' => !empty($data['product']['category_id']) ? (float)$data['product']['category_id'] : 0,
            'product_group_id' => !empty($data['product']['product_group_id']) ? (float)$data['product']['product_group_id'] : 0,
            'product_id' => !empty($data['product']['id']) ? (float)$data['product']['id'] : 0,
            'order_id' => !empty($data['order_id']) ? (float)$data['order_id'] : 0,
            'planned' => 0,
            'ready' => 0,
            'performed' => !empty($data['performed']) ? (float)$data['performed'] : 0
        ];
    }


    protected function getPaymentData($data)
    {
        $date = !empty($data['date_raw']) ? $data['date_raw'] : date('dmY');
        $date = Carbon::createFromFormat('dmY', $date)->format('Y-m-d');

        return [
            'date' => $date,
            'order_id' => !empty($data['order_id']) ? (float)$data['order_id'] : 0,
            'paid' => !empty($data['paid']) ? (float)$data['paid'] : 0
        ];
    }
}
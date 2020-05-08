<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Production;
use App\Realization;
use App\Product;
use App\Order;
use App\Facility;


class ProductionController extends Controller
{
    protected $validationRules = [
        'name' => 'required'
    ];

    public function index(Request $request) 
    {
        if ($request->wantsJson())
        {
            $today = Carbon::today();

            $month = (int)$request->get('month', $today->month);
            $year = (int)$request->get('year', $today->year);

            if (($month == $today->month) && ($year == $today->year))
            {
                $day = (int)$today->day;
            }
            else
            {
                $day = 0;
            }

            $products = Product::whereHas('productions', function($query) use ($year, $month) {
                    $query->whereYear('date', $year)
                        ->whereMonth('date', $month);
                })
                ->get();

            $allOrders = collect([]);

            foreach ($products as $key => $product) 
            {
                $productions = $products[$key]->productions()
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->orWhereNull('date')
                    ->get();

                $productProductions = [];

                foreach ($productions as $production) 
                {
                    $day = $production->day;
                    if (empty($productProductions[$day]))
                    {
                        $productProductions[$day] = clone $production;
                    }
                    else
                    {
                        if (is_array($productProductions[$production->day]->order_id))
                        {
                            $productProductions[$production->day]->order_id = array_merge($productProductions[$day]->order_id, [$production->order_id]);
                        }
                        else
                        {
                            $orderId = $productProductions[$day]->order_id;
                            $productProductions[$day]->order_id = [$orderId, $production->order_id];
                        }

                        $productProductions[$day]->planned += $production->planned;
                        $productProductions[$day]->performed += $production->performed;
                        $productProductions[$day]->batches += $production->batches;
                    }
                }


                $ordersIds = $productions->pluck('order_id')->unique()->values();

                $productOrders = Order::find($ordersIds);

                foreach ($productOrders as $order) 
                {
                    $allOrders->push($order);
                    $order->productions = $productions->where('order_id', $order->id)->keyBy('day');
                }

                $product->productions = $productProductions;
                $product->orders = $productOrders->sortBy('priority')->sortBy('date');

                if ($ordersIds->contains(0))
                {
                    $product->orders->push(new Order([
                        'id' => 0,
                        'productions' => $productions->where('order_id', $order->id)->keyBy('day')
                    ]));
                }
            }

            $days = Carbon::createFromDate($year, $month, 1)->daysInMonth;

            $monthes = [
                ['id' => 1, 'name' => 'Январь'],
                ['id' => 2, 'name' => 'Февраль'],
                ['id' => 3, 'name' => 'Март'],
                ['id' => 4, 'name' => 'Апрель'],
                ['id' => 5, 'name' => 'Май'],
                ['id' => 6, 'name' => 'Июнь'],
                ['id' => 7, 'name' => 'Июль'],
                ['id' => 8, 'name' => 'Август'],
                ['id' => 9, 'name' => 'Сентябрь'],
                ['id' => 10, 'name' => 'Октябрь'],
                ['id' => 11, 'name' => 'Ноябрь'],
                ['id' => 12, 'name' => 'Декабрь']
            ];

            $years = Production::select('date')->groupBy('date')->get()->groupBy(function($date) {
                return Carbon::parse($date->created_at)->format('Y');
            })->keys();

            $years[] = $today->year - 1;
            $years[] = $today->year;
            $years[] = $today->year + 1;

            $years = $years->unique()->sort()->values();

            $facilities = Facility::all()->keyBy('id');
            
               
            return ['days' => $days,
                    'monthes' => $monthes,
                    'years' => $years,
                    'day' => 8,
                    'year' => $year,
                    'month' => $month,
                    'products' => $products,
                    'facilities' => $facilities,
                    'orders' => $allOrders->unique('id')
                ];
        }

        return view('index', ['ngTemplate' => 'production']);
    }


    // public function orders(Request $request)
    // {
    //     if ($request->wantsJson())
    //     {
    //         $today = Carbon::today();

    //         $day = $request->get('day', $today->day);
    //         $month = $request->get('month', $today->month);
    //         $year = $request->get('year', $today->year);

    //         $date = Carbon::createFromDate($year, $month, $day);

    //         $productId = $request->get('product_id');

    //         $orders = Order::whereHas('productions', function($query) use ($date, $productId) {
    //                 $query->where('date', $date->format('Y-m-d'));
    //                 if ($productId)
    //                 {
    //                     $query->where('product_id', $productId);
    //                 }
    //             })
    //             ->with(['productions' => function($query) use ($date, $productId) {
    //                 $query->where('date', $date->format('Y-m-d'))
    //                     ->with('product');
    //                 if ($productId)
    //                 {
    //                     $query->where('product_id', $productId);
    //                 }
    //             }])
    //             ->get();

    //         $noOrderProductions = Production::where('date', $date->format('Y-m-d'))
    //             ->where('order_id', 0)
    //             ->with('product')
    //             ->get();
                
    //         return ['orders' => $orders,
    //                 'no_order' => $noOrderProductions,
    //                 'date' => $date->format('d.m.Y')];
    //     }
    // }


    public function save(Request $request)
    {
        $productsData = $request->get('products');

        foreach ($productsData as $productData) 
        {
            foreach ($productData['orders'] as $orderData) 
            {
                $productionData = $orderData['production'];

                if (!empty($productionData['id']))
                {
                    $production = Production::find($productionData['id']);
                }

                if (!empty($production))
                {
                    $performed = $production->performed;

                    $planned = (float)$productionData['planned'];
                    $manualPlanned = ($planned != $production->auto_planned) ? $planned : $production->manual_planned;

                    $production->update([
                        'auto_planned' => $planned,
                        'manual_planned' => $manualPlanned,
                        'performed' => (float)$productionData['performed'],
                        'facility_id' => $productionData['facility_id']
                    ]);

                    $performed = $production->performed - $performed;

                    $product = Product::find($productionData['product_id']);
                    if ($product)
                    {
                        $product->update([
                            'in_stock' => $product->in_stock + $performed
                        ]);
                    }

                    $baseProduction = Production::where('order_id', $production->order_id)
                        ->where('product_id', $production->product_id)
                        ->whereNull('date')
                        ->first();

                    if ($baseProduction)
                    {
                        if ($baseProduction->auto_planned <= $performed)
                        {
                            $baseProduction->order->update([
                                'status' => Order::STATUS_READY
                            ]);
                            $baseProduction->delete();
                        }
                        else
                        {
                            $baseProduction->update([
                                'auto_planned' => $baseProduction->auto_planned - $performed
                            ]);
                        }
                    }

                    $baseRealization = Realization::where('order_id', $production->order_id)
                        ->where('product_id', $production->product_id)
                        ->whereNull('date')
                        ->first();

                    if ($baseRealization)
                    {
                        $baseRealization->update([
                            'planned' => $baseRealization->planned + $performed
                        ]);
                    }
                }
                else
                {
                    $production = Production::create($productionData);
                    $product = Product::find($productionData['product_id']);
                    if ($product)
                    {
                        $product->update([
                            'in_stock' => $baseProduction->product->in_stock + $performed
                        ]);
                    }
                }
            }
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Production;
use App\Product;
use App\Order;


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

            if (($month == $today->month) && ($year ==$today->year))
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

            foreach ($products as $key => $product) 
            {
                $products[$key]->productions = $products[$key]->productions()
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->get()
                    ->keyBy('day');
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
            
               
            return ['days' => $days,
                    'monthes' => $monthes,
                    'years' => $years,
                    'day' => $day,
                    'year' => $year,
                    'month' => $month,
                    'products' => $products];
        }

        return view('index', ['ngTemplate' => 'production']);
    }


    public function orders(Request $request)
    {
        if ($request->wantsJson())
        {
            $today = Carbon::today();

            $day = $request->get('day', $today->day);
            $month = $request->get('month', $today->month);
            $year = $request->get('year', $today->year);

            $date = Carbon::createFromDate($year, $month, $day);

            $productId = $request->get('product_id');

            $orders = Order::whereHas('productions', function($query) use ($date, $productId) {
                    $query->where('date', $date->format('Y-m-d'));
                    if ($productId)
                    {
                        $query->where('product_id', $productId);
                    }
                })
                ->with(['productions' => function($query) use ($date, $productId) {
                    $query->where('date', $date->format('Y-m-d'))
                        ->with('product');
                    if ($productId)
                    {
                        $query->where('product_id', $productId);
                    }
                }])
                ->get();

            $noOrderProductions = Production::where('date', $date->format('Y-m-d'))
                ->where('order_id', 0)
                ->with('product')
                ->get();
                
            return ['orders' => $orders,
                    'no_order' => $noOrderProductions,
                    'date' => $date->format('d.m.Y')];
        }
    }


    public function save(Request $request)
    {
        $productionsData = $request->get('productions');

        foreach ($productionsData as $productionData) 
        {
            $production = Production::find($productionData['id']);

            if ($production)
            {
                $production->update([
                    'performed' => (float)$productionData['performed']
                ]);
            }
        }
    }
}
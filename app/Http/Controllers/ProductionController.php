<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use Carbon\Carbon;


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

            $day = $request->get('month', $today->day);
            $month = $request->get('month', $today->month);
            $year = $request->get('year', $today->year);

            $products = Product::with('product_group')
                ->whereHas('productions', function($query) use ($year, $month) {
                    $query->whereYear('date', $year)
                        ->whereMonth('date', $month);
                })
                ->get();

            foreach ($products as $key => $product) 
            {
                $products[$key]->productions = $products[$key]->productions()
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->with('order')
                    ->get()
                    ->keyBy('day');
            }

            $days = Carbon::createFromDate($year, $month, 1)->daysInMonth;
               
            return ['days' => $days,
                    'day' => $day,
                    'year' => $year,
                    'month' => $month,
                    'products' => $products];
        }

        return view('index', ['ngTemplate' => 'production']);
    }
}
<?php

namespace App\Services;

use DB;
use Carbon\Carbon;
use App\Category;
use App\Realization;
use App\Facility;
use App\Production;


class ProductionsService
{
    protected static $instance;

    public static function getInstance() 
    {
        if (static::$instance === null) 
        {
            static::$instance = new static;  
        }
 
        return static::$instance;
    }


    public function planOrder($order, $startToday = false)
    {
        foreach ($order->products as $product)
        {
            $productCount = $product->pivot->count;

            $baseRealization = $this->getBaseRealization($order, $product, $productCount);
            $productCount -= $baseRealization->planned;

            if ($productCount == 0)
            {
                continue;
            }

            $baseProduction = $this->getBaseProduction($order, $product, $productCount);

            $currentDay = Carbon::today();

            if (!$startToday)
            {
                $currentDay = $currentDay->addDay();
            }

            $facilities = Facility::whereHas('categories', function($query) use ($product) {
                $query->where('categories.id', $product->category_id);
            })->get();

            $facilitiesBatches = 0;

            foreach ($facilities as $facility) 
            {
                $facilitiesBatches += $facility->getPerformance($currentDay->format('Y-m-d'));
            }

            $facilitiesBatches = $facilities->sum('performance');


            while ($productCount > 0)
            {
                $categoryProduction = $this->getCategoryProduction($currentDay, $product);

                $currentProductions = Production::where('date', $currentDay->format('Y-m-d'))->get();

                $currentBatches = 0;

                foreach ($currentProductions as $currentProduction) 
                {
                    $currentBatches += $currentProduction->batches;
                }
                if ($currentBatches >= $facilitiesBatches)
                {
                    continue;
                }

                $maxCount = $product->product_group->units_from_batch * ($facilitiesBatches - $currentBatches);
                if ($maxCount > $product->product_group->forms)
                {
                    $maxCount = $product->product_group->forms;
                }
                $plannedCount = ($productCount >= $maxCount) ? $maxCount : $productCount;

                $production = Production::create([
                    'date' => $currentDay->format('Y-m-d'),
                    'category_id' => $product->category_id,
                    'product_group_id' => $product->product_group_id,
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'facility_id' => ($facilities->count() == 1) ? $facilities->first()->id : 0,
                    'auto_planned' => $plannedCount,
                    'manual_planned' => 0,
                    'performed' => 0,
                    'batches' => $plannedCount / $product->product_group->units_from_batch,
                    'salary' => 0
                ]);

                $productCount -= $plannedCount;

                $currentDay = $currentDay->addDay();
            }
        }
    }


    public function planOrders()
    {

    }


    public function updateProductions()
    {

    }



    protected function getBaseRealization($order, $product, $productCount)
    {
        $baseRealization = Realization::create([
            'date' => null,
            'category_id' => $product->category_id,
            'product_id' => $product->id,
            'order_id' => $order->id,
            'planned' => ($productCount >= $product->free_in_stock) ? $product->free_in_stock : $productCount,
            'performed' => 0
        ]);

        return $baseRealization;
    }


    protected function getBaseProduction($order, $product, $productCount)
    {
        $baseProduction = Production::where([
                'date' => null,
                'order_id' => $order->id,
                'product_id' => $product->id
            ])
            ->first();

        if (!$baseProduction)
        {    
            $baseProduction = Production::create([
                'date' => null,
                'category_id' => $product->category_id,
                'product_group_id' => $product->product_group_id,
                'order_id' => $order->id,
                'product_id' => $product->id,
                'facility_id' => 0,
                'auto_planned' => $productCount,
                'manual_planned' => 0,
                'performed' => 0,
                'batches' => $productCount / $product->product_group->units_from_batch,
                'salary' => 0
            ]);
        }

        return $baseProduction;
    }


    protected function getCategoryProduction($day, $product)
    {
        $categoryProduction = Production::where([
            'date' => $day->format('Y-m-d'),
            'category_id' => $product->category_id,
            'order_id' => 0,
            'product_id' => 0
        ])->first();

        if (!$categoryProduction)
        {
            $categoryProduction = Production::create([
                'date' => $day->format('Y-m-d'),
                'category_id' => $product->category_id,
                'product_group_id' => 0,
                'order_id' => 0,
                'product_id' => 0,
                'facility_id' => 0,
                'auto_planned' => 0,
                'manual_planned' => 0,
                'performed' => 0,
                'batches' => 0,
                'salary' => 0
            ]);
        }

        return $categoryProduction;
    }
}
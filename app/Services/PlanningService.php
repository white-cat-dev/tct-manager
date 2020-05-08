<?php

namespace App\Services;

use DB;
use App\Category;
use Carbon\Carbon;
use App\Realization;
use App\Facility;
use App\Production;


class PlanningService
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


    public function planOrder($order)
    {
        foreach ($order->products as $product)
        {
            $productCount = $product->pivot->count;
            if ($product->free_in_stock > 0)
            {
                $baseRealization = Realization::create([
                    'date' => null,
                    'category_id' => $product->category_id,
                    'product_id' => $product->id,
                    'order_id' => $order->id,
                    'planned' => ($productCount >= $product->free_in_stock) ? $product->free_in_stock : $productCount,
                    'performed' => 0
                ]);

                $productCount -= $baseRealization->planned;
            }

            if ($productCount == 0)
            {
                continue;
            }

            $baseProduction = $this->getBaseProduction($order, $product, $productCount);

            $currentDay = Carbon::today();

            $maxBatches = Facility::whereHas('categories', function($query) use ($product) {
                $query->where('categories.id', $product->category_id);
            })->get()->sum('performance');

            while ($productCount > 0)
            {
                $currentDay = $currentDay->addDay();

                $categoryProduction = $this->getCategoryProduction($currentDay, $product);

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

                $maxCount = $product->product_group->units_from_batch * ($maxBatches - $currentBatches);
                if ($maxCount > $product->product_group->forms)
                {
                    $maxCount = $product->product_group->forms;
                }
                $plannedCount = ($productCount >= $maxCount) ? $maxCount : $productCount;

                $production = Production::create([
                    'date' => $currentDay->format('Y-m-d'),
                    'category_id' => $product->category_id,
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'facility_id' => 0,
                    'auto_planned' => $plannedCount,
                    'manual_planned' => 0,
                    'performed' => 0,
                    'batches' => $plannedCount / $product->product_group->units_from_batch
                ]);

                $productCount -= $plannedCount;
            }
        }
    }


    public function planOrders()
    {

    }


    public function updateProductions()
    {

    }



    protected function getBaseProduction($order, $product, $productCount)
    {
        $baseProduction = Production::where([
            'date' => null,
            'order_id' => $order->id,
            'product_id' => $product->id
        ])->first();

        if (!$baseProduction)
        {    
            $baseProduction = Production::create([
                'date' => null,
                'category_id' => $product->category_id,
                'order_id' => $order->id,
                'product_id' => $product->id,
                'facility_id' => 0,
                'auto_planned' => $productCount,
                'manual_planned' => 0,
                'performed' => 0,
                'batches' => $productCount / $product->product_group->units_from_batch
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
                'order_id' => 0,
                'product_id' => 0,
                'facility_id' => 0,
                'auto_planned' => 0,
                'manual_planned' => 0,
                'performed' => 0,
                'batches' => 0
            ]);
        }

        return $categoryProduction;
    }
}
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
                $realization = Realization::create([
                    'date' => date('Y-m-d'),
                    'product_id' => $product->id,
                    'order_id' => $order->id,
                    'planned' => ($productCount >= $product->free_in_stock) ? $product->free_in_stock : $productCount,
                    'performed' => 0
                ]);

                $productCount -= $realization->planned;
            }

            if ($productCount == 0)
            {
                continue;
            }

            $baseProduction = Production::create([
                'date' => null,
                'order_id' => $order->id,
                'product_id' => $product->id,
                'facility_id' => 0,
                'planned' => $productCount,
                'performed' => 0,
                'batches' => $productCount / $product->product_group->units_from_batch
            ]);

            $currentDay = Carbon::today();

            $maxBatches = Facility::whereHas('categories', function($query) use ($product) {
                $query->where('categories.id', $product->category_id);
            })->get()->sum('performance');

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

                $maxCount = $product->product_group->units_from_batch * ($maxBatches - $currentBatches);
                if ($maxCount > $product->product_group->forms / $product->product_group->unit_in_units)
                {
                    $maxCount = $product->product_group->forms / $product->product_group->unit_in_units;
                }
                $plannedCount = ($productCount >= $maxCount) ? $maxCount : $productCount;

                $production = Production::create([
                    'date' => $currentDay->format('Y-m-d'),
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'facility_id' => 0,
                    'planned' => $plannedCount,
                    'performed' => 0,
                    'batches' => $plannedCount / $product->product_group->units_from_batch
                ]);

                $productCount -= $plannedCount;
            }
        }
    }
}
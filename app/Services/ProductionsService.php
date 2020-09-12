<?php

namespace App\Services;

use DB;
use Carbon\Carbon;
use App\Category;
use App\Realization;
use App\Facility;
use App\Production;
use App\Order;


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


    public function testPlanOrder($products)
    {
        $orderDateTo = date('Y-m-d');

        foreach ($products as $productData) 
        {
            $product = $productData['product'];
            $count = $productData['count'];

            $baseProduction = $product->getBaseProduction();

            if ($baseProduction)
            {
                $basePlanned = $baseProduction->auto_planned + $count;
                $basePerformed = ($product->in_stock > $basePlanned) ? $basePlanned : $product->in_stock;
            }
            else
            {
                $basePlanned = $count;
                $basePerformed = ($product->in_stock > $count) ? $count : $product->in_stock;
            }

            $basePlanned = $basePlanned - $basePerformed;
            $lastBatches = ($basePlanned <= 100) ? (($basePlanned <= 50) ? 1 : 2) : 3;
            
            $facilities = Facility::whereHas('categories', function($query) use ($product) {
                $query->where('categories.id', $product->category_id);
            })->get();

            $currentDay = Carbon::today()->addDay();

            while ($basePlanned > 0)
            {
                if (($product->product_group->forms == 0) && ($product->product_group->performance == 0))
                {
                    break;
                }

                $production = $this->getProduction($currentDay, $product, $basePlanned, $lastBatches, $facilities, true);

                if ($production)
                {
                    if ($production->manual_batches > 0)
                    {
                        $lastBatches = $production->manual_batches;
                    }
                    $basePlanned -= $production->planned;
                }

                $currentDay = $currentDay->addDay();
            }

            $currentDay = $currentDay->subDay()->format('Y-m-d');

            if ($currentDay > $orderDateTo)
            {
                $orderDateTo = $currentDay;
            }
        }

        return $orderDateTo;
    }


    public function planOrder($order)
    {
        foreach ($order->products as $product)
        {
            $this->planOrderProduct($order, $product);
        }
    }


    public function replanOrder($order, $oldProducts)
    {
        $oldProductsIds = $oldProducts->pluck('id', 'id');

        $order->refresh();

        foreach ($order->products as $product)
        {
            $oldProductsIds->forget($product->id);

            $oldProduct = $oldProducts->find($product->id);

            if (!$oldProduct)
            {
                $this->planOrderProduct($order, $product);
            }
            else
            {
                $productCount = $oldProduct->pivot->count - $product->pivot->count;

                $baseProduction = $product->getBaseProduction();

                $autoPlanned = $baseProduction->auto_planned - $productCount;
                if ($autoPlanned < 0)
                {
                    $autoPlanned = 0;
                }

                $baseProduction->update([
                    'auto_planned' => $autoPlanned,
                    'performed' => ($baseProduction->performed > $autoPlanned) ? $autoPlanned : $baseProduction->performed
                ]);

                $this->replanProduct($product);
            }
        }

        foreach ($oldProductsIds as $oldProductId) 
        {
            $oldProduct = $oldProducts->find($oldProductId);

            $baseProduction = $oldProduct->getBaseProduction();

            $autoPlanned = $baseProduction->auto_planned - $oldProduct->pivot->count;
            if ($autoPlanned < 0)
            {
                $autoPlanned = 0;
            }

            $baseProduction->update([
                'auto_planned' => $autoPlanned,
                'performed' => ($baseProduction->performed > $autoPlanned) ? $autoPlanned : $baseProduction->performed
            ]);

            $this->replanProduct($oldProduct);
        }
    }


    protected function planOrderProduct($order, $product)
    {
        $count = $product->pivot->count;

        $progress = $product->getProgress($order);

        $count -= $progress['realization'];

        $baseProduction = $product->getBaseProduction();

        if ($baseProduction)
        {
            $baseProductionPlanned = $baseProduction->auto_planned + $count;

            $baseProduction->update([
                'auto_planned' => $baseProductionPlanned,
                'performed' => ($baseProduction->product->in_stock > $baseProductionPlanned) ? $baseProductionPlanned : $baseProduction->product->in_stock
            ]);
        }
        else
        {
            $baseProduction = Production::create([
                'date' => null,
                'category_id' => $product->category_id,
                'product_group_id' => $product->product_group_id,
                'product_id' => $product->id,
                'order_id' => 0,
                'facility_id' => 0,
                'manual_planned' => -1,
                'auto_planned' => $count,
                'priority' => $order->priority,
                'priority' => 1,
                'date_to' => null,
                'performed' => ($product->in_stock > $count) ? $count : $product->in_stock,
                'auto_batches' => 0,
                'manual_batches' => -1,
                'salary' => 0
            ]);
        }

        $this->replanProduct($product);  
    }


    public function replanProduct($product)
    {
        $baseProduction = $product->getBaseProduction();

        if (($baseProduction->auto_planned == 0) && ($baseProduction->performed == 0))
        {
            $baseProduction->update([
                'priority' => 1
            ]);

            return;
        }

        $basePlanned = $baseProduction->auto_planned - $baseProduction->performed;
        $baseBatches = ceil($basePlanned / $product->product_group->units_from_batch);
        $lastBatches = ($basePlanned <= 100) ? (($basePlanned <= 50) ? 1 : 2) : 3;

        $productions = $product->productions()->whereNotNull('date')->where('date', '>=', date('Y-m-d'))->get();

        $planned = 0;
        foreach ($productions as $production) 
        {
            if ($production->performed > 0)
            {
                continue;
            }

            $planned += $production->planned;
        }
        $batches = ceil($planned / $product->product_group->units_from_batch);


        if ($batches != $baseBatches)
        {
            $facilities = Facility::whereHas('categories', function($query) use ($product) {
                $query->where('categories.id', $product->category_id);
            })->get();

            $currentDay = Carbon::today()->addDay();

            while ($basePlanned > 0)
            {
                if (($product->product_group->forms == 0) && ($product->product_group->performance == 0))
                {
                    break;
                }

                $production = $this->getProduction($currentDay, $product, $basePlanned, $lastBatches, $facilities);

                if ($production)
                {
                    if ($production->manual_batches > 0)
                    {
                        $lastBatches = $production->manual_batches;
                    }

                    $basePlanned -= $production->planned;
                }

                $currentDay = $currentDay->addDay();
            }

            $currentDay = $currentDay->subDay()->format('Y-m-d');

            Production::where('product_id', $product->id)
                ->where('performed', 0)
                ->whereNotNull('date')
                ->where('date', '>', $currentDay)
                ->delete();

            $baseProduction->update([
                'date_to' => $currentDay
            ]);
        }
    }


    public function planOrders()
    {
        $orders = Order::where('status', '!=', Order::STATUS_FINISHED)->get();

        foreach ($orders as $order) 
        {
            $this->planOrder($order);
        }
    }


    public function replanOrders()
    {
        $orders = Order::where('status', '!=', Order::STATUS_FINISHED)->get();

        foreach ($orders as $order) 
        {
            $this->replanOrder($order, $order->products);
        }
    }


    public function replan()
    {
        Production::whereNull('date')
            ->orWhere(function ($query) {
                $query->whereNotNull('date')
                    ->where('performed', 0)
                    ->where('manual_planned', '<', 0);
            })->delete();

        $this->planOrders();
    }


    protected function getProduction($day, $product, $count, $lastBatches, $facilities, $testPlan = false)
    {
        $currentProductions = Production::where('date', $day->format('Y-m-d'))->get();

        $production = $currentProductions
            ->where('product_id', $product->id)
            ->first(); 

        if ($production && ($production->performed > 0))
        {
            return;
        }

        // $facilitiesBatches = $this->getFacilityBatches($day, $facilities);

        // $currentBatches = 0;
        // $currentForms = 0;

        // foreach ($currentProductions as $currentProduction) 
        // {
        //     if ($currentProduction->product_id == $product->id)
        //     {
        //         continue;
        //     }

        //     $currentBatches += $currentProduction->batches;

        //     if ($currentProduction->product_group_id == $product->product_group_id)
        //     {
        //         $currentForms += $currentProduction->planned;
        //     }
        // }


        // $currentBatches = $facilitiesBatches - $currentBatches;
        // $currentBatches = $facilitiesBatches;

        // if ($currentBatches <= 0)
        // {
        //     return;
        // }

        // $currentForms = $product->product_group->forms - $currentForms;
        // $currentForms = $product->product_group->forms;

        // if ($currentForms <= 0)
        // {
        //     return;
        // }

        // switch ($priority) 
        // {
        //     case Order::PRIORITY_NORMAL:
        //         $maxForms = $product->product_group->units_from_batch;
        //         break;
            
        //     case Order::PRIORITY_HIGH:
        //         $maxForms = 2 * $product->product_group->units_from_batch;
        //         if ($maxForms > $product->product_group->forms)
        //         {
        //             $maxForms = $product->product_group->units_from_batch;
        //         }
        //         break;

        //     case Order::PRIORITY_VERY_HIGH:
        //         $maxForms = $product->product_group->forms;
        //         break;

        //     default:
        //         $maxForms = 0;
        //         break;
        // }

        // $maxBatches = round($maxForms / $product->product_group->units_from_batch, 1);

        // if ($maxForms > $currentForms)
        // {
        //     $maxForms = $currentForms;
        // }
        
        // if ($maxBatches > $currentBatches) 
        // {
        //     $maxBatches = $currentBatches;
        // }

        // if ($maxForms > $maxBatches * $product->product_group->units_from_batch)
        // {
        //     $plannedBatches = round($maxForms / $product->product_group->units_from_batch, 1);
        // }
        // else
        // {
        //     $plannedBatches = round($maxBatches, 1);
        // }

        $plannedBatches = $lastBatches;

        if ($plannedBatches == 0)
        {
            return;
        }

        if ($plannedBatches * $product->product_group->units_from_batch >= $count)
        {
            $plannedBatches = ceil($count / $product->product_group->units_from_batch);
        }

        $plannedCount = $plannedBatches * $product->product_group->units_from_batch;


        if ($production)
        {
            if ($production->manual_planned < 0)
            {
                if ($testPlan)
                {
                    $production->auto_planned = $plannedBatches * $product->product_group->units_from_batch;
                    $production->auto_batches = $plannedBatches;
                    $production->priority = $priority;
                }
                else
                {
                    $production->update([
                        'auto_planned' => $plannedBatches * $product->product_group->units_from_batch,
                        'auto_batches' => $plannedBatches
                    ]);
                }
            }
        }
        else
        {
            if ($testPlan)
            {
                $production = new Production([
                    'date' => $day->format('Y-m-d'),
                    'category_id' => $product->category_id,
                    'product_group_id' => $product->product_group_id,
                    'order_id' => 0,
                    'product_id' => $product->id,
                    'facility_id' => ($facilities->count() == 1) ? $facilities->first()->id : 0,
                    'auto_planned' => $plannedBatches * $product->product_group->units_from_batch,
                    'manual_planned' => -1,
                    'priority' => 1,
                    'performed' => 0,
                    'auto_batches' => $plannedBatches,
                    'manual_batches' => -1,
                    'salary' => 0
                ]);
            }
            else
            {
                $production = Production::create([
                    'date' => $day->format('Y-m-d'),
                    'category_id' => $product->category_id,
                    'product_group_id' => $product->product_group_id,
                    'order_id' => 0,
                    'product_id' => $product->id,
                    'facility_id' => ($facilities->count() == 1) ? $facilities->first()->id : 0,
                    'auto_planned' => $plannedBatches * $product->product_group->units_from_batch,
                    'manual_planned' => -1,
                    'priority' => 1,
                    'performed' => 0,
                    'auto_batches' => $plannedBatches,
                    'manual_batches' => -1,
                    'salary' => 0
                ]);
            }
        }

        return $production;
    }


    protected function getFacilityBatches($day, $facilities)
    {
        $facilitiesBatches = 0;

        foreach ($facilities as $facility) 
        {
            $facilitiesBatches += $facility->getPerformance($day->format('Y-m-d'));
        }

        $facilitiesBatches = $facilities->sum('performance');

        return $facilitiesBatches;
    }


    protected function deleteProductions($day, $order, $product)
    {
        Production::where([
                'order_id' => $order->id,
                'product_id' => $product->id
            ])
            ->where('date', '>=', $day->format('Y-m-d'))
            ->delete();
    }
}
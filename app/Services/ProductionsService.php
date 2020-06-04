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


    public function planOrder($order)
    {
        foreach ($order->products as $product)
        {
            $this->planOrderProduct($order, $product);
        }

            // $baseProduction = $this->getBaseProduction($order, $product, $productCount);

        

            // $currentDay = Carbon::today();

            // if (!$startToday)
            // {
            //     $currentDay = $currentDay->addDay();
            // }

            // $facilities = Facility::whereHas('categories', function($query) use ($product) {
            //     $query->where('categories.id', $product->category_id);
            // })->get();

            // while ($productCount > 0)
            // {
            //     $production = $this->getProduction($currentDay, $order, $product, $productCount, $facilities);

            //     if ($production === -1)
            //     {
            //         break;
            //     }
            //     else if ($production)
            //     {
            //         $productCount -= $production->planned;
            //     }

            //     $currentDay = $currentDay->addDay();
            // }

            // $this->deleteProductions($currentDay, $order, $product);
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

                $baseProduction->update([
                    'auto_planned' => $autoPlanned,
                    'performed' => ($baseProduction->performed > $autoPlanned) ? $autoPlanned : $baseProduction->performed
                ]);
            }
        }

        foreach ($oldProductsIds as $oldProductId) 
        {
            $oldProduct = $oldProducts->find($oldProductId);

            $baseProduction = $oldProduct->getBaseProduction();

            $autoPlanned = $baseProduction->auto_planned - $oldProduct->pivot->count;

            $baseProduction->update([
                'auto_planned' => $autoPlanned,
                'performed' => ($baseProduction->performed > $autoPlanned) ? $autoPlanned : $baseProduction->performed
            ]);
        }
    }


    protected function planOrderProduct($order, $product)
    {
        $productCount = $product->pivot->count;

        $progress = $product->getProgress($order);

        $productCount -= $progress['realization'];

        $baseProduction = $product->getBaseProduction();

        if ($baseProduction)
        {
            $freeInStock = ($product->in_stock > $baseProduction->performed) ? ($product->in_stock - $baseProduction->performed) : 0;

            $baseProduction->update([
                'auto_planned' => $baseProduction->auto_planned + $productCount,
                'performed' => $baseProduction->performed + ($freeInStock > $productCount) ? $productCount : $freeInStock
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
                'manual_planned' => 0,
                'auto_planned' => $productCount,
                'performed' => ($product->in_stock > $productCount) ? $productCount : $product->in_stock,
                'batches' => 0,
                'salary' => 0
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


    public function updateOrderPlan($order, $product, $productCount)
    {
        $baseProduction = $this->updateBaseProduction($order, $product, $productCount);

        return $baseProduction;

        // $baseRealization = $this->updateBaseRealization($order, $product, $productCount);

        // $productions = Production::where('order_id', $order->id)
        //     ->where('product_id', $product->id)
        //     ->whereNotNull('date')
        //     ->get();

        // $planned = 0;
        // foreach ($productions as $production) 
        // {
        //     $planned += $production->planned;
        // }

        // dd($planned, $baseProduction);

        // if ($planned > $baseProduction->auto_planned)
        // {
        //     $difference = $planned - $baseProduction->auto_planned;
        //     $lastDay = $productions->sortByDesc('date')->first()->date;
        //     $differenceDays = 0;

        //     foreach ($productions->sortByDesc('date') as $production) 
        //     {
        //         if ($production->planned <= $difference)
        //         {
        //             $difference -= $production->planned;
        //             $production->delete();
        //             $differenceDays += 1;
        //         }
        //         else
        //         {
        //             $production->update([
        //                 'auto_planned' => $production->planned - $difference
        //             ]);

        //             break;
        //         }
        //     }

        //     if ($differenceDays > 0)    
        //     {
        //         $nextProductions = Production::where('date', '>', $lastDay)
        //             ->where('product_id', $product->id)
        //             ->get();

        //         foreach ($nextProductions as $nextProduction) 
        //         {
        //             $nextProduction->update([
        //                 'date' => Carbon::createFromDate($nextProduction->date)->subDays($differenceDays)->format('Y-m-d')
        //             ]);
        //         }
        //     }
        // }
        // else if ($planned < $baseProduction->auto_planned)
        // {
        //     $production = $productions->sortByDesc('date')->first();
        //     $currentDay = Carbon::createFromDate($production->date);

        //     $difference = $baseProduction->auto_planned - $planned;

        //     while ($difference > 0)
        //     {
        //         $production = $this->getProduction($currentDay, $order, $product, $difference);

        //         if ($production === -1)
        //         {
        //             break;
        //         }
        //         else if ($production)
        //         {
        //             $difference -= $production->new_planned;
        //         }

        //         $currentDay = $currentDay->addDay();
        //     }
        // }
    }


    // public function createOrderProduction($baseProduction, $productCount, $productionData)
    // {
    //     dump($productCount);

    //     $productCount = ($productCount > $baseProduction->auto_planned) ? $baseProduction->auto_planned :  $productCount;

    //     $production = Production::where('date', $productionData['date'])
    //         ->where('product_id', $baseProduction->product_id)
    //         ->where('order_id', $baseProduction->order_id)
    //         ->first();


    //     if ($production)
    //     {
    //         $production->update([
    //             'performed' => $production->performed + $productCount,
    //             'manual_planned' => $production->performed + $productCount,
    //             'salary' => $production->salary + $productCount * $production->product->product_group->salary_units
    //         ]);
    //     }
    //     else
    //     {
    //         $production = Production::create([
    //             'date' => $productionData['date'],
    //             'category_id' => $baseProduction->category_id,
    //             'product_group_id' => $baseProduction->product_group_id,
    //             'product_id' => $baseProduction->product_id,
    //             'order_id' => $baseProduction->order_id,
    //             'facility_id' => $productionData['facility_id'],
    //             'performed' => $productCount,
    //             'auto_planned' => 0,
    //             'manual_planned' => $productCount,
    //             'batches' => 0,
    //             'salary' => $productCount * $baseProduction->product->product_group->salary_units
    //         ]);
    //     }

    //     $this->updateOrderPlan($baseProduction->order, $baseProduction->product, $productCount);

    //     return $production;
    // }


    protected function updateBaseProduction($order, $product, $productCount)
    {
        $baseProduction = Production::where('order_id', $order->id)
            ->where('product_id', $product->id)
            ->whereNull('date')
            ->first();

        if ($baseProduction->performed + $productCount > $baseProduction->auto_planned)
        {
            $performed = $baseProduction->auto_planned;
        }
        else
        {
            $performed = $baseProduction->performed + $productCount;
        }

        $baseProduction->update([
            'performed' => $performed
        ]);

        return $baseProduction;
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
                'batches' => 0,
                'salary' => 0
            ]);

            if ($product->free_in_stock > 0)
            {
                $baseProduction->update([
                    'performed' => ($product->free_in_stock < $productCount) ? $product->free_in_stock : $productCount
                ]);
            }
        }
        else
        {
            $baseProduction->update([
                'auto_planned' => $productCount
            ]);

            $progress = $product->getProgress($order);
            

            if ($progress['ready'] > $product->free_in_stock)
            {
                $baseProduction->update([
                    'performed' => $progress['realization'] + $product->free_in_stock
                ]);
            }
            else if ($progress['ready'] < $product->free_in_stock)
            {
                $baseProduction->update([
                    'performed' => $progress['realization'] + $progress['ready'] + $product->free_in_stock
                ]);
            }
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
            ])
            ->first();

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


    protected function getProduction($day, $order, $product, $productCount, $facilities = null)
    {
        if ((in_array($product->variation, ['', 'grey', 'yellow']) && $product->product_group->forms == 0) ||
            (!in_array($product->variation, ['', 'grey', 'yellow']) && $product->product_group->forms_add == 0))
        {
            return -1;
        }

        if (!$facilities)
        {
            $facilities = Facility::whereHas('categories', function($query) use ($product) {
                $query->where('categories.id', $product->category_id);
            })->get();
        }

        $facilitiesBatches = $this->getFacilityBatches($day, $facilities);


        $currentProductions = Production::where('date', $day->format('Y-m-d'))->get();

        $production = $currentProductions->where('order_id', $order->id)
            ->where('product_id', $product->id)
            ->first();

        if ($production)
        {
            $productCount += $production->planned;
            $oldPlanned = $production->planned;
        }


        $currentBatches = 0;
        $currentForms = [
            'forms' => 0,
            'forms_add' => 0
        ];

        foreach ($currentProductions as $currentProduction) 
        {
            if ($production && $currentProduction->id == $production->id)
            {
                continue;
            }

            $currentBatches += $currentProduction->batches;

            if ($currentProduction->product_group_id == $product->product_group_id)
            {
                if (in_array($currentProduction->product->variation, ['', 'grey', 'yellow']))
                {
                    $currentForms['forms'] += $currentProduction->planned;
                }
                else
                {
                    $currentForms['forms_add'] += $currentProduction->planned;
                }
            }
        }

        // $currentBatches = $facilitiesBatches - $currentBatches;
        $currentBatches = $facilitiesBatches;

        if ($currentBatches <= 0)
        {
            return;
        }

        if (in_array($product->variation, ['', 'grey', 'yellow']))
        {
            $currentForms = $product->product_group->forms - $currentForms['forms'];
        }
        else
        {
            $currentForms = $product->product_group->forms_add - $currentForms['forms_add'];
        }


        $maxCount = $product->product_group->units_from_batch * $currentBatches;

        if ($maxCount > $currentForms)
        {
            $maxCount = $currentForms;
        }

        // $plannedCount = $maxCount;
        $plannedCount = floor($maxCount / $product->product_group->units_from_batch) * $product->product_group->units_from_batch;

        if ($plannedCount == 0)
        {
            $plannedCount = $maxCount;
        }

        if ($plannedCount == 0)
        {
            return;
        }

        $plannedCount = ($plannedCount > $productCount) ? $productCount : $plannedCount;

        if ($production)
        {
            $production->update([
                'auto_planned' => $plannedCount,
                'batches' => $plannedCount / $product->product_group->units_from_batch
            ]);
        }
        else
        {
            $production = Production::create([
                'date' => $day->format('Y-m-d'),
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
        }

        $production->new_planned = !empty($oldPlanned) ? ($production->planned - $oldPlanned) : $production->planned;

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
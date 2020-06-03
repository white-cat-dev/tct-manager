<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Production;
use App\Realization;
use App\Product;
use App\Order;
use App\Category;
use App\Facility;
use App\Material;
use App\MaterialApply;
use App\Services\EmploymentsService;
use App\Services\ProductionsService;


class ProductionsController extends Controller
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
                $currentDay = (int)$today->day;
            }
            else
            {
                $currentDay = 0;
            }


            $categories = Category::whereHas('productions', function($query) use ($year, $month) {
                    $query->whereYear('date', $year)
                        ->whereMonth('date', $month)
                        ->where('product_id', 0)
                        ->where('order_id', 0);
                })
                ->get();

            foreach ($categories as $key => $category) 
            {
                $categories[$key]->productions = $category->productions()
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->where('product_id', 0)
                    ->where('order_id', 0)
                    ->get()
                    ->keyBy('day');
            }



            $materials = Material::whereHas('applies', function($query) use ($year, $month) {
                $query->whereYear('date', $year)
                    ->whereMonth('date', $month);
                }) 
                ->get();

            foreach ($materials as $key => $material) 
            {
                $materials[$key]->applies = $material->applies()
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->get()
                    ->keyBy('day');
            }



            $products = Product::whereHas('productions', function($query) use ($year, $month) {
                    $query->where(function ($query) use ($year, $month) 
                    {
                        $query->where(function ($query2) use ($year, $month) 
                        {
                            $query2->whereYear('date', $year)
                                ->whereMonth('date', $month);
                        })
                        ->orWhereNull('date');
                    });
                })
                ->get();

            $allOrders = collect([]);

            foreach ($products as $key => $product) 
            {
                $products[$key]->productions = $product->productions()
                    ->where(function ($query) use ($year, $month) 
                    {
                        $query->where(function ($query2) use ($year, $month) 
                        {
                            $query2->whereYear('date', $year)
                                ->whereMonth('date', $month);
                        })
                        ->orWhereNull('date');
                    })
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

            $facilities = Facility::all()->keyBy('id');
               
            return ['days' => $days,
                    'monthes' => $monthes,
                    'years' => $years,
                    'day' => $currentDay,
                    'year' => $year,
                    'month' => $month,
                    'products' => $products->sortBy('product_group.name')->values(),
                    'facilities' => $facilities,
                    'orders' => [],
                    'categories' => $categories,
                    'materials' => $materials
                ];
        }

        return view('index', ['ngTemplate' => 'productions']);
    }


    public function save(Request $request)
    {
        $today = Carbon::today();
        $month = $request->get('month', $today->month);
        $year = $request->get('year', $today->year);
        $day = $request->get('day', $today->day);

        $productsData = $request->get('products');

        foreach ($productsData as $productData) 
        {
            $productionData = $productData['production'];

            if (!empty($productionData['id']))
            {
                $production = Production::find($productionData['id']);

                $productionPerformed = $production->performed;

                // $planned = (float)$productionData['planned'];
                // if ((float)$productionData['performed'] == 0)
                // {
                //     $manualPlanned = ($planned != $production->auto_planned) ? $planned : $production->manual_planned;
                // }
                // else
                // {
                //     $manualPlanned = (float)$productionData['performed'];
                // }

                $batches = !empty($productionData['batches']) ? (float)$productionData['batches'] : 0;
                $manualPlanned = ($batches != $production->batches) ? $productionData['planned'] : $production->manual_planned;
                

                $production->update([
                    'auto_planned' => 0,
                    'manual_planned' => $manualPlanned,
                    'batches' => $batches,
                    'performed' => (float)$productionData['performed'],
                    'salary' => (float)$productionData['performed'] * $production->product->product_group->salary_units,
                    'facility_id' => $productionData['facility_id']
                ]);


                $productionPerformed = $production->performed - $productionPerformed;

                // if ($production->order)
                // {
                //     ProductionsService::getInstance()->updateOrderPlan($production->order, $production->product, $productionPerformed);
                // }
            }
            else
            {
                if (empty($productionData['planned']) && empty($productionData['performed']))
                {
                    continue;
                }

                // if (empty($productionData['order_id']))
                // {
                //     $performed = $productionData['performed'];

                //     $baseProductions = Production::where('product_id', $productionData['product_id'])
                //         ->whereNull('date')
                //         ->with('order')
                //         ->get()
                //         ->sortBy('order.priority')
                //         ->sortBy('order.date');

                    
                //     foreach ($baseProductions as $baseProduction) 
                //     {
                //         $production = ProductionsService::getInstance()->createOrderProduction($baseProduction, $performed, $productionData);

                //         $performed -= $production->performed;

                //         if ($performed == 0)
                //         {
                //             break;
                //         }
                //     }

                //     $productionPerformed = $productionData['performed'];

                //     if ($performed > 0)
                //     {
                //         $productionData['performed'] = $performed;

                //         $production = Production::create($this->getData($productionData));
                //     }
                // }
                // else
                // {
                // }
  

                $production = Production::create($this->getData($productionData));
                $productionPerformed = $production->performed;
            }

            $baseProduction = $production->product->getBaseProduction();

            if ($baseProduction)
            {
                $baseProduction->update([
                    'performed' => ($baseProduction->performed + $productionPerformed > $baseProduction->auto_planned) ? $baseProduction->auto_planned : $baseProduction->performed + $productionPerformed
                ]);
            }


            $this->updateCategoryProduction($production, $productionPerformed);

            $this->updateMaterialsApply($production, $productionPerformed);

            $production->product->update([
                'in_stock' => $production->product->in_stock + $productionPerformed
            ]);
        }

        $this->updateControlMaterialsApply($request->get('materials'));

        EmploymentsService::getInstance()->updateEmployments($year, $month, $day);
    }


    protected function getData($data)
    {
        $batches = !empty($data['batches']) ? $data['batches'] : 0;

        if ($batches > 0)
        {

        }

        return [
            'date' => !empty($data['date']) ? $data['date'] : null,
            'category_id' => !empty($data['category_id']) ? $data['category_id'] : 0,
            'product_group_id' => !empty($data['product_group_id']) ? $data['product_group_id'] : 0,
            'facility_id' => !empty($data['facility_id']) ? $data['facility_id'] : 0,
            'product_id' => !empty($data['product_id']) ? $data['product_id'] : 0,
            'order_id' => !empty($data['order_id']) ? $data['order_id'] : 0,
            'auto_planned' => 0,
            'manual_planned' => !empty($data['planned']) ? $data['planned'] : 0,
            'performed' => !empty($data['performed']) ? $data['performed'] : 0,
            'batches' => !empty($data['batches']) ? $data['batches'] : 0,
            'salary' => !empty($data['salary']) ? $data['salary'] : 0
        ];
    }


    protected function updateCategoryProduction($production, $performed)
    {
        $categoryProduction = Production::where('order_id', 0)
            ->where('product_id', 0)
            ->where('category_id', $production->category_id)
            ->where('date', $production->date)
            ->first();

        if ($categoryProduction)
        {
            $categoryProduction->update([
                'performed' => $categoryProduction->performed + $performed,
                'salary' => $categoryProduction->salary + $performed * $production->product->product_group->salary_units
            ]);
        }
        else
        {
            $categoryProduction = Production::create([
                'date' => $production->date,
                'category_id' => $production->category_id,
                'product_group_id' => 0,
                'product_id' => 0,
                'order_id' => 0,
                'facility_id' => 0,
                'auto_planned' => 0,
                'manual_planned' => 0,
                'performed' => $performed,
                'batches' => 0,
                'salary' => $performed * $production->product->product_group->salary_units
            ]);
        }
    }


    protected function updateMaterialsApply($production, $performed)
    {
        $recipe = $production->product_group->recipe;

        if (!$recipe)
        {
            return;
        }

        foreach ($recipe->material_groups as $materialGroup) 
        {
            if ($materialGroup->variations)
            {
                $material = $materialGroup->materials->where('variation', $production->product->variation)->first();
            }
            else
            {
                $material = $materialGroup->materials->first();
            }

            if (!$material)
            {
                continue;
            }

            $planned = $materialGroup->pivot->count * $performed;

            $materialAppy = $material->applies()->where(['date' => $production->date])->first();

            if ($materialAppy)
            {
                $materialAppy->update([
                    'planned' => $materialAppy->planned + $planned
                ]);
            }
            else
            {
                $materialAppy = $material->applies()->create([
                    'date' => $production->date,
                    'performed' => 0,
                    'planned' => $planned
                ]);
            }

            if (!$materialGroup->control)
            {
                $materialAppy->update([
                    'performed' => $materialAppy->performed + $planned
                ]);

                $material->update([
                    'in_stock' => $material->in_stock - $planned
                ]);
            }
        }
    }


    protected function updateControlMaterialsApply($materialsData)
    {
        foreach ($materialsData as $materialData) 
        {
            $materialAppyData = $materialData['apply'];

            if (!empty($materialAppyData['id']))
            {
                $materialAppy = MaterialApply::find($materialAppyData['id']);

                if ($materialAppy->material->material_group->control)
                {
                    $appyPerformed = $materialAppy->performed;

                    $materialAppy->update([
                        'performed' => $materialAppyData['performed']
                    ]);

                    $appyPerformed = $materialAppy->performed - $appyPerformed;

                    $materialAppy->material->update([
                        'in_stock' => $materialAppy->material->in_stock - $appyPerformed
                    ]);
                }
            }
        }
    }
}
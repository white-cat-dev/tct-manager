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
use App\MaterialGroup;
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
                    'products' => $products->sortBy(function($item) { return $item->product_group->name . $item->id; })->values(),
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

                $production->update([
                    'manual_planned' => (float)$productionData['manual_planned'],
                    'manual_batches' => (float)$productionData['manual_batches'],
                    'performed' => (float)$productionData['performed'],
                    'salary' => (float)$productionData['performed'] * $production->product->product_group->salary_units,
                    'facility_id' => $productionData['facility_id']
                ]);

                $productionPerformed = $production->performed - $productionPerformed;
            }
            else
            {
                if (empty($productionData['manual_planned']) && empty($productionData['performed']))
                {
                    continue;
                }
  
                $production = Production::create($this->getData($productionData));
                $production->update([
                    'salary' => $production->performed * $production->product->product_group->salary_units
                ]);
                $productionPerformed = $production->performed;
            }


            $production->product->update([
                'in_stock' => $production->product->in_stock + $productionPerformed
            ]);

            $baseProduction = $production->product->getBaseProduction();

            if ($baseProduction)
            {
                $baseProduction->update([
                    'performed' => ($baseProduction->product->in_stock > $baseProduction->auto_planned) ? $baseProduction->auto_planned : $baseProduction->product->in_stock
                ]);

                // ProductionsService::getInstance()->replanProduct($production->product);
            }

            $this->updateMaterialsApply($production, $productionPerformed);
        }


        $this->updateControlMaterialsApply($request->get('materials'));

        if (!empty($production))
        {
            $this->updateCategoryProductions($production->date);
        }

        EmploymentsService::getInstance()->updateEmployments($year, $month, $day);
    }


    public function replan(Request $request)
    {
        ProductionsService::getInstance()->replan();
    }


    protected function getData($data)
    {
        return [
            'date' => !empty($data['date']) ? $data['date'] : null,
            'category_id' => !empty($data['category_id']) ? $data['category_id'] : 0,
            'product_group_id' => !empty($data['product_group_id']) ? $data['product_group_id'] : 0,
            'facility_id' => !empty($data['facility_id']) ? $data['facility_id'] : 0,
            'product_id' => !empty($data['product_id']) ? $data['product_id'] : 0,
            'order_id' => 0,
            'auto_planned' => 0,
            'manual_planned' => !empty($data['manual_planned']) ? $data['manual_planned'] : -1,
            'priority' => Order::PRIORITY_NORMAL,
            'date_to' => null,
            'performed' => !empty($data['performed']) ? $data['performed'] : 0,
            'auto_batches' => 0,
            'manual_batches' => !empty($data['manual_batches']) ? $data['manual_batches'] : -1,
            'salary' => !empty($data['salary']) ? $data['salary'] : 0
        ];
    }


    protected function updateCategoryProductions($date)
    {
        $categoryProductions = Production::where('product_id', 0)
            ->where('date', $date)
            ->get();

        $productions = Production::where('product_id', '!=', 0)
            ->where('date', $date)
            ->get();

        $categorySalaries = [];

        foreach ($productions as $production) 
        {
            if (!empty($categorySalaries[$production->category_id]))
            {
                $categorySalaries[$production->category_id]['performed'] += $production->performed;
                $categorySalaries[$production->category_id]['salary'] += $production->salary;
            }
            else
            {
                $categorySalaries[$production->category_id] = [
                    'performed' => $production->performed,
                    'salary' => $production->salary
                ];
            }
        }

        foreach ($categorySalaries as $categoryId => $categorySalary) 
        {
            $categoryProduction = $categoryProductions->where('category_id', $categoryId)->first();

            if ($categoryProduction)
            {
                $categoryProduction->update([
                    'performed' => $categorySalary['performed'],
                    'salary' => $categorySalary['salary']
                ]);
            }
            else
            {
                $categoryProduction = Production::create([
                    'date' => $date,
                    'category_id' => $categoryId,
                    'product_group_id' => 0,
                    'product_id' => 0,
                    'order_id' => 0,
                    'facility_id' => 0,
                    'auto_planned' => 0,
                    'manual_planned' => -1,
                    'date_to' => null,
                    'priority' => Order::PRIORITY_NORMAL,
                    'performed' => $categorySalary['performed'],
                    'auto_batches' => 0,
                    'manual_batches' => -1,
                    'salary' => $categorySalary['salary']
                ]);
            }
        }
    }


    protected function updateMaterialsApply($production, $performed)
    {
        $material = MaterialGroup::where('name', 'Цемент насыпь')->first()->materials()->first();
        $materialAppy = $material->applies()->where(['date' => $production->date])->first();
        if (!$materialAppy)
        {
            $materialAppy = $material->applies()->create([
                'date' => $production->date,
                'performed' => 0,
                'planned' => 0
            ]);
        }


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
            $materialApplyData = $materialData['apply'];

            if (!empty($materialApplyData['id']))
            {
                $materialAppy = MaterialApply::find($materialApplyData['id']);

                if ($materialAppy->material->material_group->control)
                {
                    $appyPerformed = $materialAppy->performed;

                    $materialAppy->update([
                        'performed' => $materialApplyData['performed']
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
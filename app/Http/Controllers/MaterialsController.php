<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MaterialGroup;
use App\Material;
use App\MaterialSupply;
use App\Stock;
use Carbon\Carbon;
use App\Services\DateService;


class MaterialsController extends Controller
{
    public function index(Request $request) 
    {
        if ($request->wantsJson())
        {
            $materialGroups = MaterialGroup::with('materials')->orderBy('name')->get();
            return $materialGroups;
        }

        return view('index', ['ngTemplate' => 'materials']);
    }


    public function show(Request $request, MaterialGroup $materialGroup) 
    {
        if ($request->wantsJson())
        {
            $materialGroup->materials = $materialGroup->materials;
            return $materialGroup;
        }

        return view('index', ['ngTemplate' => 'materials.show']);
    }


    public function create(Request $request) 
    {
        if ($request->wantsJson())
        {
            $this->validate($request, $this->validationRules);

            $materialGroupData = $this->getData($request);
            $materialGroup = MaterialGroup::create($materialGroupData);

            foreach ($request->get('materials', []) as $materialData) 
            {
                $materialData['material_group_id'] = $materialGroup->id;

                if (!$materialGroup->variations)
                {
                    $materialData['variation'] = '';
                }
                $material = $materialGroup->materials()->create($materialData);

                $materialStock = $material->stocks()->create([
                    'date' => date('Y-m-d'),
                    'process_id' => 0,
                    'process_type' => '',
                    'in_stock' => $material->in_stock,
                    'new_in_stock' => $material->in_stock,
                    'reason' => 'create'
                ]);
            }

            $materialGroup->materials = $materialGroup->materials;

            return $materialGroup;
        }

        return view('index', ['ngTemplate' => 'materials.edit']);
    }


    public function edit(Request $request, MaterialGroup $materialGroup) 
    {
        if ($request->wantsJson())
        {
            $this->validate($request, $this->validationRules);

            $materialGroupData = $this->getData($request);
            $materialGroup->update($materialGroupData);

            $materialsIds = $materialGroup->materials()->select('id')->pluck('id', 'id');

            foreach ($request->get('materials', []) as $materialData) 
            {
                $materialData['material_group_id'] = $materialGroup->id;

                if (!$materialGroup->variations)
                {
                    $materialData['variation'] = '';
                }

                $id = !empty($materialData['id']) ? $materialData['id'] : 0;

                $materialsIds->forget($id);

                $material = $materialGroup->materials()->find($id);

                $materialData = $this->getMaterialData($materialData, $materialGroup);

                if (!$material) 
                {
                    $material = $materialGroup->materials()->create($materialData);

                    $materialStock = $material->stocks()->create([
                        'date' => date('Y-m-d'),
                        'process_id' => 0,
                        'process_type' => '',
                        'in_stock' => $material->in_stock,
                        'new_in_stock' => $material->in_stock,
                        'reason' => 'create'
                    ]);
                }
                else 
                {
                    $material->updateInStock($materialData['in_stock'], 'manual');
                    $material->update($materialData);
                }
            }

            // $materialGroup->materials()->detach($materialsIds);

            return $materialGroup;
        }

        return view('index', ['ngTemplate' => 'materials.edit']);
    }


    public function delete(Request $request, MaterialGroup $materialGroup)
    {
        if ($request->wantsJson())
        {
            $materialGroup->delete();
        }
    }


    public function copy(Request $request, MaterialGroup $materialGroup)
    {
        $materialGroupCopy = $materialGroup->replicate()->fill([
            'name' => $materialGroup->name . ' (копия)'
        ]);
        $materialGroupCopy->save();

        foreach ($materialGroup->materials as $material) 
        {
            $materialCopy = $material->replicate()->fill([
                'material_group_id' => $materialGroupCopy->id
            ]);
            $materialCopy->save();

            $materialStock = $materialCopy->stocks()->create([
                'date' => date('Y-m-d'),
                'process_id' => 0,
                'process_type' => '',
                'in_stock' => $materialCopy->in_stock,
                'new_in_stock' => $materialCopy->in_stock,
                'reason' => 'create'
            ]);
        }

        return $materialGroupCopy;
    }


    public function supplies(Request $request, MaterialGroup $materialGroup)
    {
        if ($request->wantsJson())
        {
            $today = Carbon::today();

            $month = (int)$request->get('month', $today->month);
            $year = (int)$request->get('year', $today->year);

            $monthes = DateService::getMonthes();
            $years = DateService::getYears(MaterialSupply::select('date'));

            $materialIds = $materialGroup->materials()->select('id')->pluck('id');

            $supplies = MaterialSupply::whereIn('material_id', $materialIds)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->with('material')
                ->get();

            return ['monthes' => $monthes,
                'years' => $years,
                'year' => $year,
                'month' => $month,
                'supplies' => $supplies
            ];
        }
    }


    public function stocks(Request $request, Material $material)
    {
        if ($request->wantsJson())
        {
            $today = Carbon::today();

            $month = (int)$request->get('month', $today->month);
            $year = (int)$request->get('year', $today->year);

            $monthes = DateService::getMonthes();
            $years = DateService::getYears(Stock::select('date'));

            $stocks = $material->stocks()
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();

            return ['monthes' => $monthes,
                'years' => $years,
                'year' => $year,
                'month' => $month,
                'stocks' => $stocks
            ];
        }
    }


    public function saveSupply(Request $request)
    {
        if ($request->wantsJson())
        {
            $suppliesData = $request->get('supplies');

            foreach ($suppliesData as $supplyData) 
            {
                if (!empty($supplyData['id']))
                {
                    $supply = MaterialSupply::find($supplyData['id']);
                    $supplyPerformed = $supply->performed;
                    $supply->update($this->getSupplyData($supplyData));
                    $supplyPerformed = $supply->performed - $supplyPerformed;
                }
                else
                {
                    $supply = MaterialSupply::create($this->getSupplyData($supplyData));
                    $supplyPerformed = $supply->performed;
                }

                $material = $supply->material;
                $material->updateInStock($material->in_stock + $supplyPerformed, 'material_supply', $supply);
            }
        }
    }


    protected $validationRules = [
        'name' => 'required',
        'units' => 'required'
    ];


    protected function getData(Request $request)
    {
        $variations = $request->get('variations', '');
        if ($variations === null)
        {
            $variations = '';
        }
        return [
            'name' => $request->get('name', ''),
            'variations' => $variations,
            'units' => $request->get('units', ''),
            'control' => $request->get('control', 0),
        ];
    }


    protected function getMaterialData($data, $materialGroup)
    {
        return [
            'variation' => !empty($data['variation']) ? $data['variation'] : '',
            'price' => !empty($data['price']) ? $data['price'] : 0,
            'in_stock' => !empty($data['in_stock']) ? $data['in_stock'] : 0
        ];
    }


    protected function getSupplyData($data)
    {
        $date = !empty($data['date_raw']) ? $data['date_raw'] : date('dmY');
        $date = Carbon::createFromFormat('dmY', $date)->format('Y-m-d');

        return [
            'date' => $date,
            'material_id' => !empty($data['material_id']) ? $data['material_id'] : 0,
            'performed' => !empty($data['performed']) ? $data['performed'] : 0
        ];
    }
}
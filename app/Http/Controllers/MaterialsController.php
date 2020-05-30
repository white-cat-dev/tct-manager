<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MaterialGroup;
use App\Material;
use App\MaterialSupply;
use Carbon\Carbon;


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
                }
                else 
                {
                    if ($material->in_stock != $materialData['in_stock'])
                    {
                        $materialStock = $material->stocks()->where('date', date('Y-m-d'))->first();
                        if ($materialStock)
                        {
                            $materialStock->update([
                                'new_in_stock' => $materialData['in_stock']
                            ]);
                        }
                    }
                    $material->update($materialData);
                }
            }

            $materialGroup->materials()->whereIn('materials.id', $materialsIds)->delete();

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


    public function saveSupply(Request $request)
    {
        $suppliesData = $request->get('supplies');

        foreach ($suppliesData as $supplyData) 
        {
            $supply = MaterialSupply::create($this->getSupplyData($supplyData));

            $supply->material->update([
                'in_stock' => $supply->material->in_stock + $supply->performed
            ]);
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
        $date = !empty($data['date']) ? $data['date'] : date('dmY');
        $date = Carbon::createFromFormat('dmY', $date)->format('Y-m-d');

        return [
            'date' => $date,
            'material_id' => !empty($data['material_id']) ? $data['material_id'] : 0,
            'performed' => !empty($data['performed']) ? $data['performed'] : 0
        ];
    }
}
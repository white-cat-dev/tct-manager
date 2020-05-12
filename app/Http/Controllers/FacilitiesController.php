<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Facility;
use App\Worker;
use Arr;


class FacilitiesController extends Controller
{
    public function index(Request $request) 
    {
        if ($request->wantsJson())
        {
            $facilities = Facility::orderBy('status', 'desc')->orderBy('status_date')->get();
            return $facilities;
        }

        return view('index', ['ngTemplate' => 'facilities']);
    }


    public function show(Request $request, Facility $facility) 
    {
        if ($request->wantsJson())
        {            
            return $facility;
        }

        return view('index', ['ngTemplate' => 'facilities.show']);
    }


    public function create(Request $request) 
    {
        if ($request->wantsJson())
        {
            $this->validate($request, $this->validationRules);

            $facility = Facility::create($this->getData($request));


            foreach ($request->get('categories') as $categoryId => $categoryData) 
            {
                if ($categoryData) 
                {
                    $facility->categories()->attach($categoryId);
                }
            }


            foreach ($request->get('workers') as $workerData) 
            {
                $worker = Worker::find($workerData['id']);

                if ($worker) 
                {
                    if ($worker->facility_id != $facility->id)
                    {
                        $worker->update([
                            'facility_id' => $facility->id
                        ]);
                    }
                }
            }
            

            return $facility;
        }

        return view('index', ['ngTemplate' => 'facilities.edit']);
    }


    public function edit(Request $request, Facility $facility) 
    {
        if ($request->wantsJson())
        {
            $this->validate($request, $this->validationRules);

            $facilityData = $this->getData($request);

            $facility->update($facilityData);

            $categoriesIds = [];

            foreach ($request->get('categories') as $categoryId => $categoryData) 
            {
                if ($categoryData) 
                {
                    $id = !empty($categoryData['id']) ? $categoryData['id'] : $categoryId;

                    $category = $facility->categories->find($id);
                    if (!$category) 
                    {
                        $facility->categories()->attach($id);
                    }

                    $categoriesIds[] = $id;
                }
            }

            $facility->categories()->wherePivotNotIn('category_id', $categoriesIds)->detach();



            return $facility;
        }

        return view('index', ['ngTemplate' => 'facilities.edit']);
    }


    public function delete(Request $request, Facility $facility)
    {
        if ($request->wantsJson())
        {
            $facility->delete();
        }
    }


    protected $validationRules = [
        'name' => 'required',
        'performance' => 'required',
        'status' => 'required'
    ];


    protected function getData(Request $request)
    {
        $data = [
            'name' => $request->get('name', ''),
            'performance' => $request->get('performance', 0),
            'status' => $request->get('status', Facility::STATUS_ACTIVE),
            'status_date' => $request->get('status_date_raw', -1),
            'icon_color' => $request->get('icon_color', '#000000')
        ];


        if ($data['status_date'] == -1)
        {
            $data['status_date'] = $request->get('status_date', null); 
        }
        else
        {
            $data['status_date'] = $data['status_date'] ? substr($data['status_date'], 0, 10) : null;
        }

        if (($data['status_date']) && ($data['status_date'] <= date('Y-m-d')))
        {
            $data['status_date'] = null;
            $data['status'] = ($data['status'] + 1) % 2;
        }

        return $data;
    }
}
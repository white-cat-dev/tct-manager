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
            // $facilities = Facility::orderBy('status', 'desc')->orderBy('status_date')->get();
            $facilities = Facility::all();
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
        $statusDate = $request->get('status_date_raw', -1);
        if ($statusDate == -1)
        {
            $statusDate = $request->get('status_date', null); 
        }
        else
        {
            $statusDate = $statusDate ? substr($statusDate, 0, 10) : null;
        }

        $status = $request->get('status', Worker::STATUS_ACTIVE);
        if ($statusDate && ($statusDate <= date('Y-m-d')))
        {
            $statusDate = null;
            $status = ($status + 1) % 2;
        }

        return [
            'name' => $request->get('name', ''),
            'performance' => $request->get('performance', 0),
            'status' => $status,
            'status_date' => $statusDate,
            'icon_color' => $request->get('icon_color', '#000000')
        ];
    }
}
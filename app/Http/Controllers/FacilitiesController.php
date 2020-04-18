<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Facility;
use App\Worker;


class FacilitiesController extends Controller
{
    public function index(Request $request) 
    {
        if ($request->wantsJson())
        {
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

            $facility->update($this->getData($request));


            $categoriesIds = [];

            foreach ($request->get('categories') as $categoryId => $categoryData) 
            {
                if ($categoryData) 
                {
                    $category = $facility->categories->find($categoryId);
                    if (!$category) 
                    {
                        $facility->categories()->attach($categoryId);
                    }

                    $categoriesIds[] = $categoryId;
                }
            }

            $facility->categories()->wherePivotNotIn('category_id', $categoriesIds)->detach();


            $workersIds = [];

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

                    $workersIds[] = $worker->id;
                }
            }

            $facility->workers()->whereNotIn('id', $workersIds)->update([
                'facility_id' => 0
            ]);


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
        'performance' => 'required'
    ];


    protected function getData(Request $request)
    {
        return [
            'name' => $request->get('name', ''),
            'performance' => $request->get('performance', 0),
            'status' => $request->get('status', 'active')
        ];
    }
}
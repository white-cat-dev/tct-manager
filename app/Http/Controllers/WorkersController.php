<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Worker;
use App\Employment;
use App\EmploymentStatus;


class WorkersController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson())
        {
            $workers = Worker::with('facility')->get();
            return $workers;
        }

        return view('index', ['ngTemplate' => 'workers']);
    }


    public function show(Request $request, Worker $worker) 
    {
        if ($request->wantsJson())
        {
            return $worker;
        }

        return view('index', ['ngTemplate' => 'workers.show']);
    }


    public function create(Request $request) 
    {
        if ($request->wantsJson())
        {
            $this->validate($request, $this->validationRules);

            $worker = Worker::create($this->getData($request));
            
            return $worker;
        }

        return view('index', ['ngTemplate' => 'workers.edit']);
    }


    public function edit(Request $request, Worker $worker) 
    {
        if ($request->wantsJson())
        {
            $this->validate($request, $this->validationRules);

            $worker->update($this->getData($request));

            return $worker;
        }

        return view('index', ['ngTemplate' => 'workers.edit']);
    }


    public function delete(Request $request, Worker $worker)
    {
        if ($request->wantsJson())
        {
            $worker->delete();
        }
    }


    protected $validationRules = [
        'name' => 'required',
        'surname' => 'required',
        'full_name' => 'required',
        'patronymic' => 'required',
    ];


    protected function getData(Request $request)
    {
        return [
            'name' => $request->get('name', ''),
            'surname' => $request->get('surname', ''),
            'full_name' => $request->get('full_name', ''),
            'patronymic' => $request->get('patronymic', ''),
            'status' => $request->get('status', 'active'),
            'facility_id' => $request->get('facility_id', 0)
        ];
    }
}
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
            $workers = Worker::orderBy('status', 'desc')->orderBy('status_date')->get();
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
        $data = [
            'name' => $request->get('name', ''),
            'surname' => $request->get('surname', ''),
            'full_name' => $request->get('full_name', ''),
            'patronymic' => $request->get('patronymic', ''),
            'phone' => $request->get('phone', ''),
            'status' => $request->get('status', Worker::STATUS_ACTIVE),
            'status_date' => $request->get('status_date_raw', -1),
            'status_date_next' => $request->get('status_date_next_raw', -1)
        ];

        if ($data['status_date'] == -1)
        {
            $data['status_date'] = $request->get('status_date', null); 
        }
        else
        {
            $data['status_date'] = $data['status_date'] ? substr($data['status_date'], 0, 10) : null;
        }

        if ($data['status_date_next'] == -1)
        {
            $data['status_date_next'] = $request->get('status_date_next', null); 
        }
        else
        {
            $data['status_date_next'] = $data['status_date_next'] ? substr($data['status_date_next'], 0, 10) : null;
        }

        if (($data['status_date']) && ($data['status_date'] <= date('Y-m-d')))
        {
            $data['status_date'] = $data['status_date_next'];
            $data['status_date_next'] = null;
            $data['status'] = ($data['status'] + 1) % 2;
        }

        return $data;
    }
}
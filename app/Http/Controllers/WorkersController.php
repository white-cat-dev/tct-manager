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
            // $workers = Worker::orderBy('status', 'desc')->orderBy('status_date')->get();
            $workers = Worker::all();
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
        'name' => 'required'
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

        $birthdate = $request->get('birthdate_raw', -1); 
        if ($birthdate == -1)
        {
            $birthdate = $request->get('birthdate', null); 
        }
        else
        {
            $birthdate = $birthdate ? Carbon::createFromFormat('dmY', $birthdate)->format('Y-m-d') : null;
        }

        return [
            'name' => $request->get('name', ''),
            'surname' => $request->get('surname', ''),
            'full_name' => $request->get('full_name', ''),
            'patronymic' => $request->get('patronymic', ''),
            'phone' => $request->get('phone', ''),
            'passport' => $request->get('passport', ''),
            'birthdate' => $birthdate,
            'status' => $status,
            'status_date' => $statusDate
        ];
    }
}
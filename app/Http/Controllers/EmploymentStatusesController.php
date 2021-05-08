<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EmploymentStatus;


class EmploymentStatusesController extends Controller
{
    protected $validationRules = [
        'name' => 'required',
        'icon' => 'required',
        'icon_color' => 'required',
        'type' => 'required'
    ];


    public function index(Request $request)
    {
        if ($request->wantsJson())
        {
            $statuses = EmploymentStatus::all();

            return $statuses;
        }

        return view('index', ['ngTemplate' => 'employments.statuses']);
    }


    public function save(Request $request)
    {
        if ($request->wantsJson())
        {
            $statusesData = $request->get('statuses');

            $statusesIds = [];

            foreach ($statusesData as $statusData) 
            {
                if (!empty($statusData['id']))
                {
                    $status = EmploymentStatus::find($statusData['id']);

                    if ($status)
                    {
                        $status->update($this->getData($statusData));

                        $statusesIds[] = $status->id;
                    }
                }
                else
                {
                    $status = EmploymentStatus::create($this->getData($statusData));

                    $statusesIds[] = $status->id;
                }
            }

            EmploymentStatus::whereNotIn('id', $statusesIds)->delete();
        }
    }


    protected function getData($data)
    {
        return [
            'icon' => !empty($data['icon']) ? $data['icon'] : 0,
            'icon_color' => !empty($data['icon_color']) ? $data['icon_color'] : '#000000',
            'name' => !empty($data['name']) ? $data['name'] : '',
            'type' => !empty($data['type']) ? $data['type'] : 'fixed',
            'base_salary' => !empty($data['base_salary']) ? $data['base_salary'] : 0,
            'salary' => !empty($data['salary']) ? $data['salary'] : 0,
            'default_salary' => !empty($data['default_salary']) ? $data['default_salary'] : 0,
            'customable' => !empty($data['customable']) ? $data['customable'] : false
        ];
    }
}
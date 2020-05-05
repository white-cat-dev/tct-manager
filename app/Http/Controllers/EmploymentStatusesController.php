<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EmploymentStatus;


class EmploymentStatusesController extends Controller
{
    protected $validationRules = [
        'name' => 'required',
        'icon' => 'required',
        'icon_color' => 'required'
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
                        $status->update($statusData);

                        $statusesIds[] = $status->id;
                    }
                }
                else
                {
                    $status = EmploymentStatus::create($statusData);

                    $statusesIds[] = $status->id;
                }
            }

            EmploymentStatus::whereNotIn('id', $statusesIds)->delete();
        }
    }
}
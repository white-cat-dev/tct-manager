<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Worker;
use App\WorkerSalary;
use App\Facility;
use App\Employment;
use App\EmploymentStatus;
use App\Services\EmploymentsService;


class EmploymentsController extends Controller
{
    protected $validationRules = [
        'name' => 'required'
    ];

    public function index(Request $request)
    {
        if ($request->wantsJson())
        {
            $today = Carbon::today();

            $month = (int)$request->get('month', $today->month);
            $year = (int)$request->get('year', $today->year);

            if (($month == $today->month) && ($year ==$today->year))
            {
                $day = (int)$today->day;
            }
            else
            {
                $day = 0;
            }

            $workers = Worker::where('status', Worker::STATUS_ACTIVE)->get();

            foreach ($workers as $key => $worker) 
            {
                $workers[$key]->employments = $workers[$key]->employments()
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->get()
                    ->keyBy('day');

                $workers[$key]->salary = $workers[$key]->salaries()
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->first();

                if (!$workers[$key]->salary)
                {
                    $workers[$key]->salary = WorkerSalary::create([
                        'date' => Carbon::createFromDate($year, $month, 1)->format('Y-m-d'),
                        'worker_id' => $worker->id,
                        'employments' => 0,
                        'advance' => 0,
                        'bonus' => 0
                    ]);
                }
            }


            $days = Carbon::createFromDate($year, $month, 1)->daysInMonth;

            $monthes = [
                ['id' => 1, 'name' => 'Январь'],
                ['id' => 2, 'name' => 'Февраль'],
                ['id' => 3, 'name' => 'Март'],
                ['id' => 4, 'name' => 'Апрель'],
                ['id' => 5, 'name' => 'Май'],
                ['id' => 6, 'name' => 'Июнь'],
                ['id' => 7, 'name' => 'Июль'],
                ['id' => 8, 'name' => 'Август'],
                ['id' => 9, 'name' => 'Сентябрь'],
                ['id' => 10, 'name' => 'Октябрь'],
                ['id' => 11, 'name' => 'Ноябрь'],
                ['id' => 12, 'name' => 'Декабрь']
            ];

            $years = Employment::select('date')->groupBy('date')->get()->groupBy(function($date) {
                return Carbon::parse($date->created_at)->format('Y');
            })->keys();

            $years[] = $today->year - 1;
            $years[] = $today->year;
            $years[] = $today->year + 1;

            $years = $years->unique()->sort()->values();


            $statuses = EmploymentStatus::all()->keyBy('id');

            $facilities = Facility::all()->keyBy('id');
               
            return ['days' => $days,
                    'monthes' => $monthes,
                    'years' => $years,
                    'day' => (int)$day,
                    'year' => (int)$year,
                    'month' => (int)$month,
                    'workers' => $workers,
                    'statuses' => $statuses,
                    'facilities' => $facilities
                ];
        }

        return view('index', ['ngTemplate' => 'workers']);
    }


    public function save(Request $request)
    {
        $employmentsGroups = collect($request->get('employments'))->groupBy('worker_id');

        $today = Carbon::today();
        $month = $request->get('month', $today->month);
        $year = $request->get('year', $today->year);

        $statuses = EmploymentStatus::all()->keyBy('id');

        foreach ($employmentsGroups as $workerId => $employmentsGroup) 
        {
            foreach ($employmentsGroup as $employmentData) 
            {
                $date = Carbon::createFromDate($year, $month, $employmentData['day'])->format('Y-m-d');

                $employment = Employment::where('worker_id', $employmentData['worker_id'])
                    ->where('date', $date)
                    ->first();

                if ($employment)
                {
                    $employment->update([
                        'status_id' => $employmentData['status_id'],
                        'facility_id' => $employmentData['facility_id']
                    ]); 
                }
                else
                {
                    $employment = Employment::create([
                        'date' => $date,
                        'worker_id' => $employmentData['worker_id'],
                        'status_id' => $employmentData['status_id'],
                        'facility_id' =>  $employmentData['facility_id'],
                        'salary' => 0
                    ]);
                }
            }
        }

        EmploymentsService::getInstance()->updateEmployments($year, $month);
    }


    public function saveSalary(Request $request, WorkerSalary $salary)
    {
        $salary->update($request->all());
    }
}
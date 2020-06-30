<?php

namespace App\Services;

use DB;
use Carbon\Carbon;
use App\Employment;
use App\Production;
use App\Worker;
use App\WorkerSalary;


class EmploymentsService
{
    protected static $instance;

    public static function getInstance() 
    {
        if (static::$instance === null) 
        {
            static::$instance = new static;  
        }
 
        return static::$instance;
    }


    public function updateEmployments($year, $month, $day = null)
    {
        $employmentsTotal = $this->getEmploymentsTotal($year, $month, $day);

        $employmentsDays = Employment::whereYear('date', $year)
            ->whereMonth('date', $month);

        if ($day)
        {
            $employmentsDays->whereDay('date', $day);
        }

        $employmentsDays = $employmentsDays->get()->groupBy('date');

        foreach ($employmentsDays as $date => $employments) 
        {
            $day = Carbon::createFromDate($date)->day;

            if (!empty($managerEmployment))
            {
                $statusProduction = $managerEmployment->status->customable ? $managerEmployment->status_custom : $managerEmployment->status->salary_production;

                $managerEmployment->update([
                    'salary' => ceil(200 * $statusProduction)
                ]);
            }

            foreach ($employments as $employment) 
            {
                $mainCategory = $employment->main_category;
                $statusProduction = $employment->status->customable ? $employment->status_custom : $employment->status->salary_production;

                if ($employment->worker_id == 0)
                {
                    $employment->update([
                        'salary' => ceil(200 * $statusProduction)
                    ]);
                    continue;
                }
                
                $salary = 0;
                if (!empty($employmentsTotal[$day][$mainCategory]))
                {
                    $salary = $employmentsTotal[$day][$mainCategory]['person_salary'] * $statusProduction;
                }
                $salary += $employment->status->salary_fixed;

                $employment->update([
                    'salary' => ceil($salary)
                ]);
            }
        }

        $workers = Worker::whereHas('employments', function($query) use ($year, $month) {
                $query->whereYear('date', $year)
                    ->whereMonth('date', $month);
            })
            ->get();

        foreach ($workers as $worker) 
        {
            $employments = $worker->employments()
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();

            $employmentsSalary = 0;

            foreach ($employments as $employment) 
            {
                $employmentsSalary += $employment->salary;
            }

            $workerSalary = $worker->salaries()
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->first();

            if (!$workerSalary)
            {
                $workerSalary = WorkerSalary::create([
                    'date' => Carbon::createFromDate($year, $month, 1)->format('Y-m-d'),
                    'worker_id' => $worker->id,
                    'employments' => $employmentsSalary,
                    'advance' => 0,
                    'bonus' => 0,
                    'tax' => 0,
                    'lunch' => 0,
                    'surcharge' => 0
                ]);
            }
            else 
            {
                $workerSalary->update([
                    'employments' => $employmentsSalary
                ]);
            }
        }
    }


    public function getEmploymentsTotal($year, $month, $day = null)
    {
        $categoryProductions = Production::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('product_id', 0)
            ->where('order_id', 0);

        $employmentsDays = Employment::whereYear('date', $year)
            ->whereMonth('date', $month);

        if ($day)
        {
            $categoryProductions->whereDay('date', $day);

            $employmentsDays->whereDay('date', $day);
        }

        $categoryProductions = $categoryProductions->get();

        $employmentsDays = $employmentsDays->get()->groupBy('date');


        $employmentsTotal = [];

        $newEmploymentTotal = [
            'salary' => 0,
            'manager' => 0,
            'final_salary' => 0,
            'team' => 0,
            'person_salary' => 0
        ];

        foreach ($categoryProductions as $categoryProduction) 
        {
            $mainCategory = $categoryProduction->category->main_category;
            $day = $categoryProduction->day;

            if (empty($employmentsTotal[$day])) 
            {
                $employmentsTotal[$day] = [
                    $mainCategory => $newEmploymentTotal
                ];
            }
            else if (empty($employmentsTotal[$day][$mainCategory]))
            {
                $employmentsTotal[$day][$mainCategory] = $newEmploymentTotal;
            }

            $employmentsTotal[$day][$mainCategory]['salary'] += $categoryProduction->salary;
        }


        foreach ($employmentsDays as $date => $employments) 
        {
            $managerEmployment = null;

            $day = Carbon::createFromDate($date)->day;

            foreach ($employments as $employment) 
            {
                if ($employment->worker_id == 0)
                {
                    $managerEmployment = $employment;
                    continue;
                }

                $statusProduction = $employment->status->customable ? $employment->status_custom : $employment->status->salary_production;
                $mainCategory = $employment->main_category;

                if (!$mainCategory)
                {
                    continue;
                }

                if (empty($employmentsTotal[$day])) 
                {
                    $employmentsTotal[$day] = [
                        $mainCategory => $newEmploymentTotal
                    ];
                }
                else if (empty($employmentsTotal[$day][$mainCategory]))
                {
                    $employmentsTotal[$day][$mainCategory] = $newEmploymentTotal;
                }

                $employmentsTotal[$day][$mainCategory]['team'] += $statusProduction;
            }

            if (!empty($managerEmployment))
            {
                $statusProduction = $managerEmployment->status->customable ? $managerEmployment->status_custom : $managerEmployment->status->salary_production;
                if ($employmentsTotal[$day]['tiles']) 
                {
                    $employmentsTotal[$day]['tiles']['manager'] = 50 * $statusProduction;
                }
            }

            if (!empty($employmentsTotal[$day]))
            {
                foreach ($employmentsTotal[$day] as $category => $employmentTotal) 
                {
                    $employmentsTotal[$day][$category]['salary'] = ceil($employmentsTotal[$day][$category]['salary']);

                    $employmentsTotal[$day][$category]['final_salary'] = $employmentsTotal[$day][$category]['salary'] - $employmentsTotal[$day][$category]['manager'];
                    if ($employmentsTotal[$day][$category]['team'] > 0)
                    {
                        $employmentsTotal[$day][$category]['person_salary'] = ceil($employmentsTotal[$day][$category]['final_salary'] / $employmentsTotal[$day][$category]['team']);
                    }
                }
            }
        }

        return $employmentsTotal;
    }
}
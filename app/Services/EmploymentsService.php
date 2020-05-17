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
        $employmentsDays = Employment::whereYear('date', $year)
            ->whereMonth('date', $month);

        if ($day)
        {
            $employmentsDays->whereDay('date', $day);
        }

        $employmentsDays = $employmentsDays->get()->groupBy('date');


        $mainCategories = $this->getMainCategories($year, $month, $day);


        foreach ($employmentsDays as $date => $employments) 
        {
            $team = [];
            $day = Carbon::createFromDate($date)->day;

            $managerEmployment = [];

            foreach ($employments as $employment) 
            {
                if ($employment->worker_id == 0)
                {
                    $managerEmployment = $employment;
                    continue;
                }

                $mainCategory = $employment->main_category;
                $statusProduction = $employment->status->customable ? $employment->status_custom : $employment->status->salary_production;

                if (!empty($team[$mainCategory]))
                {
                    $team[$mainCategory] += $statusProduction;
                }
                else
                {
                    $team[$mainCategory] = $statusProduction;
                }
            }

            if (!empty($managerEmployment))
            {
                $statusProduction = $managerEmployment->status->customable ? $managerEmployment->status_custom : $managerEmployment->status->salary_production;
                $mainCategories['tiles']->productions[$day]->salary -= 50 * $statusProduction;

                $managerEmployment->update([
                    'salary' => ceil(200 * $statusProduction)
                ]);
            }

            foreach ($employments as $employment) 
            {
                if ($employment->worker_id == 0)
                {
                    continue;
                }

                $mainCategory = $employment->main_category;
                $statusProduction = $employment->status->customable ? $employment->status_custom : $employment->status->salary_production;

                if (!empty($mainCategories[$mainCategory]->productions[$day])) 
                {
                    $mainCategorySalary = $mainCategories[$mainCategory]->productions[$day]->salary;
                }
                else
                {
                    $mainCategorySalary = 0;
                }

                if (!empty($team[$mainCategory]))
                {
                    $salaryProduction = $mainCategorySalary / $team[$mainCategory] * $statusProduction;
                }
                else
                {
                    $salaryProduction = 0;
                }

                
                $salary = $salaryProduction + $employment->status->salary_fixed;

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

        $employments = Employment::where('worker_id', 0)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $employmentsSalary = 0;

        foreach ($employments as $employment) 
        {
            $employmentsSalary += $employment->salary;
        }

        $workerSalary = WorkerSalary::where('worker_id', 0)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->first();

        if (!$workerSalary)
        {
            $workerSalary = WorkerSalary::create([
                'date' => Carbon::createFromDate($year, $month, 1)->format('Y-m-d'),
                'worker_id' => 0,
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


    protected function getMainCategories($year, $month, $day)
    {
        $categoryProductions = Production::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('product_id', 0)
            ->where('order_id', 0);

        if ($day)
        {
            $categoryProductions->whereDay('date', $day);
        }

        $categoryProductions = $categoryProductions->get();

        $mainCategories = [];

        foreach ($categoryProductions as $categoryProduction) 
        {
            $mainCategory = $categoryProduction->category->main_category;
            $day = $categoryProduction->day;

            if (!empty($mainCategories[$mainCategory])) 
            {
                if (!empty($mainCategories[$mainCategory]->productions[$day]))
                {
                    $mainCategories[$mainCategory]->productions[$day]->salary += $categoryProduction->salary;
                }
                else
                {
                    $mainCategories[$mainCategory]->productions[$day] = $categoryProduction;
                }
            }
            else
            {
                $mainCategories[$mainCategory] = (object)[
                    'productions' => [
                        $day => $categoryProduction
                    ]
                ];
            }
        }

        return $mainCategories;
    }
}
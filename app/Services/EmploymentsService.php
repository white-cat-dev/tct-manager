<?php

namespace App\Services;

use DB;
use Carbon\Carbon;
use App\Employment;
use App\Production;
use App\Worker;


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

        $categoriesProductions = Production::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('product_id', 0)
            ->where('order_id', 0)
            ->get()
            ->groupBy('day');

        foreach ($employmentsDays as $date => $employments) 
        {
            $team = [];
            $day = Carbon::createFromDate($date)->day;

            foreach ($employments as $employment) 
            {
                foreach ($employment->facility->categories as $category) 
                {
                    if (!empty($team[$category->id]))
                    {
                        $team[$category->id] += $employment->status->salary_production;
                    }
                    else
                    {
                        $team[$category->id] = $employment->status->salary_production;
                    }
                }
            }

            foreach ($employments as $employment) 
            {
                $salaryProduction = 0;

                foreach ($employment->facility->categories as $category) 
                {
                    if (!empty($categoriesProductions[$day]))
                    {
                        $categoryProduction = $categoriesProductions[$day]->where('category_id', $category->id)->first();

                        $categorySalary = $categoryProduction ? $categoryProduction->salary : 0;
                    }
                    else
                    {
                        $categorySalary = 0;
                    }

                    $salaryProduction += $categorySalary / $team[$category->id] * $employment->status->salary_production;
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
                    'date' => Carbon::createFromDate($year, $month, 1)->format('yyyy-MM-dd'),
                    'worker_id' => $worker->id,
                    'employments' => 0,
                    'advance' => 0,
                    'bonus' => 0
                ]);
            }

            $workerSalary->update([
                'employments' => $employmentsSalary
            ]);
        }
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Worker;
use App\Employment;


class EmploymentsDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employments:day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create employments for current day';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $workers = Worker::where('status', Worker::STATUS_ACTIVE)->get();

        foreach ($workers as $worker) 
        {
            $employment = $worker->employments()->where('date', date('Y-m-d'))->first();

            if (!$employment)
            {
                $employment = $worker->employments()->create([
                    'date' => date('Y-m-d'),
                    'status_id' => 1,
                    'status_custom' => 1,
                    'main_category' => 'tiles',
                    'salary' => 0
                ]);
            }
        }

        $employment = Employment::where('worker_id', 0)->where('date', date('Y-m-d'))->first();

        if (!$employment)
        {
            $employment = Employment::create([
                'worker_id' => 0,
                'date' => date('Y-m-d'),
                'status_id' => 1,
                'status_custom' => 8,
                'main_category' => 'tiles',
                'salary' => 0
            ]);
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Worker;


class WorkersStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workers:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update workers status';

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
        $workers = Worker::all();
        
        foreach ($workers as $worker) 
        {     
            if ($worker->status_date && ($worker->status_date <= date('Y-m-d')))
            {
                $worker->update([
                    'status_date' => null,
                    'status' => ($worker->status + 1) % 2
                ]);
            }
        }
    }
}

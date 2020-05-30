<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Facility;


class FacilitiesStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facilities:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update facilities status';

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
        $facilities = Facility::all();
        
        foreach ($facilities as $facility) 
        {     
            if ($facility->status_date && ($facility->status_date <= date('Y-m-d')))
            {
                $facility->update([
                    'status_date' => null,
                    'status' => ($facility->status + 1) % 2
                ]);
            }
        }
    }
}

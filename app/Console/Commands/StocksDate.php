<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Stock;


class StocksDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stocks:date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update stocks date';

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
     * @return int
     */
    public function handle()
    {
        $stocks = Stock::all();

        foreach ($stocks as $stock) 
        {
            $stock->update([
                'process_date' => !empty($stock->process) ? $stock->process->date : $stock->date
            ]);
        }
    }
}

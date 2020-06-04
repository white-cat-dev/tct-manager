<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductionsService;

class OrdersPlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:plan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Plan orders';

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
        ProductionsService::getInstance()->replanOrders();
    }
}

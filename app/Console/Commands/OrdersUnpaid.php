<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Order;


class OrdersUnpaid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check finished but unpaid orders';

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
        $orders = Order::where('status', Order::STATUS_FINISHED)->get();

        foreach ($orders as $order) 
        {
            if ($order->paid < $order->cost)
            {
                $order->update([
                    'status' => Order::STATUS_UNPAID
                ]);
            }
        }
    }
}

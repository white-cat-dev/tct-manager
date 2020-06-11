<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Order;


class OrdersPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check orders payments';

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
        $orders = Order::all();

        foreach ($orders as $order) 
        {
            if ($order->paid != $order->payments_paid)
            {
                $order->payments()->create([
                    'date' => $order->date,
                    'paid' => $order->paid - $order->payments_paid
                ]);
            }
        }
    }
}

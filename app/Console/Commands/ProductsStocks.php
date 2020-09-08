<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Product;


class ProductsStocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:stocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Saving products stocks for current date';

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
        $products = Product::all();

        foreach ($products as $product) 
        {
            $stock = $product->stocks()->where(['date' => date('Y-m-d')])->first();
            
            if (!$stock)
            {
                $stock = $product->stocks()->create([
                    'date' => date('Y-m-d'),
                    'in_stock' => $product->in_stock,
                    'new_in_stock' => $product->in_stock,
                    'process_id' => 0,
                    'process_type' => '',
                    'reason' => 'create'
                ]);
            }
        }
    }
}

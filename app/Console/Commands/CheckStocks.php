<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Product;

class CheckStocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:stocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check stocks';

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
            $startStocks = $product->stocks()->first()->in_stock;

            $productions = $product->productions()->whereNotNull('date')->get();
            $productionsCount = $productions->sum('performed');

            $realizations = $product->realizations()->get();
            $realizationsCount = $realizations->sum('performed');

            $finishStocks = round($startStocks + $productionsCount - $realizationsCount, 3);

            $this->info($product->product_group->name . ' ' . $product->product_group->size . ' ' . $product->variation_text . ': '. $startStocks . ' + ' . $productionsCount . ' - ' . $realizationsCount . ' = ' . $finishStocks . ' (в наличии ' . $product->in_stock  . ')');

            if ($finishStocks != $product->in_stock)
            {
                $this->error('!!!');
            }
        }
    }
}

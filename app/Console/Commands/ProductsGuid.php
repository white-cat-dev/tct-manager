<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ProductGroup;
use Str;


class ProductsGuid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:guid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate guid for products';

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
        $productGroups = ProductGroup::all();

        foreach ($productGroups as $productGroup) 
        {
            if (!$productGroup->guid)
            {
                $productGroup->update([
                    'guid' => Str::uuid()
                ]);
            }
        }
    }
}

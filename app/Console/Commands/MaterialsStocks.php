<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Material;


class MaterialsStocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'materials:stocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Saving materials stocks for current date';

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
        $materials = Material::all();

        foreach ($materials as $material) 
        {
            $stock = $material->stocks()->where(['date' => date('Y-m-d')])->first();
            
            if (!$stock)
            {
                $stock = $material->stocks()->create([
                    'date' => date('Y-m-d'),
                    'in_stock' => $material->in_stock,
                    'new_in_stock' => $material->in_stock,
                    'process_id' => 0,
                    'process_type' => '',
                    'reason' => 'create'
                ]);
            }
        }
    }
}

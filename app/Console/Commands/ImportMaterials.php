<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MaterialGroup;


class ImportMaterials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:materials {file?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import materials and recipes from json file';

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
        $file = $this->argument('file');

        if (empty($file))
        {
            $file = resource_path('/data/materials.json');
        }
        $materialGroupsData = json_decode(file_get_contents($file), true);

        foreach ($materialGroupsData as $materialGroupData) 
        {
            $materialGroup = $this->getMaterialGroup($materialGroupData);

            foreach ($materialGroupData['materials'] as $materialData) 
            {
                $material = $this->getMaterial($materialData, $materialGroup);
            }

            $this->info($materialGroup->name);
        }
    }


    protected function getMaterialGroup($materialGroupData)
    {
        $materialGroup = MaterialGroup::where('name', $materialGroupData['name'])
            ->first();

        if (!$materialGroup)
        {
            $materialGroup = MaterialGroup::create($materialGroupData);
        }
        else
        {
            $materialGroup->update($materialGroupData);
        }

        return $materialGroup;
    }


    protected function getMaterial($materialData, $materialGroup)
    {
        $material = $materialGroup->materials()
            ->where('variation', $materialData['variation'])
            ->first();

        if (!$material)
        {
            $material = $materialGroup->materials()->create($materialData);
        }
        else
        {
            $material->update($materialData);
        }

        return $material;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MaterialGroup;
use App\ProductGroup;
use App\Recipe;


class ImportRecipes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:recipes {file?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import recipes from json file';

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
            $file = resource_path('/data/recipes.json');
        }
        $recipesData = json_decode(file_get_contents($file), true);

        foreach ($recipesData as $recipeData) 
        {
            $recipe = $this->getRecipe($recipeData);

            foreach ($recipeData['material_groups'] as $materialGroupData) 
            {
                $materialGroup = $this->getMaterialGroup($materialGroupData);

                if ($materialGroup)
                {
                    $recipeMaterialGroup = $recipe->material_groups->find($materialGroup->id);

                    if (!$recipeMaterialGroup) 
                    {
                        $recipeMaterialGroup = $recipe->material_groups()->attach($materialGroup->id, [
                            'count' => $materialGroupData['count']
                        ]);
                    }
                    else 
                    {
                        $recipe->material_groups()->updateExistingPivot($materialGroup->id, [
                            'count' => $materialGroupData['count']
                        ]);
                    }

                }
            }

            foreach ($recipeData['product_groups'] as $productGroupData) 
            {
                $productGroup = $this->getProductGroup($productGroupData);

                if ($productGroup)
                {
                    $productGroup->update([
                        'recipe_id' => $recipe->id
                    ]);
                }
            }

            $this->info($recipe->name);
        }
    }


    protected function getRecipe($recipeData)
    {
        $recipe = Recipe::where('name', $recipeData['name'])
            ->first();

        if (!$recipe)
        {
            $recipe = Recipe::create($recipeData);
        }
        else
        {
            $recipe->update($recipeData);
        }

        return $recipe;
    }


    protected function getMaterialGroup($materialGroupData)
    {
        $materialGroup = MaterialGroup::where('name', $materialGroupData['name'])
            ->first();

        return $materialGroup;
    }


    protected function getProductGroup($productGroupData)
    {
        $size = explode('×', $productGroupData['size']);
        if (count($size) == 3)
        {
            $productGroup = ProductGroup::where('name', $productGroupData['name'])
                ->where('length', $size[0])
                ->where('width', $size[1])
                ->where('height', $size[2])
                ->first();      
        }
        else
        {
            $productGroup = ProductGroup::where('name', $productGroupData['name'])
                ->where('length', $size[0])
                ->where('height', $size[1])
                ->first();   
        }

        return $productGroup;
    }
}

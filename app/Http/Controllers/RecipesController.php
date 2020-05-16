<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Recipe;


class RecipesController extends Controller
{
    public function index(Request $request) 
    {
        if ($request->wantsJson())
        {
            $recipes = Recipe::all();

            return $recipes;
        }

        return view('index', ['ngTemplate' => 'recipes']);
    }


    public function show(Request $request, Recipe $recipe) 
    {
        if ($request->wantsJson())
        {
            return $recipe;
        }

        return view('index', ['ngTemplate' => 'recipes.show']);
    }


    public function create(Request $request) 
    {
        if ($request->wantsJson())
        {
            $this->validate($request, $this->validationRules);

            $recipeData = $this->getData($request);
            $recipe = Recipe::create($recipeData);

            foreach ($request->get('material_groups', []) as $materialGroupData) 
            {
            	$recipe->material_groups()->attach([
                    $materialGroupData['id'] => [
                        'count' => $materialGroupData['pivot']['count']
                    ]
                ]);
            }
            
            return $recipe;
        }

        return view('index', ['ngTemplate' => 'recipes.edit']);
    }


    public function edit(Request $request, Recipe $recipe) 
    {
        if ($request->wantsJson())
        {
            $this->validate($request, $this->validationRules);

            $recipesData = $this->getData($request);
            $recipe->update($recipesData);

            $materialGroupsIds = $recipe->material_groups()->select('material_group_id')->pluck('material_group_id', 'material_group_id');

            foreach ($request->get('material_groups', []) as $materialGroupData) 
            {
                $materialGroupsIds->forget($materialGroupData['id']);

                $materialGroup = $recipe->material_groups()->find($materialGroupData['id']);

                if (!$materialGroup) 
                {
                    $materialGroup = $recipe->material_groups()->attach($materialGroupData['id'], [
                        'count' => $materialGroupData['pivot']['count']
                    ]);
                }
                else 
                {
                    $recipe->material_groups()->updateExistingPivot($materialGroupData['id'], [
                        'count' => $materialGroupData['pivot']['count']
                    ]);
                }
            }

            $recipe->material_groups()->whereIn('recipes_material_groups.material_group_id', $materialGroupsIds)->delete();

            return $recipe;
        }

        return view('index', ['ngTemplate' => 'recipes.edit']);
    }


    public function delete(Request $request, Recipe $recipe)
    {
        if ($request->wantsJson())
        {
            $recipe->delete();
        }
    }


    protected $validationRules = [
        'name' => 'required',
        'category_id' => 'required'
    ];


    protected function getData(Request $request)
    {
        return [
            'name' => $request->get('name', ''),
            'category_id' => $request->get('category_id', 0)
        ];
    }
}
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
            $recipes = Recipe::orderBy('name')->get();

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

            $recipeData = $this->getData($request);
            $recipe->update($recipeData);

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

            $recipe->material_groups()->detach($materialGroupsIds);

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


    public function copy(Request $request, Recipe $recipe)
    {
        $recipeCopy = $recipe->replicate()->fill([
            'name' => $recipe->name . ' (копия)'
        ]);
        $recipeCopy->save();

        foreach ($recipe->material_groups as $materialGroup) 
        {
            $recipeCopy->material_groups()->attach([
                $materialGroup->id => [
                    'count' => $materialGroup->pivot->count
                ]
            ]);
        }

        return $recipeCopy;
    }


    protected $validationRules = [
        'name' => 'required'
    ];


    protected function getData(Request $request)
    {
        return [
            'name' => $request->get('name', '')
        ];
    }
}
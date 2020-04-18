<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;


class CategoriesController extends Controller
{
    public function index(Request $request) 
    {
        if ($request->wantsJson())
        {
        	$categories = Category::all();
        	return $categories;
        }

        return view('index', ['ngTemplate' => 'categories']);
    }


    public function show(Request $request, Category $category) 
    {
        if ($request->wantsJson())
        {
        	return $category;
        }

        return view('index', ['ngTemplate' => 'categories.show']);
    }


    public function create(Request $request) 
    {
        if ($request->wantsJson())
        {
			$this->validate($request, $this->validationRules);

            $category = Category::create($this->getData($request));
            
            return $category;
        }

        return view('index', ['ngTemplate' => 'categories.edit']);
    }


    public function edit(Request $request, Category $category) 
    {
        if ($request->wantsJson())
        {
        	$this->validate($request, $this->validationRules);

            $category->update($this->getData($request));

            return $category;
        }

        return view('index', ['ngTemplate' => 'categories.edit']);
    }


    public function delete(Request $request, Category $category)
    {
        if ($request->wantsJson())
        {
            $category->delete();
        }
    }



    protected $validationRules = [
        'name' => 'required',
        'units' => 'required'
    ];

    protected function getData(Request $request)
    {
        return [
            'name' => $request->get('name', ''),
            'units' => $request->get('units', 'area'),
            'has_colors' => $request->get('has_colors', false)
        ];
    }
}
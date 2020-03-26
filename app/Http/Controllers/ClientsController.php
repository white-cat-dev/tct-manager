<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;


class ClientsController extends Controller
{
    protected $validationRules = [
        'name' => 'required',
        'phone' => 'required'
    ];

    public function index(Request $request) 
    {
        if ($request->wantsJson())
        {
        	$clients = Client::all();
        	return $clients;
        }

        return view('index', ['ngTemplate' => 'clients']);
    }


    public function show(Request $request, Client $client) 
    {
        if ($request->wantsJson())
        {
        	return $client;
        }

        return view('index', ['ngTemplate' => 'clients.show']);
    }


    public function create(Request $request) 
    {
        if ($request->wantsJson())
        {
			$this->validate($request, $this->validationRules);

            $client = Client::create($this->getData($request));
            
            return $client;
        }

        return view('index', ['ngTemplate' => 'clients.edit']);
    }


    public function edit(Request $request, Client $client) 
    {
        if ($request->wantsJson())
        {
        	$this->validate($request, $this->validationRules);

            $client->update($this->getData($request));

            return $client;
        }

        return view('index', ['ngTemplate' => 'clients.edit']);
    }


    public function delete(Request $request, Client $client)
    {
        if ($request->wantsJson())
        {
            $client->delete();
        }
    }


    protected function getData(Request $request)
    {
        return [
            'name' => $request->get('name'),
            'phone' => $request->get('phone'),
            'email' => $request->get('email')
        ];
    }
}
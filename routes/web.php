<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth')->group(function() 
{
	Route::get('/', function() {
		return redirect()->route('orders');
	});

	Route::prefix('categories')->group(function()
	{
		Route::get('/', 'CategoriesController@index')->name('categories');
		Route::get('create', 'CategoriesController@create')->name('category-create');
		Route::get('{category}', 'CategoriesController@show')->name('category-show');
		Route::get('{category}/edit', 'CategoriesController@edit')->name('category-edit');
		
		Route::post('/', 'CategoriesController@create');
		Route::post('{category}', 'CategoriesController@edit');

		Route::delete('{category}', 'CategoriesController@delete');
	});


	Route::prefix('products')->group(function()
	{
		Route::get('/', 'ProductsController@index')->name('products');
		Route::get('create', 'ProductsController@create')->name('product-create');
		Route::get('{productGroup}', 'ProductsController@show')->name('product-show');
		Route::get('{productGroup}/edit', 'ProductsController@edit')->name('product-edit');

		Route::post('/', 'ProductsController@create');
		Route::post('{productGroup}', 'ProductsController@edit');

		Route::delete('{productGroup}', 'ProductsController@delete');
	});


	Route::prefix('clients')->group(function()
	{
		Route::get('/', 'ClientsController@index')->name('clients');
		Route::get('create', 'ClientsController@create')->name('client-create');
		Route::get('{client}', 'ClientsController@show')->name('client-show');
		Route::get('{client}/edit', 'ClientsController@edit')->name('client-edit');

		Route::post('/', 'ClientsController@create');
		Route::post('{client}', 'ClientsController@edit');

		Route::delete('{client}', 'ClientsController@delete');
	});


	Route::prefix('orders')->group(function()
	{
		Route::post('realizations', 'OrdersController@saveRealizations');

		Route::get('/', 'OrdersController@index')->name('orders');
		Route::get('create', 'OrdersController@create')->name('order-create');
		Route::get('{order}', 'OrdersController@show')->name('order-show');
		Route::get('{order}/edit', 'OrdersController@edit')->name('order-edit');

		Route::post('/', 'OrdersController@create');
		Route::post('{order}', 'OrdersController@edit');

		Route::delete('{order}', 'OrdersController@delete');
	});


	Route::prefix('production')->group(function()
	{
		Route::get('/', 'ProductionController@index')->name('production');
		Route::get('orders', 'ProductionController@orders');

		Route::post('/', 'ProductionController@save');
	});


	Route::prefix('workers')->group(function()
	{
		Route::get('/', 'WorkersController@index')->name('workers');
		Route::get('create', 'WorkersController@create')->name('worker-create');
		Route::get('{worker}', 'WorkersController@show')->name('worker-show');
		Route::get('{worker}/edit', 'WorkersController@edit')->name('worker-edit');

		Route::post('/', 'WorkersController@create');
		Route::post('{worker}', 'WorkersController@edit');
		
		Route::delete('{worker}', 'WorkersController@delete');
	});


	Route::prefix('employments')->group(function()
	{
		Route::get('/', 'EmploymentsController@index')->name('employments');
		Route::post('/', 'EmploymentsController@save');
		
		Route::post('salaries/{salary}', 'EmploymentsController@saveSalary');

		Route::get('statuses', 'EmploymentStatusesController@index')->name('employment-statuses');
		Route::post('statuses', 'EmploymentStatusesController@save');

	});


	Route::prefix('facilities')->group(function()
	{
		Route::get('/', 'FacilitiesController@index')->name('facilities');
		Route::get('create', 'FacilitiesController@create')->name('facility-create');
		Route::get('{facility}', 'FacilitiesController@show')->name('facility-show');
		Route::get('{facility}/edit', 'FacilitiesController@edit')->name('facility-edit');

		Route::post('/', 'FacilitiesController@create');
		Route::post('{facility}', 'FacilitiesController@edit');
		
		Route::delete('{facility}', 'FacilitiesController@delete');
	});


	Route::prefix('templates')->group(function()
	{
		Route::prefix('categories')->group(function()
		{
			Route::get('/', 'TemplatesController@categories');
			Route::get('show', 'TemplatesController@categoriesShow');
			Route::get('edit', 'TemplatesController@categoriesEdit');
		});

		Route::prefix('products')->group(function()
		{
			Route::get('/', 'TemplatesController@products');
			Route::get('show', 'TemplatesController@productsShow');
			Route::get('edit', 'TemplatesController@productsEdit');
		});

		Route::prefix('clients')->group(function()
		{
			Route::get('/', 'TemplatesController@clients');
			Route::get('show', 'TemplatesController@clientsShow');
			Route::get('edit', 'TemplatesController@clientsEdit');
		});

		Route::prefix('orders')->group(function()
		{
			Route::get('/', 'TemplatesController@orders');
			Route::get('show', 'TemplatesController@ordersShow');
			Route::get('edit', 'TemplatesController@ordersEdit');
		});

		Route::prefix('production')->group(function()
		{
			Route::get('/', 'TemplatesController@production');
		});

		Route::prefix('employments')->group(function()
		{
			Route::get('/', 'TemplatesController@employments');
			Route::get('statuses', 'TemplatesController@employmentsStatuses');
		});

		Route::prefix('workers')->group(function()
		{
			Route::get('/', 'TemplatesController@workers');
			Route::get('show', 'TemplatesController@workersShow');
			Route::get('edit', 'TemplatesController@workersEdit');
		});

		Route::prefix('facilities')->group(function()
		{
			Route::get('/', 'TemplatesController@facilities');
			Route::get('show', 'TemplatesController@facilitiesShow');
			Route::get('edit', 'TemplatesController@facilitiesEdit');
		});
	});
});


Auth::routes(['register' => false, 'password.request' => false]);;

Route::get('/home', 'HomeController@index')->name('home');

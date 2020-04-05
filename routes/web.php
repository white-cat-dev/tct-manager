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
	// Route::get('create', 'OrdersController@create')->name('order-create');
	// Route::get('{order}', 'OrdersController@show')->name('order-show');
	// Route::get('{order}/edit', 'OrdersController@edit')->name('order-edit');

	// Route::post('/', 'OrdersController@create');
	// Route::post('{order}', 'OrdersController@edit');

	// Route::delete('{order}', 'OrdersController@delete');
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
});
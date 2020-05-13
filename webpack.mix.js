const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js(['resources/js/app.js',
		'resources/js/main.js',
		'resources/js/categories.js',
		'resources/js/products.js',
		'resources/js/clients.js',
		'resources/js/orders.js',
		'resources/js/productions.js',
		'resources/js/workers.js',
		'resources/js/statuses.js',
		'resources/js/facilities.js',
		'resources/js/employments.js'], 'public/js')

    .sass('resources/sass/app.scss', 'public/css');
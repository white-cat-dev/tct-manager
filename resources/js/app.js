require('angular');


var tctApp = angular.module('tctApp', [
	require('angular-sanitize'),
	require('angular-route'), 
	require('angular-resource'), 
	require('ui-select'),
]);


tctApp.config(['$locationProvider', function($locationProvider) {
    $locationProvider.html5Mode({
        enabled: true,
        requireBase: false
    });
}]);


tctApp.config(['$routeProvider', function($routeProvider) {
	$routeProvider
		.when('/categories', {
			templateUrl: '/templates/categories',
			controller: 'CategoriesController'
	    })
	    .when('/categories/create', {
			templateUrl: '/templates/categories/edit',
			controller: 'CategoriesController'
	    })
	    .when('/categories/:id', {
			templateUrl: '/templates/categories/show',
			controller: 'CategoriesController'
	    })
	    .when('/categories/:id/edit', {
			templateUrl: '/templates/categories/edit',
			controller: 'CategoriesController'
	    })

		.when('/products', {
			templateUrl: '/templates/products',
			controller: 'ProductsController'
	    })
	    .when('/products/create', {
			templateUrl: '/templates/products/edit',
			controller: 'ProductsController'
	    })
	    .when('/products/:id', {
			templateUrl: '/templates/products/show',
			controller: 'ProductsController'
	    })
	    .when('/products/:id/edit', {
			templateUrl: '/templates/products/edit',
			controller: 'ProductsController'
	    })

	    .when('/clients', {
			templateUrl: '/templates/clients',
			controller: 'ClientsController'
	    })
	    .when('/clients/create', {
			templateUrl: '/templates/clients/edit',
			controller: 'ClientsController'
	    })
	    .when('/clients/:id', {
			templateUrl: '/templates/clients/show',
			controller: 'ClientsController'
	    })
	    .when('/clients/:id/edit', {
			templateUrl: '/templates/clients/edit',
			controller: 'ClientsController'
	    })

	    .when('/orders', {
			templateUrl: '/templates/orders',
			controller: 'OrdersController'
	    })
	    .when('/orders/create', {
			templateUrl: '/templates/orders/edit',
			controller: 'OrdersController'
	    })
	    .when('/orders/:id', {
			templateUrl: '/templates/orders/show',
			controller: 'OrdersController'
	    })
	    .when('/orders/:id/edit', {
			templateUrl: '/templates/orders/edit',
			controller: 'OrdersController'
	    })

	    .when('/production', {
			templateUrl: '/templates/production',
			controller: 'ProductionController'
	    })
}]);

tctApp.factory('CategoriesRepository', ['$resource', function($resource) { 
	return $resource('/categories/:id'); 
}]);

tctApp.factory('ProductsRepository', ['$resource', function($resource) { 
	return $resource('/products/:id'); 
}]);

tctApp.factory('ClientsRepository', ['$resource', function($resource) { 
	return $resource('/clients/:id'); 
}]);

tctApp.factory('OrdersRepository', ['$resource', function($resource) { 
	return $resource('/orders/:id'); 
}]);

tctApp.factory('ProductionRepository', ['$resource', function($resource) { 
	return $resource('/production', null, {
		orders: { method: 'GET', url: '/production/orders' }
    }); 
}]);


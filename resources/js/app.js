window.moment = require('moment');

require('bootstrap');
require('angular');
require('angularjs-color-picker');
require('angular-datepicker');


var tctApp = angular.module('tctApp', [
	require('angular-sanitize'),
	require('angular-route'), 
	require('angular-resource'), 
	require('ui-select'),
	'color.picker',
	'datePicker'
]);


tctApp.config(['$locationProvider', function($locationProvider) {
    $locationProvider.html5Mode({
        enabled: true,
        requireBase: false
    });
}]);

tctApp.config(function($provide) {
    $provide.decorator('ColorPickerOptions', function($delegate) {
        var options = angular.copy($delegate);
        options.round = false;
        options.alpha = false;
    	options.hue = true;
        options.format = 'hexString';

        return options;
    });
});

tctApp.config(function($provide) {
    $provide.value('$locale', {
    	DATETIME_FORMATS: {
			"AMPMS": [ "д.п.", "п.п." ],
			"DAY": [ "воскресенье", "понедельник", "вторник", "среда", "четверг", "пятница", "суббота" ],
			"ERANAMES": [ "до н.э.", "н.э." ],
			"ERAS": [ "до н.э.", "н.э." ],
			"FIRSTDAYOFWEEK": 0,
			"MONTH": ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
			"SHORTDAY": ["вс", "пн", "вт", "ср", "чт", "пт", "сб"],
			"SHORTMONTH": ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
			"STANDALONEMONTH": ["январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь"],
			"WEEKENDRANGE": [5, 6],
			"fullDate": "EEEE, d MMMM y г.",
			"longDate": "d MMMM y г.",
			"medium": "d MMMM y г. H:mm",
			"mediumDate": "d MMMM y г.",
			"mediumTime": "H:mm:ss",
			"short": "dd.MM.yyyy H:mm",
			"shortDate": "dd.MM.yyyy",
			"shortTime": "H:mm",
			"format": "dd.MM.yyyy"
		},
	    "NUMBER_FORMATS": {
			"CURRENCY_SYM": "\u20bd",
			"DECIMAL_SEP": ",",
			"GROUP_SEP": "\u00a0",
			"PATTERNS": [
				{
					"gSize": 3,
					"lgSize": 3,
					"maxFrac": 3,
					"minFrac": 0,
					"minInt": 1,
					"negPre": "-",
					"negSuf": "",
					"posPre": "",
					"posSuf": ""
				}
			]
		}
	});
});


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

	    .when('/productions', {
			templateUrl: '/templates/productions',
			controller: 'ProductionsController'
	    })

	    .when('/employments', {
			templateUrl: '/templates/employments',
			controller: 'EmploymentsController'
	    })

	    .when('/employments/statuses', {
			templateUrl: '/templates/employments/statuses',
			controller: 'EmploymentStatusesController'
	    })

	    .when('/workers', {
			templateUrl: '/templates/workers',
			controller: 'WorkersController'
	    })
	    .when('/workers/create', {
			templateUrl: '/templates/workers/edit',
			controller: 'WorkersController'
	    })
	    .when('/workers/:id', {
			templateUrl: '/templates/workers/show',
			controller: 'WorkersController'
	    })
	    .when('/workers/:id/edit', {
			templateUrl: '/templates/workers/edit',
			controller: 'WorkersController'
	    })

	    .when('/facilities', {
			templateUrl: '/templates/facilities',
			controller: 'FacilitiesController'
	    })
	    .when('/facilities/create', {
			templateUrl: '/templates/facilities/edit',
			controller: 'FacilitiesController'
	    })
	    .when('/facilities/:id', {
			templateUrl: '/templates/facilities/show',
			controller: 'FacilitiesController'
	    })
	    .when('/facilities/:id/edit', {
			templateUrl: '/templates/facilities/edit',
			controller: 'FacilitiesController'
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
	return $resource('/orders/:id', null, {
		saveRealizations: { method: 'POST', url: '/orders/realizations' }
    });  
}]);

tctApp.factory('WorkersRepository', ['$resource', function($resource) { 
	return $resource('/workers/:id'); 
}]);

tctApp.factory('EmploymentStatusesRepository', ['$resource', function($resource) { 
	return $resource('/employments/statuses'); 
}]);

tctApp.factory('FacilitiesRepository', ['$resource', function($resource) { 
	return $resource('/facilities/:id'); 
}]);

tctApp.factory('EmploymentsRepository', ['$resource', function($resource) { 
	return $resource('/employments', null, {
		saveSalary: { method: 'POST', url: '/employments/salaries/:id' }
    });  
}]);

tctApp.factory('ProductionsRepository', ['$resource', function($resource) { 
	return $resource('/productions', null, {
		orders: { method: 'GET', url: '/productions/orders' }
    }); 
}]);


require('jquery');
require('bootstrap');
require('angular');
require('angularjs-color-picker');
require('angular-datepicker');
window.toastr = require('toastr');
window.moment = require('moment');

toastr.options.timeOut = 2000;
toastr.options.extendedTimeOut = 1000;


var tctApp = angular.module('tctApp', [
	require('angular-sanitize'),
	require('angular-route'), 
	require('angular-resource'), 
	require('angular-ui-mask'),
	require('ui-select'),
	'color.picker',
	'datePicker'
]);


tctApp.config(['$httpProvider', function($httpProvider) {
    $httpProvider.useApplyAsync(true);

    $httpProvider.interceptors.push(['$rootScope', '$q', '$location', function ($rootScope, $q, $location) {
        return {
            response: function (response) 
            {
                return response || $q.when(response);
            },

            responseError: function (response) 
            {
                switch (response.status)
                {
                    case -1:
                    case 502:
                        toastr.error('Проверьте подключение к Интернету и обновите страницу');
                        break;

                    case 301:
                    case 302:
                        $location.url('/');
                        toastr.warning('Страница перемещена');
                        break;

                    case 401:
                    case 403:
                    case 419:
                        toastr.error('Ваша сессия истекла, обновите страницу и авторизуйтесь');
                        break;

                    case 422:
                        toastr.error('Проверьте введенные данные');
                        break;

                    case 404:
                        $location.url('/');
                        toastr.warning('Страница не найдена');
                        break;

                    case 500:
                        toastr.error('Произошла ошибка на сервере, сообщите разработчику');
                        break;

                    default:
                        toastr.error('Произошла неизвестная ошибка, сообщите разработчику код ответа - ' + response.status);
                        break;
                }

                return $q.reject(response);
            },
            request: function(config) 
            {
                return config;
            },
            requestError: function(err) 
            {
                $q.reject(err);
            }
        };
    }]);
}]);


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
			"DECIMAL_SEP": ".",
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

	    .when('/materials', {
			templateUrl: '/templates/materials',
			controller: 'MaterialsController'
	    })
	    .when('/materials/create', {
			templateUrl: '/templates/materials/edit',
			controller: 'MaterialsController'
	    })
	    .when('/materials/:id', {
			templateUrl: '/templates/materials/show',
			controller: 'MaterialsController'
	    })
	    .when('/materials/:id/edit', {
			templateUrl: '/templates/materials/edit',
			controller: 'MaterialsController'
	    })

	    .when('/recipes', {
			templateUrl: '/templates/recipes',
			controller: 'RecipesController'
	    })
	    .when('/recipes/create', {
			templateUrl: '/templates/recipes/edit',
			controller: 'RecipesController'
	    })
	    .when('/recipes/:id', {
			templateUrl: '/templates/recipes/show',
			controller: 'RecipesController'
	    })
	    .when('/recipes/:id/edit', {
			templateUrl: '/templates/recipes/edit',
			controller: 'RecipesController'
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
	return $resource('/products/:id', null, {
		copy: { method: 'POST', url: '/products/:id/copy' },
		orders: { method: 'GET', url: '/products/:id/orders', 'isArray': true },
		stocks:  { method: 'GET', url: '/products/:id/stocks' }
    });  
}]);

tctApp.factory('MaterialsRepository', ['$resource', function($resource) { 
	return $resource('/materials/:id', null, {
		copy: { method: 'POST', url: '/materials/:id/copy' },
		supplies: { method: 'GET', url: '/materials/:id/supplies' },
		saveSupply: { method: 'POST', url: '/materials/supply' },
		stocks:  { method: 'GET', url: '/materials/:id/stocks' }
    });  
}]);

tctApp.factory('RecipesRepository', ['$resource', function($resource) { 
	return $resource('/recipes/:id', null, {
		copy: { method: 'POST', url: '/recipes/:id/copy' }
    });  
}]);

tctApp.factory('ClientsRepository', ['$resource', function($resource) { 
	return $resource('/clients/:id'); 
}]);

tctApp.factory('OrdersRepository', ['$resource', function($resource) { 
	return $resource('/orders/:id', null, {
		saveRealization: { method: 'POST', url: '/orders/realization' },
		savePayment: { method: 'POST', url: '/orders/payment' },
		getDate: { method: 'POST', url: '/orders/date' },
		paidCostReport: { method: 'GET', url: '/orders/paid-cost-report' },
		query: { method: 'GET', url: '/orders' }
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
		saveMaterials: { method: 'POST', url: '/productions/materials' },
		replan: { method: 'GET', url: '/productions/replan' }
	});
}]);

tctApp.factory('ExportsRepository', ['$resource', function($resource) { 
	return $resource('/export', null, {
		products: { method: 'GET', url: '/export/products' },
		materials: { method: 'GET', url: '/export/materials' },
		productions: { method: 'GET', url: '/export/productions' },
		order: { method: 'GET', url: '/export/order' }
    });  
}]);

tctApp.factory('AuthRepository', ['$resource', function($resource) {
	return $resource('/login', null, {
	    logout: { method: 'POST', url: '/logout' }
	});
}]);




tctApp.run(function($rootScope, $location, AuthRepository) 
{
    $rootScope.searchInputKeyPressed = function($event) 
    {
        if ($event.which === 13)
        {
        	$event.currentTarget.blur();
        	$event.currentTarget.nextElementSibling.querySelector('.btn').click();
        }
    };

    $rootScope.inputKeyPressed = function($event) 
    {
        if ($event.which === 13)
        {
        	$event.currentTarget.blur();
        }
    };

    $rootScope.focusNextInput = function($event) 
    {
        var nextInput = $event.currentTarget.nextElementSibling;

        setTimeout(function() {
        	nextInput.focus();
        }, 50);
    };

    $rootScope.inputFloat = function(model, key)
    {
		model[key] = model[key].replace(',', '.');
		model[key] = model[key].replace(/[^.\d]/g, '');

		if (model[key].split('.').length - 1 > 1)
		{
			var index = model[key].indexOf('.');
			var count = model[key].substring(0, index);
			model[key] = model[key].substring(index).replace('.', '');
			model[key] = count + model[key];
		}
    }

    $rootScope.logout = function()
    {
    	AuthRepository.logout(function(response) 
    	{
			$location.url('/login');
    	});
    }
});


angular.module('tctApp').controller('ProductionController', [
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
	'ProductionRepository',
	'OrdersRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$timeout,
		ProductionRepository,
		OrdersRepository
	){

	$scope.days = 0;
	$scope.monthes = [];
	$scope.years = [];

	$scope.currentDay = 0;
	$scope.currentMonth = 0;
	$scope.currentYear = 0;

	$scope.productionProducts = [];
	$scope.productionOrders = [];


	$scope.init = function()
	{
		var request = {};
		if ($scope.currentYear > 0)
		{
			request.year = $scope.currentYear;
		}
		if ($scope.currentMonth > 0)
		{
			request.month = $scope.currentMonth;
		}

		ProductionRepository.get(request, function(response) 
		{
			$scope.days = response.days;
			$scope.monthes = response.monthes;
			$scope.years = response.years;

			$scope.currentDay = response.day;
			$scope.currentMonth = response.month;
			$scope.currentYear = response.year;

			$scope.productionProducts = response.products;

			// for (product of $scope.productionProducts)
			// {
			// 	for (key in product.productions)
			// 	{
			// 		if (key < $scope.currentDay)
			// 		{
			// 			if (product.productions[key].performed >= product.productions[key].planned)
			// 			{
			// 				product.productions[key].status = 'done';
			// 			}
			// 			else
			// 			{
			// 				product.productions[key].status = 'failed';
			// 			}
			// 		}
			// 	}
			// }
		});


		OrdersRepository.query({'status': 'current'}, function(response) 
		{
			$scope.productionOrders = response;

			console.log($scope.productionOrders);
		});
	}

	$scope.$watch('currentYear', function(newValue, oldValue) 
	{
		$scope.init();
	});

	$scope.$watch('currentMonth', function(newValue, oldValue) 
	{
		$scope.init();
	});


	$scope.markColors = [
		'#9b59b6',
		'#2ecc71',
		'#1abc9c',
		'#3498db'
	];

	$scope.ordersMarkColors = {};

	$scope.markOrder = function(orderId)
	{
		if ($scope.ordersMarkColors[orderId])
		{
			$scope.markColors.push($scope.ordersMarkColors[orderId]);
			delete $scope.ordersMarkColors[orderId];
		}
		else
		{
			$scope.ordersMarkColors[orderId] = $scope.markColors.pop();
		}
	}

	
	$scope.getOrderMarkColor = function(production)
	{
		if ((production) && ($scope.ordersMarkColors[production.order_id]))
		{
			return $scope.ordersMarkColors[production.order_id];
		}
		else
		{
			return 'transparent';
		}
	}



	$scope.isModalShown = false;

	$scope.modalDate = '';
	$scope.modalProductionOrders = [];
	$scope.modalNoOrderProductions = [];
	$scope.modalProductId = 0;


	$scope.showModal = function(day, productId)
	{
		var request = {
			'year': $scope.currentYear,
			'month': $scope.currentMonth,
			'day': day
		}

		if (productId)
		{
			request['product_id'] = productId;
		}

		ProductionRepository.orders(request, function(response) 
		{
			$scope.modalDate = response.date;
			$scope.modalProductionOrders = response.orders;
			$scope.modalNoOrderProductions = response.no_order;

			$scope.isModalShown = true;
		});
	}


	$scope.hideModal = function()
	{
		$scope.isModalShown = false;

		$scope.modalDate = '';
		$scope.modalProductionOrders = [];
	}

	


	$scope.save = function()
	{
		var productions = [];
		for (order of $scope.modalProductionOrders)
		{
			for (production of order.productions)
			{
				productions.push(production);
			}
		}

		ProductionRepository.save({'productions': productions}, function(response) 
		{
			$scope.successAlert = 'Все изменения успешно сохранены!';
			$scope.showAlert = true;

			$timeout(function() {
				$scope.showAlert = false;
			}, 2000);


			$scope.hideModal();
			$scope.init();
		});
	}
}]);
angular.module('tctApp').controller('ProductionController', [
	'$scope',
	'$routeParams',
	'$location',
	'ProductionRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		ProductionRepository
	){

	$scope.days = 0;
	$scope.monthes = {};
	$scope.years = {};

	$scope.currentDay = 0;
	$scope.currentMonth = 0;
	$scope.currentYear = 0;

	$scope.products = [];


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
		console.log(request);

		ProductionRepository.get(request, function(response) 
		{
			$scope.days = response.days;
			$scope.monthes = response.monthes;
			$scope.years = response.years;

			$scope.currentDay = response.day;
			$scope.currentMonth = response.month;
			$scope.currentYear = response.year;

			$scope.productionProducts = response.products;

			for (product of $scope.productionProducts)
			{
				for (key in product.productions)
				{
					if (key < $scope.currentDay)
					{
						if (product.productions[key].performed >= product.productions[key].planned)
						{
							product.productions[key].status = 'done';
						}
						else
						{
							product.productions[key].status = 'failed';
						}
					}
				}
			}
		});
	}


	$scope.isModalShown = false;

	$scope.modalDate = '';
	$scope.modalProductionOrders = [];
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
			$scope.hideModal();
			$scope.init();
		});
	}
}]);
angular.module('tctApp').controller('ProductionController', [
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
	'$filter',
	'ProductionRepository',
	'OrdersRepository',
	'ProductsRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$timeout,
		$filter,
		ProductionRepository,
		OrdersRepository,
		ProductsRepository
	){

	$scope.days = 0;
	$scope.monthes = [];
	$scope.years = [];

	$scope.currentDate = {
		'day': 0,
		'month': 0,
		'year': 0
	};

	$scope.productionProducts = [];
	$scope.productionOrders = [];
	
	$scope.facilities = {};


	$scope.init = function()
	{
		var request = {};
		if ($scope.currentDate.year > 0)
		{
			request.year = $scope.currentDate.year;
		}
		if ($scope.currentDate.month > 0)
		{
			request.month = $scope.currentDate.month;
		}

		ProductionRepository.get(request, function(response) 
		{
			$scope.days = response.days;
			$scope.monthes = response.monthes;
			$scope.years = response.years;

			$scope.currentDate.day = response.day;
			$scope.currentDate.month = response.month;
			$scope.currentDate.year = response.year;

			$scope.productionProducts = response.products;
			$scope.productionOrders = response.orders;

			$scope.facilities = response.facilities;
		});
	}


	$scope.markColors = [
		'#e67e22',
		'#9b59b6',
		'#2ecc71',
		'#1abc9c',
		'#d35400',
		'#f1c40f',
		'#3498db',
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

	$scope.modalDate = new Date();
	$scope.modalProductionProducts = [];

	$scope.isOrdersShown = false;


	$scope.showModal = function(day)
	{
		$scope.modalDate = new Date($scope.currentDate.year, $scope.currentDate.month - 1, day);
		// $scope.modalDate = $filter('date')(currentDate, 'dd.MM.yyyy');

		$scope.modalProductionProducts = [];

		for (product of $scope.productionProducts)
		{
			var newProduct = angular.copy(product);
			newProduct.orders = [];
			newProduct.production = newProduct.productions[day];
			newProduct.productions = [];

			for (order of product.orders)
			{
				if (order.productions[day])
				{
					var newOrder = angular.copy(order);

					newOrder.production = newOrder.productions[day];
					newOrder.baseProduction = newOrder.productions[0];
					newOrder.productions = [];

					newProduct.orders.push(newOrder);
				}
			}

			if (newProduct.orders.length > 0)
			{
				$scope.modalProductionProducts.push(newProduct);
			}
		}

		$scope.isModalShown = true;
	}


	$scope.hideModal = function()
	{
		$scope.isModalShown = false;

		$scope.modalDate = '';
		$scope.modalProductionProducts = [];
	}

	


	$scope.save = function()
	{
		ProductionRepository.save({'products': $scope.modalProductionProducts}, function(response) 
		{
			$scope.successTopAlert = 'Все изменения успешно сохранены!';
			$scope.showTopAlert = true;

			$timeout(function() {
				$scope.showTopAlert = false;
			}, 2000);


			$scope.hideModal();
			$scope.init();
		});
	}



	$scope.getCategoryFacilities = function(categoryId)
	{
		var categoryFacilities = [];
		for (key in $scope.facilities)
		{
			for (category of $scope.facilities[key].categories)
			{
				if (category.id == categoryId)
				{
					categoryFacilities.push($scope.facilities[key]);
					break;
				}
			}
		}

		return categoryFacilities;
	}



	$scope.hoverDay = 0;

	$scope.chooseHoverDay = function(day)
	{
		$scope.hoverDay = day;
	}


	$scope.isAddProductShown = false;
	$scope.newProduct = {};
	

	$scope.showAddProduct = function()
	{
		ProductsRepository.query(function(response) 
		{
			$scope.productGroups = response;
		});

		$scope.isAddProductShown = true;
	}


	$scope.chooseProductGroup = function(productGroup)
	{
		$scope.newProduct.id = null;
		$scope.newProduct.product_group = productGroup;
		$scope.newProduct.category = productGroup.category;

		for (key in $scope.productGroups)
		{
			if ($scope.productGroups[key].id == productGroup.id)
			{
				$scope.newProduct.products = $scope.productGroups[key].products;
				break;
			}
		}

		if (!productGroup.category.hasColors)
		{
			$scope.chooseProduct($scope.newProduct.products[0]);
		}
	}


	$scope.chooseProduct = function(product)
	{
		$scope.newProduct.id = product.id;
		$scope.newProduct.variation = product.variation;
		$scope.newProduct.variation_noun_text = product.variation_noun_text;
	}
	

	$scope.addProduct = function()
	{
		var day = $scope.modalDate.getDate();
		$scope.newProduct.production = {
			'day': day,
			'date': $filter('date')($scope.modalDate, 'yyyy-MM-dd'),
			'product_id': $scope.newProduct.id,
			'order_id': 0,
			'planned': 0,
			'performed': 0
		};
		$scope.modalProductionProducts.push($scope.newProduct);
		$scope.newProduct = {};
		$scope.isAddProductShown = false;
		console.log($scope.modalProductionProducts);
	}


	
	$scope.chosenModalType = 'perform';

	$scope.chosenModalFacility = 0;

	$scope.chooseModalType = function(type)
	{
		$scope.chosenModalType = type;
		$scope.chosenModalFacility = 0;
	}



	$scope.updateProductionPlanned = function(product)
	{
		product.production.planned = 0;
		for (order of product.orders)
		{
			product.production.planned += +order.production.planned;
		}
	}

	$scope.updateProductionPerformed = function(product)
	{
		product.production.performed = 0;
		for (order of product.orders)
		{
			product.production.performed += +order.production.performed;
		}
	}


	$scope.updateOrderProductionsPerformed = function(product)
	{
		performed = product.production.performed;

		for (order of product.orders)
		{
			if (order.production.planned > performed)
			{
				order.production.performed = performed;
			}
			else
			{
				order.production.performed = order.production.planned;
			}
			
			performed -= order.production.performed;
		}

		if (performed > 0)
		{
			if (product.orders[product.orders.length - 1].id == 0)
			{
				product.orders[product.orders.length - 1].production.performed = performed;
			}
			else
			{
				product.orders.push({
					'id': 0,
					'production': {
						'facility_id': product.production.facility_id,
						'order_id': 0,
						'category_id': product.category_id,
						'product_id': product.id,
						'planned': 0,
						'performed': performed
					}
				});
			}
		}
	}

	$scope.updateOrderProductionsFacility = function(product)
	{
		for (order of product.orders)
		{
			order.production.facility_id = product.production.facility_id;
		}
	}
}]);
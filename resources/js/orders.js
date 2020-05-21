angular.module('tctApp').controller('OrdersController', [
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
	'ProductsRepository',
	'OrdersRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$timeout,
		ProductsRepository,
		OrdersRepository
	){

	$scope.Math = window.Math;

	$scope.baseUrl = '';

	$scope.currentStatus = 0;
	$scope.currentOrder = null;

	$scope.orders = [];
	$scope.order = {
		'cost': 0,
		'weight': 0,
		'pallets': 0,
		'priority': 1,
		'products': []
	};
	$scope.id = 0;

	$scope.orderErrors = {};

	$scope.productGroups = [];

	$scope.units = {
		'area': [
			{
				'key': 'unit',
				'name': 'шт.'
			},
			{
				'key': 'units',
				'name': 'м<sup>2</sup>'
			},
			{
				'key': 'pallete',
				'name': 'поддон'
			},
		],
		'volume': [
			{
				'key': 'unit',
				'name': 'шт.'
			},
			{
				'key': 'units',
				'name': 'м<sup>3</sup>'
			},
			{
				'key': 'pallete',
				'name': 'поддон'
			},
		],
		'length': [
			{
				'key': 'units',
				'name': 'шт.'
			},
			{
				'key': 'pallete',
				'name': 'поддон'
			},
		]
	};


	$scope.init = function(status)
	{
		$scope.chooseStatus(status);
		$scope.loadOrders();
	}


	$scope.initShow = function()
	{
		$scope.baseUrl = 'orders';

		$scope.id = $routeParams['id'];

		ProductsRepository.query(function(response) 
		{
			$scope.productGroups = response;
		});

		OrdersRepository.get({id: $scope.id}, function(response) 
		{
			$scope.order = response;
		});
	}


	$scope.initEdit = function()
	{
		$scope.baseUrl = 'orders';

		$scope.id = $routeParams['id'];

		ProductsRepository.query(function(response) 
		{
			$scope.productGroups = response;
		});

		if ($scope.id)
		{
			OrdersRepository.get({id: $scope.id}, function(response) 
			{
				$scope.order = response;

				for (product of $scope.order.products)
				{
					$scope.chooseProductGroup(product, product.product_group_id);
					$scope.chooseProduct(product, product);
				}
			});
		}
	}


	$scope.save = function() 
	{
		OrdersRepository.save({id: $scope.id}, $scope.order, function(response) 
		{
			$scope.orderErrors = {};
			if ($scope.id)
			{
				$scope.successAlert = 'Заказ успешно обновлен!';
			}
			else
			{
				$scope.successAlert = 'Новый заказ успешно создан!';
			}
			$scope.showAlert = true;
			$scope.id = response.id;
			$scope.order.url = response.url;
		}, 
		function(response) 
		{
            $scope.orderErrors = response.data.errors;
        });
	}


	$scope.delete = function(id)
	{
		OrdersRepository.delete({id: id}, function(response) 
		{
			$scope.init();
		}, 
		function(response) 
		{
           
        });
	}


	$scope.loadOrders = function()
	{
		OrdersRepository.query({'status': $scope.currentStatus}, function(response) 
		{
			$scope.orders = response;
		});
	}


	$scope.chooseStatus = function(status)
	{
		$scope.currentStatus = status;

		$scope.loadOrders();
	}


	$scope.chooseOrder = function(order)
	{
		console.log(order);
		if ($scope.currentOrder && $scope.currentOrder.id == order.id)
		{
			$scope.currentOrder = null;
		}
		else
		{
			$scope.currentOrder = order;
		}
	}


	$scope.addProduct = function()
	{
		$scope.order.products.push({
			'id': null,
			'products': [],
			'pivot': {
				'price': 0,
				'count': 0,
				'cost': 0
			}
		});
	}


	$scope.deleteProduct = function(index)
	{
		$scope.order.products.splice(index, 1);

		$scope.updateOrderInfo();
	}


	$scope.chooseProductGroup = function(productData, productGroup)
	{
		productData.id = null;
		productData.category = productGroup.category;
		productData.weight_units = productGroup.weight_units;
		productData.units_in_pallete = productGroup.units_in_pallete;

		for (key in $scope.productGroups)
		{
			if ($scope.productGroups[key].id == productGroup.id)
			{
				productData.products = $scope.productGroups[key].products;
				break;
			}
		}

		if (!productGroup.category.variations)
		{
			$scope.chooseProduct(productData, productData.products[0]);
		}

		$scope.updateOrderInfo();
	}


	$scope.chooseProduct = function(productData, product)
	{
		productData.id = product.id;
		productData.in_stock = product.in_stock;
		productData.free_in_stock = product.free_in_stock;
		productData.pivot.price = product.price;
		productData.pivot.cost = product.price * productData.pivot.count;

		$scope.updateOrderInfo();
	}


	$scope.changeCount = function(productData, count) 
	{
		productData.pivot.count = parseInt(productData.pivot.count) + count;

		if ((isNaN(productData.pivot.count)) || (productData.pivot.count < 0)) 
		{
			productData.pivot.count = 0;
		}

		productData.pivot.cost = productData.pivot.price * productData.pivot.count;

		$scope.updateOrderInfo();
    }


	$scope.updateOrderInfo = function() 
	{
		$scope.order.cost = 0;
		$scope.order.weight = 0;
		$scope.order.pallets = 0;

		for (product of $scope.order.products) 
		{
			$scope.order.cost += product.pivot.cost;
			$scope.order.weight += product.weight_units * product.pivot.count;
			$scope.order.pallets += Math.ceil(product.pivot.count / product.units_in_pallete); 
		}
    }


    $scope.isRealizationModalShown = false;
    $scope.modalOrder = {};
    $scope.isAllRealizationsChosen = false;


    $scope.showRealizationModal = function(order)
    {
    	$scope.modalOrder = order || $scope.order;
    	$scope.isRealizationModalShown = true;

    	$scope.isAllRealizationsChosen = false;
    }


    $scope.hideRealizationModal = function()
    {
    	$scope.isRealizationModalShown = false;
    }


    $scope.chooseAllRealizations = function()
    {
    	if ($scope.isAllRealizationsChosen)
    	{
    		for (realization of $scope.modalOrder.realizations)
    		{
    			realization.performed = realization.planned;
    		}
    	}
    }

    $scope.checkAllRealizations = function(realization) 
    {
    	if (realization.performed > realization.planned)
		{
			realization.performed = realization.planned
		}

    	for (realization of $scope.modalOrder.realizations)
		{
			if (realization.performed < realization.planned)
			{
				$scope.isAllRealizationsChosen = false;
				return;
			}
		}
		$scope.isAllRealizationsChosen = true;
    }


    $scope.saveRealization = function()
    {
    	OrdersRepository.saveRealization({'realizations': $scope.modalOrder.realizations}, function(response) 
		{
			$scope.successTopAlert = 'Все изменения успешно сохранены!';
			$scope.showTopAlert = true;

			$timeout(function() {
				$scope.showTopAlert = false;
			}, 2000);


			if (!$scope.baseUrl)
			{
				$scope.init();
			}


			$scope.hideRealizationModal();
		});
    }
}]);
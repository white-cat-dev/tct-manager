angular.module('tctApp').controller('OrdersController', [
	'$scope',
	'$routeParams',
	'$location',
	'ProductsRepository',
	'OrdersRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		ProductsRepository,
		OrdersRepository
	){

	$scope.Math = window.Math;

	$scope.baseUrl = '';

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

	// $scope.units = {
	// 	'area': [
	// 		{
	// 			'key': 'unit',
	// 			'name': 'шт.'
	// 		},
	// 		{
	// 			'key': 'units',
	// 			'name': 'м<sup>2</sup>'
	// 		},
	// 		{
	// 			'key': 'pallete',
	// 			'name': 'поддон'
	// 		},
	// 	],
	// 	'volume': [
	// 		{
	// 			'key': 'unit',
	// 			'name': 'шт.'
	// 		},
	// 		{
	// 			'key': 'units',
	// 			'name': 'м<sup>3</sup>'
	// 		},
	// 		{
	// 			'key': 'pallete',
	// 			'name': 'поддон'
	// 		},
	// 	],
	// 	'length': [
	// 		{
	// 			'key': 'unit',
	// 			'name': 'шт.'
	// 		},
	// 		{
	// 			'key': 'units',
	// 			'name': 'м'
	// 		},
	// 		{
	// 			'key': 'pallete',
	// 			'name': 'поддон'
	// 		},
	// 	]
	// };


	$scope.init = function()
	{
		OrdersRepository.query(function(response) 
		{
			$scope.orders = response;
		});
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


	$scope.addProduct = function()
	{
		$scope.order.products.push({
			'products': [],
			'id': null,
			'pivot': {
				'price': 0,
				'count': 1,
				'cost': 0
			}
		});
	}


	$scope.deleteProduct = function(index)
	{
		$scope.order.products.splice(index, 1);

		$scope.updateOrderInfo();
	}


	$scope.chooseProductGroup = function(productData, productGroupId)
	{
		productData.id = null;
		productData.pivot.cost = 0;
		productData.products = [];

		for (key in $scope.productGroups)
		{
			if ($scope.productGroups[key].id == productGroupId)
			{
				productData.products = $scope.productGroups[key].products;
				break;
			}
		}
	}

	$scope.chooseProduct = function(productData, product)
	{
		productData.id = product.id;
		productData.price = product.price;
		productData.in_stock = product.in_stock;
		productData.category = product.category;

		productData.pivot.price = product.price;
		productData.pivot.cost = productData.price * productData.pivot.count;

		$scope.updateOrderInfo();
	}


	$scope.changeCount = function(productData, count) 
	{
		productData.pivot.count = parseInt(productData.pivot.count) + count;

		if ((isNaN(productData.pivot.count)) || (productData.pivot.count < 0)) 
		{
			productData.pivot.count = 0;
		}

		productData.pivot.cost = productData.price * productData.pivot.count;

		$scope.updateOrderInfo();
    }


	$scope.updateOrderInfo = function() 
	{
		$scope.order.cost = 0;

		for (key in $scope.order.products) 
		{
			$scope.order.cost += $scope.order.products[key].pivot.cost;
		}
    }
}]);
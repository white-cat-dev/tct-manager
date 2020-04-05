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

	$scope.orders = [];
	$scope.order = {};
	$scope.id = 0;

	$scope.orderData = {
		'products': []
	};
	$scope.orderErrors = {};

	$scope.productGroups = [];


	$scope.init = function()
	{
		OrdersRepository.query(function(response) 
		{
			$scope.orders = response;
		});
	}


	$scope.initShow = function()
	{
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
		$scope.id = $routeParams['id'];

		ProductsRepository.query(function(response) 
		{
			$scope.productGroups = response;
			console.log($scope.productGroups);
		});

		if ($scope.id)
		{
			OrdersRepository.get({id: $scope.id}, function(response) 
			{
				$scope.order = response;
				$scope.orderData = response;
			});
		}
	}


	$scope.save = function(url) 
	{
		OrdersRepository.save({id: $scope.id}, $scope.orderData, function(response) 
		{
			$location.url(url);
            $location.replace();
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
		$scope.orderData.products.push({
			'products': [],
			'id': undefined,
			'pivot': {
				'price': 0,
				'count': 1,
				'cost': 0
			}
		});
	}


	$scope.deleteProduct = function(index)
	{
		$scope.orderData.products.splice(index, 1);

		$scope.updateOrderInfo();
	}


	$scope.chooseProductGroup = function(productData, productGroupId)
	{
		productData.id = undefined;
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
		productData.pivot.cost = productData.price * productData.pivot.count;

		console.log(product);

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
		$scope.orderData.cost = 0;

		for (key in $scope.orderData.products) 
		{
			$scope.orderData.cost += $scope.orderData.products[key].pivot.cost;
		}
    }
}]);
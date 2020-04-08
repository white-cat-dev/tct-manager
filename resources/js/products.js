angular.module('tctApp').controller('ProductsController', [
	'$scope',
	'$routeParams',
	'$location',
	'CategoriesRepository',
	'ProductsRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		CategoriesRepository,
		ProductsRepository
	){

	$scope.productGroups = [];
	$scope.productGroup = {};
	$scope.id = 0;

	$scope.productGroupData = {
		'products': []
	};
	$scope.productGroupErrors = {};

	$scope.categories = [];
	$scope.currentCategory = 0;


	$scope.init = function()
	{
		if ($location.search().category)
		{
			$scope.currentCategory = $location.search().category;
		}
		
		$scope.loadCategories();
		$scope.loadProducts();
	}


	$scope.loadCategories = function()
	{
		CategoriesRepository.query(function(response) 
		{
			$scope.categories = response;
		});
	}


	$scope.loadProducts = function()
	{
		ProductsRepository.query({'category': $scope.currentCategory}, function(response) 
		{
			$scope.productGroups = response;
		});
	}

	$scope.chooseCategory = function(category)
	{
		$scope.currentCategory = category;
		$scope.loadProducts();
	}


	$scope.initShow = function()
	{
		$scope.id = $routeParams['id'];
		ProductsRepository.get({id: $scope.id}, function(response) 
		{
			$scope.productGroup = response;
		});
	}


	$scope.initEdit = function()
	{
		$scope.id = $routeParams['id'];

		$scope.loadCategories();

		if ($scope.id)
		{
			ProductsRepository.get({id: $scope.id}, function(response) 
			{
				$scope.productGroup = response;
				$scope.productGroupData = response;
			});
		}
	}


	$scope.save = function(url) 
	{
		console.log($scope.productGroupData);

		ProductsRepository.save({id: $scope.id}, $scope.productGroupData, function(response) 
		{
			$location.url(url);
            $location.replace();
		}, 
		function(response) 
		{
            $scope.productGroupErrors = response.data.errors;
        });
	}


	$scope.delete = function(id)
	{
		ProductsRepository.delete({id: id}, function(response) 
		{
			$scope.init();
		}, 
		function(response) 
		{
           
        });
	}


	$scope.addProduct = function()
	{
		$scope.productGroupData['products'].push({
			'color': 'red',
			'price': 123,
			'price_unit': 0,
			'price_pallete': 0,
			'in_stock': 0
		});
		console.log($scope.productGroupData['products']);
	}
}]);
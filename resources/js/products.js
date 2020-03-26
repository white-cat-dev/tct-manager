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
	$scope.categoryId = 0;


	$scope.init = function()
	{
		CategoriesRepository.query(function(response) 
		{
			$scope.categories = response;
			if ($scope.categories.length > 0)
			{
				$scope.categoryId = $scope.categories[0].id;
			}
		});

		ProductsRepository.query(function(response) 
		{
			$scope.productGroups = response;
		});
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
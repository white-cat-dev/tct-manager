angular.module('tctApp').controller('CategoriesController', [
	'$scope',
	'$routeParams',
	'$location',
	'CategoriesRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		CategoriesRepository
	){

	$scope.categories = [];
	$scope.category = {};
	$scope.id = 0;

	$scope.categoryData = {};
	$scope.categoryErrors = {};


	$scope.init = function()
	{
		CategoriesRepository.query(function(response) 
		{
			$scope.categories = response;
		});
	}


	$scope.initShow = function()
	{
		$scope.id = $routeParams['id'];
		CategoriesRepository.get({id: $scope.id}, function(response) 
		{
			$scope.category = response;
		});
	}


	$scope.initEdit = function()
	{
		$scope.id = $routeParams['id'];

		if ($scope.id)
		{
			CategoriesRepository.get({id: $scope.id}, function(response) 
			{
				$scope.category = response;
				$scope.categoryData = response;
			});
		}
	}


	$scope.save = function(url) 
	{
		CategoriesRepository.save({id: $scope.id}, $scope.categoryData, function(response) 
		{
			$location.url(url);
            $location.replace();
		}, 
		function(response) 
		{
            $scope.categoryErrors = response.data.errors;
        });
	}


	$scope.delete = function(id)
	{
		CategoriesRepository.delete({id: id}, function(response) 
		{
			$scope.init();
		}, 
		function(response) 
		{
           
        });
	}
}]);
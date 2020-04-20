angular.module('tctApp').controller('CategoriesController', [
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
	'CategoriesRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$timeout,
		CategoriesRepository
	){

	$scope.baseUrl = '';

	$scope.categories = [];
	$scope.category = {};
	$scope.id = 0;

	$scope.categoryErrors = {};

	$scope.units = [
		{
			'key': 'area',
			'name': 'Площадь (м<sup>2</sup>/шт./поддон)'
		},
		{
			'key': 'volume',
			'name': 'Объём (м<sup>3</sup>/шт./поддон)'
		},
		{
			'key': 'unit',
			'name': 'Поштучно (шт.)'
		}
	];


	$scope.init = function()
	{
		CategoriesRepository.query(function(response) 
		{
			$scope.categories = response;
		});
	}


	$scope.initShow = function()
	{
		$scope.baseUrl = 'categories';

		$scope.id = $routeParams['id'];

		CategoriesRepository.get({id: $scope.id}, function(response) 
		{
			$scope.category = response;
		});
	}


	$scope.initEdit = function()
	{
		$scope.baseUrl = 'categories';

		$scope.id = $routeParams['id'];

		if ($scope.id)
		{
			CategoriesRepository.get({id: $scope.id}, function(response) 
			{
				$scope.category = response;
			});
		}
	}


	$scope.save = function() 
	{
		CategoriesRepository.save({id: $scope.id}, $scope.category, function(response) 
		{
			$scope.categoryErrors = {};
			if ($scope.id)
			{
				$scope.successAlert = 'Категория успешно обновлена!';
			}
			else
			{
				$scope.successAlert = 'Новая категория успешно создана!';
			}
			$scope.showAlert = true;
			$scope.id = response.id;
			$scope.category.url = response.url;
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
			if ($scope.baseUrl)
			{
				$location.path($scope.baseUrl).replace();
			}
			else
			{
				$scope.successAlert = 'Категория успешно удалена!';
				$scope.showAlert = true;

				$timeout(function() {
					$scope.showAlert = false;
				}, 2000);

				$scope.init();
			}
		}, 
		function(response) 
		{
           
        });
	}
}]);
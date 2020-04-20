angular.module('tctApp').controller('WorkersController', [
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
	'FacilitiesRepository',
	'WorkersRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$timeout,
		FacilitiesRepository,
		WorkersRepository
	){

	$scope.Object = Object;

	$scope.baseUrl = '';

	$scope.worker = {};

	$scope.facilities = [];
	$scope.workers = [];


	$scope.init = function()
	{
		WorkersRepository.query(function(response)
		{
			$scope.workers = response;
		});
	}


	$scope.initShow = function()
	{
		$scope.baseUrl = 'workers';

		$scope.id = $routeParams['id'];

		WorkersRepository.get({id: $scope.id}, function(response) 
		{
			$scope.worker = response;
		});
	}


	$scope.initEdit = function()
	{
		$scope.baseUrl = 'workers';

		$scope.id = $routeParams['id'];

		if ($scope.id)
		{
			WorkersRepository.get({id: $scope.id}, function(response) 
			{
				$scope.worker = response;
			});
		}

		FacilitiesRepository.query(function(response) 
		{
			$scope.facilities = response;
		});
	}


	$scope.save = function() 
	{
		WorkersRepository.save({id: $scope.id}, $scope.worker, function(response) 
		{
			$scope.workerErrors = {};
			if ($scope.id)
			{
				$scope.successAlert = 'Данные работника успешно сохранены!';
			}
			else
			{
				$scope.successAlert = 'Новый работник успешно создан!';
			}
			$scope.id = response.id;
			$scope.worker.url = response.url;
		}, 
		function(response) 
		{
            $scope.workerErrors = response.data.errors;
        });
	}


	$scope.delete = function()
	{

	}
}]);
angular.module('tctApp').controller('ClientsController', [
	'$scope',
	'$routeParams',
	'$location',
	'ClientsRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		ClientsRepository
	){

	$scope.clients = [];
	$scope.client = {};
	$scope.id = 0;

	$scope.clientData = {};
	$scope.clientErrors = {};


	$scope.init = function()
	{
		ClientsRepository.query(function(response) 
		{
			$scope.clients = response;
		});
	}


	$scope.initShow = function()
	{
		$scope.id = $routeParams['id'];
		ClientsRepository.get({id: $scope.id}, function(response) 
		{
			$scope.client = response;
		});
	}


	$scope.initEdit = function()
	{
		$scope.id = $routeParams['id'];

		if ($scope.id)
		{
			ClientsRepository.get({id: $scope.id}, function(response) 
			{
				$scope.client = response;
				$scope.clientData = response;
			});
		}
	}


	$scope.save = function(url) 
	{
		ClientsRepository.save({id: $scope.id}, $scope.clientData, function(response) 
		{
			$location.url(url);
            $location.replace();
		}, 
		function(response) 
		{
            $scope.clientErrors = response.data.errors;
        });
	}


	$scope.delete = function(id)
	{
		ClientsRepository.delete({id: id}, function(response) 
		{
			$scope.init();
		}, 
		function(response) 
		{
           
        });
	}
}]);
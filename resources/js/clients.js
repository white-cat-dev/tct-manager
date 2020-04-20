angular.module('tctApp').controller('ClientsController', [
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
	'ClientsRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$timeout,
		ClientsRepository
	){

	$scope.baseUrl = '';

	$scope.clients = [];
	$scope.client = {};
	$scope.id = 0;

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
		$scope.baseUrl = 'clients';

		$scope.id = $routeParams['id'];

		ClientsRepository.get({id: $scope.id}, function(response) 
		{
			$scope.client = response;
		});
	}


	$scope.initEdit = function()
	{
		$scope.baseUrl = 'clients';

		$scope.id = $routeParams['id'];

		if ($scope.id)
		{
			ClientsRepository.get({id: $scope.id}, function(response) 
			{
				$scope.client = response;
			});
		}
	}


	$scope.save = function() 
	{
		ClientsRepository.save({id: $scope.id}, $scope.client, function(response) 
		{
			$scope.clientErrors = {};
			if ($scope.id)
			{
				$scope.successAlert = 'Данные клиента успешно обновлены!';
			}
			else
			{
				$scope.successAlert = 'Новый клиент успешно создан!';
			}
			$scope.showAlert = true;
			$scope.id = response.id;
			$scope.client.url = response.url;
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
			if ($scope.baseUrl)
			{
				$location.path($scope.baseUrl).replace();
			}
			else
			{
				$scope.successAlert = 'Клиент успешно удален!';
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
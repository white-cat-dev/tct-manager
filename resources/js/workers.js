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


	$scope.isStatusModalShown = false;
	$scope.modalStatusErrors = {};
	$scope.modalWorker = {};


	$scope.showStatusModal = function(worker, status)
	{
		$scope.worker = worker || $scope.worker;
		$scope.modalWorker = angular.copy($scope.worker);
		$scope.modalWorker.status = status;
		
		if ($scope.worker.status == $scope.modalWorker.status)
		{
			$scope.saveStatus();
			return;
		}

		$scope.isStatusModalShown = true;
	}


	$scope.hideStatusModal = function()
	{
		$scope.isStatusModalShown = false;

		$scope.modalStatusErrors = {};
	}



	// $scope.updateStatusNow = function()
	// {
	// 	$scope.worker.status = ($scope.worker.status + 2) % 4;
	// 	$scope.worker.status_date_raw = new Date();
	// }


	$scope.saveStatus = function()
	{
		if ($scope.worker.status == $scope.modalWorker.status)
		{
			$scope.modalWorker.status_date_raw = null;
			$scope.modalWorker.status_date_next_raw = null;
		}
		else if ($scope.modalWorker.status_date_raw)
		{
			$scope.modalWorker.status = ($scope.modalWorker.status + 1) % 2;
		}
		
		WorkersRepository.save({id: $scope.modalWorker.id}, $scope.modalWorker, function(response) 
		{
			$scope.modalStatusErrors = {};
			$scope.successTopAlert = 'Изменения успешно сохранены!';
			$scope.showTopAlert = true;

			$timeout(function() {
				$scope.showTopAlert = false;
			}, 2000);

			if (!$scope.baseUrl)
			{
				$scope.init();
			}
			else
			{
				$scope.worker = $scope.modalWorker;
			}

			$scope.hideStatusModal();
		}, 
		function(response) 
		{
            $scope.modalStatusErrors = response.data.errors;
        });
	}
}]);
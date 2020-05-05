angular.module('tctApp').controller('FacilitiesController', [
	'$scope',
	'$routeParams',
	'$location',
	'$filter',
	'$timeout',
	'FacilitiesRepository',
	'CategoriesRepository',
	'WorkersRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$filter,
		$timeout,
		FacilitiesRepository,
		CategoriesRepository,
		WorkersRepository
	){

	$scope.baseUrl = '';

	$scope.facilities = [];

	$scope.facility = {
		'workers': []
	};
	$scope.id = 0;

	$scope.facilityErrors = {};

	$scope.categories = [];
	$scope.workers = [];


	$scope.init = function()
	{
		FacilitiesRepository.query(function(response) 
		{
			console.log(response);
			$scope.facilities = response;
			for (var facility of $scope.facilities) 
			{
				facility.status_date_raw = facility.status_date ? new Date(Date.parse(facility.status_date.replace(/-/, '/'))) : null;
			}
			console.log($scope.facilities);
		});
	}


	$scope.initShow = function()
	{
		$scope.baseUrl = 'facilities';

		$scope.id = $routeParams['id'];

		FacilitiesRepository.get({id: $scope.id}, function(response) 
		{
			$scope.facility = response;
			$scope.facility.status_date_raw = $scope.facility.status_date ? new Date(Date.parse($scope.facility.status_date.replace(/-/, '/'))) : null;
		});
	}


	$scope.initEdit = function()
	{
		$scope.baseUrl = 'facilities';

		$scope.id = $routeParams['id'];

		if ($scope.id)
		{
			FacilitiesRepository.get({id: $scope.id}, function(response) 
			{
				$scope.facility = response;
				$scope.facility.status_date_raw = $scope.facility.status_date ? new Date(Date.parse($scope.facility.status_date.replace(/-/, '/'))) : null;

				var categories = $scope.facility.categories;
				$scope.facility.categories = {};
				for (var category of categories)
				{
					$scope.facility.categories[category.id] = true;
				}
			});
		}

		CategoriesRepository.query(function(response) 
		{
			$scope.categories = response;
		});

		WorkersRepository.query(function(response) 
		{
			$scope.workers = response;

			for (var i = 0; i < $scope.workers.length; i++)
			{
				if (($scope.workers[i].facility_id > 0) && 
					($scope.workers[i].facility_id == $scope.id))
				{
					$scope.workers.splice(i, 1);
				}
			}
		});
	}


	$scope.save = function(facility) 
	{
		facility = facility || $scope.facility;

		FacilitiesRepository.save({id: facility.id}, facility, function(response) 
		{
			$scope.facilityErrors = {};
			if ($scope.id)
			{
				$scope.successAlert = 'Данные цеха успешно сохранены!';
			}
			else
			{
				$scope.successAlert = 'Новый цех успешно создан!';
			}
			$scope.showAlert = true;
			$scope.id = response.id;
			$scope.facility.url = response.url;
		}, 
		function(response) 
		{
            $scope.facilityErrors = response.data.errors;
        });
	}


	$scope.delete = function(id)
	{
		FacilitiesRepository.delete({id: id}, function(response) 
		{
			$scope.init();
		}, 
		function(response) 
		{
           
        });
	}



	$scope.isAddWorkerShown = false;
	$scope.newWorker = {};
	

	$scope.showAddWorker = function()
	{
		$scope.isAddWorkerShown = true;
	}
	

	$scope.addWorker = function()
	{
		var index = $scope.workers.indexOf($scope.newWorker.data);
		$scope.facility.workers.push($scope.newWorker.data);
		$scope.workers.splice(index, 1);
		$scope.newWorker.data = null;
		$scope.isAddWorkerShown = false;
	}


	$scope.deleteWorker = function(index)
	{
		var worker = $scope.facility.workers[index];
		$scope.facility.workers.splice(index, 1);
		$scope.workers.push(worker);
	}



	$scope.isStatusModalShown = false;
	$scope.modalStatusErrors = {};
	$scope.modalFacility = {};


	$scope.showStatusModal = function(facility, status)
	{
		$scope.facility = facility || $scope.facility;
		$scope.modalFacility = angular.copy($scope.facility);
		$scope.modalFacility.status = status;
		
		if ($scope.facility.status == $scope.modalFacility.status)
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
	// 	$scope.facility.status = ($scope.facility.status + 2) % 4;
	// 	$scope.facility.status_date_raw = new Date();
	// }


	$scope.saveStatus = function()
	{
		if ($scope.facility.status == $scope.modalFacility.status)
		{
			$scope.modalFacility.status_date_raw = null;
		}
		else if ($scope.modalFacility.status_date_raw)
		{
			$scope.modalFacility.status = ($scope.modalFacility.status + 1) % 2;
		}

		FacilitiesRepository.save({id: $scope.modalFacility.id}, $scope.modalFacility, function(response) 
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
				$scope.facility = $scope.modalFacility;
			}

			$scope.hideStatusModal();
		}, 
		function(response) 
		{
            $scope.modalStatusErrors = response.data.errors;
        });
	}
}]);
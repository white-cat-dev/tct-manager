angular.module('tctApp').controller('FacilitiesController', [
	'$scope',
	'$routeParams',
	'$location',
	'FacilitiesRepository',
	'CategoriesRepository',
	'WorkersRepository',
	function(
		$scope, 
		$routeParams,
		$location,
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
			$scope.facilities = response;
		});
	}


	$scope.initShow = function()
	{
		$scope.baseUrl = 'facilities';

		$scope.id = $routeParams['id'];
		$scope.loadFacility()
	}


	$scope.initEdit = function()
	{
		$scope.baseUrl = 'facilities';

		$scope.id = $routeParams['id'];

		if ($scope.id)
		{
			$scope.loadFacility()
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


	$scope.loadFacility = function()
	{
		FacilitiesRepository.get({id: $scope.id}, function(response) 
		{
			$scope.facility = response;

			var categories = $scope.facility.categories;
			$scope.facility.categories = {};
			for (var category of categories)
			{
				$scope.facility.categories[category.id] = true;
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
}]);